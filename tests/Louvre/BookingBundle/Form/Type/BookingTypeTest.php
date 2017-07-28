<?php

namespace Tests\Louvre\BookingBundle\Form\Type;

use Louvre\BookingBundle\Form\Type\BookingType;
use Louvre\BookingBundle\Model\Booking;
use Symfony\Component\Form\Test\TypeTestCase;

class BookingTypeTest extends TypeTestCase
{
	
	public function testSubmitValidData()
	{
		$formData = array(
			'date' => new \Datetime('now'),
			'type' => "Journée",
			'email' => "mail@example.com",
			'firstName' => 'Prénom',
			'lastName' => 'Nom',
			'nbTickets' => 3,
		);

		
		$form = $this->factory->create(BookingType::class);
		$booking = Booking::fromArray($formData);
		

		
		//Submit the data to the form directly
		$form->submit($formData);

		$this->assertTrue($form->isSynchronized());
		$this->assertEquals($booking, $form->getData());

		$view = $form->createView();
		$children = $view->children;

		foreach (array_keys($formData) as $key) {
			$this->assertArrayHasKey($key, $children);
		}
	}
}