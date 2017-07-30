<?php

namespace Tests\Louvre\BookingBundle\BookingManager;

use PHPUnit\Framework\TestCase;
use Louvre\BookingBundle\BookingManager\LouvreDateChecker;
use Louvre\BookingBundle\BookingManager\LouvreTypeChecker;
use Louvre\BookingBundle\BookingManager\LouvrePriceChecker;
use Louvre\BookingBundle\Entity\Booking;
use Louvre\BookingBundle\Entity\Ticket;

class BookingManagerTest extends TestCase
{
	

	/**
	 *test validity of passed date
	 *
	 */
	public function testPassedDate()
	{
		$booking = new Booking();
		$booking->setDate(new \Datetime('05/09/2017')); // passed date should be invalid
		$date = $booking->getDate();
		$dateChecker = new LouvreDateChecker();
		$invalidDate = $dateChecker->checkValidDate($date);

		$this->assertTrue($invalidDate);

	}

	/**
	 * test validity if date is a sunday
	 *
	 */
	public function testSunday()
	{
		$booking = new Booking();
		$booking->setDate(new \Datetime('09/03/2017')); //is a sunday
		$date = $booking->getDate();
		$dateChecker = new LouvreDateChecker();
		$dayOff = $dateChecker->checkDayOff($date);

		$this->assertTrue($dayOff);
	}

	/**
	 * test validity if date is a day off
	 *
	 */
	public function testDayOff()
	{
		$booking = new Booking();
		$booking->setDate(new \Datetime('11/01/2017')); //is day off
		$date = $booking->getDate();
		$dateChecker = new LouvreDateChecker();
		$dayOff = $dateChecker->checkDayOff($date);

		$this->assertTrue($dayOff);		
	}

	/**
	 * test validity of type according to hour of the day
	 *
	 * FAILURE if time of execution is before 2pm
	 * SUCCESS if after 2pm
	 *
	 */
	public function testType()
	{
		
		$booking = new Booking();
		$booking->setType("Journée");
		$type = $booking->getType();
		$booking->setDate(new \Datetime('now')); 
		$date = $booking->getDate();
		$typeChecker = new LouvreTypeChecker();
		$errorType = $typeChecker->checkType($date, $type);

		$this->assertTrue($errorType);

	}

	/**
	 * test applied discount on price
	 *
	 */
	public function testDiscount()
	{
		$ticket = new Ticket();
		$ticket->setDiscount(true);
		$birthdate = new \Datetime('05/09/1989');
		$type = "Journée";
		$discount = $ticket->getDiscount();
		$price = $ticket->getPrice();
		$priceChecker = new LouvrePriceChecker();
		$price = $priceChecker->checkPrice($birthdate, $type, $discount);

		$this->assertEquals(10, $price);

	}

	/**
	 * test price setting
	 *
	 * according to type of booking and age of ticket holder
	 * 
	 */
	public function testAgeTypePrice()
	{
		$booking = new Booking();
		$booking->setType('Demi-journée'); // half day, half price
		$type = $booking->getType();

		$ticket = new Ticket();
		$ticket->setDiscount(false); // No discount
		$discount = $ticket->getDiscount(); 
		$ticket->setAge(new \Datetime('08/05/1950')); // over 60 so senior discount is applied
		$birthdate = $ticket->getAge();
		$priceChecker = new LouvrePriceChecker();
		$price = $priceChecker->checkPrice($birthdate, $type, $discount);

		$this->assertEquals(6, $price);

	}
}