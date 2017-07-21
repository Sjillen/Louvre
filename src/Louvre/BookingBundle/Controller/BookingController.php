<?php

namespace Louvre\BookingBundle\Controller;

use Louvre\BookingBundle\Entity\Booking;
use Louvre\BookingBundle\Repository\BookingRepository;
use Louvre\BookingBundle\Entity\Ticket;
use Louvre\BookingBundle\Entity\Billet;
use Louvre\BookingBundle\Form\BookingType;
use Louvre\BookingBundle\Form\BilletType;

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

		$booking = new Booking();

		$form = $this->createForm(BookingType::class, $booking);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			
			$now = new \DateTime('now');
			$date = $booking->getDate();
			$day = (int) date_diff($now, $date)->format("%a");
			$hour = (int) $now->format('h');
			// if the date is today && after 14:00 && the ticket type chosen is Journée
			if ($day === 0 && $hour >= 14 && $booking->getType() === 'Journée' )
			{
				$request->getSession()->getFlashBag()->add('warning', 'Seuls les tickets Demi-journée sont disponibles après 14h00');

				return $this->redirectToRoute('louvre_booking_booking');
			}

			if ($day < 0 || ($day === 0 && $hour >= 19)) {
				$request->getSession()->getFlashBag()->add('warning', 'Impossible de réserver pour une date antérieure !');
			}

			//Checking the amount of tickets sold for the chosen date
			$ticketsSold = $this->getDoctrine()->getManager()->getRepository('LouvreBookingBundle:Booking')->ticketsSold($booking->getDate());
			$ticketsLeft = 1000 - $ticketsSold;

			//If the chosen amount is exceeding
			if ($ticketsLeft < $booking->getNbTickets()) {
				$request->getSession()->getFlashBag()->add('error', 'Désolé, le nombre maximum de tickets réservés a été atteint pour ce jour. Nombre de place restantes pour cette date: '. $ticketsLeft . ' tickets.' );

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

			$request->getSession()->set('tickets', $tickets);
			$tickets = $request->getSession()->get('tickets');
			

			$date1 = new \Datetime('now');

			for ($i = 1; $i <= $nbTickets; $i++)
			{
      			$birthdate = $tickets[$i]->getAge();

				$diff = date_diff($birthdate, $date1);
				$age = (int) $diff->format("%a");
				$age /= 365.25;

				
				if ($age < 4)
				{
					$tickets[$i]->setPrice(0);
				}
				elseif ($age >= 4 && $age <= 12 ) 
				{
					$tickets[$i]->setPrice(8);
				}
				elseif ($age >= 60) 
				{
					$tickets[$i]->setPrice(12);
				}
				elseif($tickets[$i]->getDiscount())
				{
					$tickets[$i]->setPrice(10);
				}
				else
				{
					$tickets[$i]->setPrice(16);
				}
				
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
		$amount = 0;
		//Calcul du montant total de la commande
		for($i = 1; $i <= $booking->getNbTickets(); $i++)
		{
			$amount += ($tickets[$i]->getPrice());
		}
		$amountStripe = $amount*100;
		$session->set('amount', $amount);
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
		$token = $_POST['stripeToken'];

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
			)) );
		$mail = new \SendGrid\Mail($from, $subject, $to, $content);
		$apiKey = getenv('SENDGRID_API_KEY');
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