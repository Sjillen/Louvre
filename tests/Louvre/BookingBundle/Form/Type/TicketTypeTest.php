<?php

namespace Tests\Louvre\BookingBundle\Form\Type;

use Louvre\BookingBundle\Form\Type\TicketType;
use Louvre\BookingBundle\Model\Ticket;
use Symfony\Component\Form\Test\TypeTestCase;

class TicketTypeTest extends TypeTestCase
{
	public function testSubmitValidData()
	{
		$formData = array(
			'reference' => '9A9A9A9A',
			'firstName' => 'Prénom1',
			'lastName' => 'Nom1',
			'age' => '09/05/1989',
			'country' => 'FR',
			'discount' => false,
		);

		$form = $this->factory->create(TicketType::class);
		$ticket = Ticket::fromArray($formData);

		//Submit the data to the form directly
		$form->submit($formData);

		$this->assertTrue($form->isSynchronized());
		$this->assertEquals($ticket, $form->getData());

		$view = $form->createView();
		$children = $view->children;

		foreach (array_keys($formData) as $key) {
			$this->assertArrayHasKey($key, $children);
		}
	}
}