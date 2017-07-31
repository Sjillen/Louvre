<?php

namespace Louvre\BookingBundle\Controller;

use Louvre\BookingBundle\Entity\Booking;
use Louvre\BookingBundle\Repository\BookingRepository;
use Louvre\BookingBundle\Entity\Ticket;
use Louvre\BookingBundle\Entity\Billet;
use Louvre\BookingBundle\Form\Type\BookingType;
use Louvre\BookingBundle\Form\Type\BilletType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Validator\Constraints\DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class BookingController extends Controller
{
	

	public function indexAction() {

		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Accueil", $this->get("router")->generate('louvre_booking_home'));
		return $this->render('LouvreBookingBundle:Booking:index.html.twig');
	}


	public function bookingAction(Request $request)
	{
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Accueil", $this->get("router")->generate('louvre_booking_home'))
					->addItem("Réservation", $this->get("router")->generate('louvre_booking_booking'));
		
		date_default_timezone_set('Europe/Paris');

		$session = $request->getSession();

		//services
		$dateChecker = $this->container->get('louvre_booking.dateChecker');
		$typeChecker = $this->container->get('louvre_booking.typeChecker');

		$booking = new Booking();
		$form = $this->createForm(BookingType::class, $booking);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			
			$date = $booking->getDate();
			
			//Check if selected date is not a day off
			$dayOff = $dateChecker->checkDayOff($date);
			if ($dayOff)
			{
				$request->getSession()->getFlashBag()->add('warning', 'Le musée est fermé les dimanches et les mardis, ainsi que les 1er mai, 1er Novembre et 25 Décembre. Merci de sélectionner une autre date.');

				return $this->redirectToRoute('louvre_booking_booking');
			}

			//Check if selected date is valid (not passed)
			$invalidDate = $dateChecker->checkValidDate($date);
			
			if ($invalidDate) {
				$request->getSession()->getFlashBag()->add('warning', 'Désolé les réservations ne sont plus disponibles pour cette date !');
				return $this->redirectToRoute('louvre_booking_booking');
			}


			$type = $booking->getType();
			//Check that bookingType is not set to Journee if it is passed 2pm today
			$errorType = $typeChecker->checkType($date, $type);

			if ($errorType)
			{
				$request->getSession()->getFlashBag()->add('warning', 'Seuls les tickets Demi-journée sont disponibles après 14h00');

				return $this->redirectToRoute('louvre_booking_booking');
			}

			

			//Checking the amount of tickets sold for the chosen date
			$ticketsSold = $this->getDoctrine()->getManager()->getRepository('LouvreBookingBundle:Booking')->ticketsSold($booking->getDate());
			$ticketsLeft = 1000 - $ticketsSold;

			//If the chosen amount is exceeding
			if ($ticketsLeft < $booking->getNbTickets()) {
				$request->getSession()->getFlashBag()->add('warning', 'Désolé, le nombre maximum de tickets réservés a été atteint pour ce jour. Nombre de place restantes pour cette date: '. $ticketsLeft . ' tickets.' );

				return $this->redirectToRoute('louvre_booking_booking');
			}

			// Saving the object in session
			$session->set('booking', $booking);
			return $this->redirectToRoute('louvre_booking_ticket');

		}

		return $this->render('LouvreBookingBundle:Booking:booking.html.twig', array(
			'form' => $form->createView(),
			));
	}

	public function ticketAction(Request $request)
	{
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Accueil", $this->get("router")->generate('louvre_booking_home'))
					->addItem("Réservation", $this->get("router")->generate('louvre_booking_booking'))
					->addItem("Tickets", $this->get("router")->generate('louvre_booking_ticket'));

		$billet = new Billet();
		$booking = $request->getSession()->get('booking');

		

		$nbTickets = $booking->getNbTickets();
		$type = $booking->getType();
		$reference = $booking->getReference();

		

		$tickets = array();
		for ($i = 1; $i <= $nbTickets; $i++)
		{		
			$tickets[$i] = new Ticket();
			$tickets[$i]->setReference($reference);

			$billet->getTickets()->add($tickets[$i]);
		}

		$form = $this->createForm(BilletType::class, $billet);
		

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			//Checking the amount of tickets sold for the chosen date
			$ticketsSold = $this->getDoctrine()->getManager()->getRepository('LouvreBookingBundle:Booking')->ticketsSold($booking->getDate());
			$ticketsLeft = 1000 - $ticketsSold;

			//If the chosen amount is exceeding
			if ($ticketsLeft < $booking->getNbTickets()) {
				$request->getSession()->getFlashBag()->add('danger', 'Désolé, le nombre maximum de tickets réservés a été atteint pour ce jour. Nombre de place restantes pour cette date: '. $ticketsLeft . ' tickets.' );

				return $this->redirectToRoute('louvre_booking_booking');
			}


			$request->getSession()->set('tickets', $tickets);
			$tickets = $request->getSession()->get('tickets');
			$prices = [];
			//Service that will set price accordingly to specified options
			$priceChecker = $this->container->get('louvre_booking.priceChecker');

			for ($i = 1; $i <= $nbTickets; $i++)
			{
      			$birthdate = $tickets[$i]->getAge();
      			$discount = $tickets[$i]->getDiscount();
      			$price = $priceChecker->checkPrice($birthdate, $type, $discount);
				$tickets[$i]->setPrice($price);
				$prices[$i] = $price;		
			}

			//Check that the amount is not equal to 0
			$amount = $priceChecker->checkAmount($prices);
			if (!$amount)// if amount = 0, error
			{
				$request->getSession()->getFlashBag()->add('danger', 'Erreur, les enfants de moins de 3ans doivent être accompagnés.');

				return $this->redirectToRoute('louvre_booking_ticket');
			}else // if amount > 0, save data in session 
			{
				$request->getSession()->set('amount', $amount);
			}


			return $this->redirectToRoute('louvre_booking_review');

			

		}
		return $this->render('LouvreBookingBundle:Booking:ticket.html.twig', array(
			'form' => $form->createView(),
			'booking' => $booking,
						
			));
	}

	public function recapAction(Request $request)
	{
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Accueil", $this->get("router")->generate('louvre_booking_home'))
					->addItem("Réservation", $this->get("router")->generate('louvre_booking_booking'))
					->addItem("Tickets", $this->get("router")->generate('louvre_booking_ticket'))
					->addItem("Récapitulatif", $this->get("router")->generate('louvre_booking_review'));

		$session = $request->getSession();

		$booking = $session->get('booking');
		$tickets = $session->get('tickets');
		$amount = $session->get('amount');
		$amountStripe = $amount*100;
		$session->set('amountStripe', $amountStripe);
		
		return $this->render('LouvreBookingBundle:Booking:recap.html.twig', array(
			'booking' => $booking,
			'tickets' => $tickets,
			'amount' => $amount,
			'amountStripe' => $amountStripe
			
		));
	}


	public function confirmAction(Request $request)
	{
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Accueil", $this->get("router")->generate('louvre_booking_home'))
					->addItem("Réservation", $this->get("router")->generate('louvre_booking_booking'))
					->addItem("Tickets", $this->get("router")->generate('louvre_booking_ticket'))
					->addItem("Récapitulatif", $this->get("router")->generate('louvre_booking_review'))
					->addItem("Confirmé", $this->get("router")->generate('louvre_booking_confirm'));


		$session = $request->getSession();
		$booking = $session->get('booking');
		$tickets = $session->get('tickets');
		$amountStripe = $session->get('amountStripe');
		$amount = $session->get('amount');
		
		
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey("sk_test_rv7QCc1V2Pk38fdU0wT2dUrT");

		// Token is created using Stripe.js or Checkout!
		// Get the payment token submitted by the form:
		$token = $request->get('stripeToken');

		// Charge the user's card:
		$charge = \Stripe\Charge::create(array(
		  "amount" => $amountStripe,
		  "currency" => "eur",
		  "description" => "e-billet",
		  "source" => $token,
		));
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($booking);
		foreach($tickets as $ticket)
		{
			$em->persist($ticket);
		}
		$em->flush();
		
		
		$recipient = $booking->getEmail();
		// Email to be sent once payment process is finished
		$from = new \SendGrid\Email("Louvre e-billet", "e-billet@louvre.fr");
		$subject = "Votre réservation" .$booking->getReference();
		$to = new \SendGrid\Email($booking->getFirstName()." ". $booking->getLastName(), $recipient);
		$content = new \SendGrid\Content("text/html", $this->render('LouvreBookingBundle:Emails:order.html.twig',array(
			'booking'=>$booking,
			'tickets' => $tickets,
			'amount' => $amount
			)) );
		$mail = new \SendGrid\Mail($from, $subject, $to, $content);
		$apiKey = '';
		$sg = new \SendGrid($apiKey);
		$response = $sg->client->mail()->send()->post($mail);
		
		
		
		
		$request->getSession()->getFlashBag()->add('success','Succès ! Tickets envoyés par email à <strong>'. $booking->getEmail(). ' </strong>!');

		return $this->render('LouvreBookingBundle:Booking:confirm.html.twig',array(
			'booking' => $booking,
			'tickets' => $tickets,
			'amount' => $amount,
			));
	}
}