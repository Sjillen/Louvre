<?php

namespace Louvre\BookingBundle\Controller;

use Louvre\BookingBundle\Entity\Booking;
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
	public function indexAction(Request $request)
	{
		
		$session = $request->getSession();
		date_default_timezone_set('Europe/Paris');
		$booking = new Booking();


		$form = $this->createForm(BookingType::class, $booking);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$session->set('booking', $booking);

			return $this->redirectToRoute('louvre_booking_ticket');

		}

		return $this->render('LouvreBookingBundle:Booking:index.html.twig', array(
			'form' => $form->createView(),
			));
	}

	public function ticketAction(Request $request)
	{
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
				
			}
			
			

			return $this->redirectToRoute('louvre_booking_review');

			$request->getSession()->getFlashBag()->add('success','Votre reservation a bien ete prise en compte.');

		}

		return $this->render('LouvreBookingBundle:Booking:ticket.html.twig', array(
			'form' => $form->createView(),
			'booking' => $booking,

			
			));
	}

	public function recapAction(Request $request)
	{
		$session = $request->getSession();

		$booking = $session->get('booking');
		$tickets = $session->get('tickets');
		
		return $this->render('LouvreBookingBundle:Booking:recap.html.twig', array(
			'booking' => $booking,
			'tickets' => $tickets,
			
		));
	}


	public function confirmAction()
	{
		return $this->render('LouvreBookingBundle:Booking:confirm.html.twig');
	}
}