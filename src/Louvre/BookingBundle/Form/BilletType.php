<?php

namespace Louvre\BookingBundle\Form;

use Louvre\BookingBundle\Entity\Billet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BilletType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			
			->add('tickets', CollectionType::class, array(
				'entry_type' => TicketType::class,
				'allow_add'   => true,
				'allow_delete' => true
				))
			->add('save', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Billet::class,
		));
	}
}