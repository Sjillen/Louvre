<?php

namespace Louvre\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('tickets', CollectionType::class, array(
				'entry_value' => TicketType::class,
				'allow_add'   => true,
				'allow_delete' => true
				))
			->add('save', SubmitType::class);
	}
}