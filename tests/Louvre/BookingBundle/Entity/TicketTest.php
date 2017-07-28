<?php

namespace Tests\Louvre\BookingBundle\Entity;

use Louvre\BookingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{

	const FIRSTNAME = "FirstN";
	const LASTNAME = "LastN";
	const AGE = "29/09/1987";
	const COUNTRY = "FR";
	const DISCOUNT = false;
	const PRICE = 16;

	public function testTicketCreation()
	{
		$ticket = new Ticket();

		$ticket->setFirstName(self::FIRSTNAME)
				->setLastName(self::LASTNAME)
				->setAge(self::AGE)
				->setCountry(self::COUNTRY)
				->setDiscount(self::DISCOUNT)
				;
		
		$this->assertEquals($ticket->getFirstName(), self::FIRSTNAME);
		$this->assertEquals($ticket->getLastName(), self::LASTNAME);
		$this->assertEquals($ticket->getAge(), self::AGE);
		$this->assertEquals($ticket->getCountry(), self::COUNTRY);
		$this->assertEquals($ticket->getDiscount(), self::DISCOUNT);
		$this->assertEquals($ticket->getPrice(), self::PRICE);

	}

}

