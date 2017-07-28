<?php

namespace Tests\Louvre\BookingBundle\Entity;

use Louvre\BookingBundle\Entity\Booking;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
	const DATE = "29/09/2017";
	const TYPE = "Journée";
	const MAIL = "mail@example.com";
	const FIRSTNAME = "FirstN";
	const LASTNAME = "LastN";
	const NBTICKETS = 3;

	public function testBookingCreation()
	{
		$booking = new Booking();

		$booking->setDate(self::DATE)
				->setType(self::TYPE)
				->setEmail(self::MAIL)
				->setFirstName(self::FIRSTNAME)
				->setLastName(self::LASTNAME)
				->setNbTickets(self::NBTICKETS)
				;

		
		$this->assertEquals($booking->getDate(), self::DATE);
		$this->assertEquals($booking->getType(), self::TYPE);
		$this->assertEquals($booking->getEmail(), self::MAIL);
		$this->assertEquals($booking->getFirstName(), self::FIRSTNAME);
		$this->assertEquals($booking->getLastName(), self::LASTNAME);
		$this->assertEquals($booking->getNbTickets(), self::NBTICKETS);
			


	}

	public function testUniqueReference()
	{
		$booking1 = new Booking();
		$booking2 = new Booking();

		$this->assertNotEquals($booking1->getReference(), $booking2->getReference());
	}
}

