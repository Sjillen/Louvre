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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class BookingController extends Controller
{
	public function indexAction(Request $request)
	{
		
		$session = $request->getSession();
		
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
}