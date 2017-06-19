<?php

namespace Louvre\BookingBundle\Controller;

use Louvre\BookingBundle\Entity\Booking;
use Louvre\BookingBundle\Entity\Ticket;
use Louvre\BookingBundle\Form\BookingType;
use Louvre\BookingBundle\Form\TicketType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class BookingController extends Controller
{
	public function indexAction(Request $request)
	{
		$booking = new Booking();

		$form = $this->createForm(BookingType::class, $booking);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($booking);
			$em->flush();

			return $this->redirectToRoute('louvre_booking_ticket');

		}

		return $this->render('LouvreBookingBundle:Booking:index.html.twig', array(
			'form' => $form->createView(),
			));
	}

	public function ticketAction(Request $request)
	{
		$ticket = new Ticket();

		$form = $this->createForm(TicketType::class, $ticket);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($ticket);
			$em->flush();

			return $this->redirectToRoute('louvre_booking_index');

			$request->getSession()->getFlashBag()->add('success','Votre reservation a bien ete prise en compte.');

		}

		return $this->render('LouvreBookingBundle:Booking:ticket.html.twig', array(
			'form' => $form->createView(),
			));
	}
}