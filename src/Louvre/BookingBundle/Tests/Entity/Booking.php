<?php

namespace Louvre\BookingBundle\Tests\Entity;

use Louvre\BookingBundle\Entity\Booking;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
	$booking = new Booking();

	$this->assertEquals('test@mail.com', $booking->setEmail('test@mail.com'));
	$this->assertEquals('firstName', $booking->setFirstName('firstName'));
	$this->assertEquals('lastName', $booking->setLastName('lastName'));
	$this->assertEquals('10', $booking->setNbTickets('10'));
}