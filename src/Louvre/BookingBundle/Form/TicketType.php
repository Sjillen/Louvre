<?php

namespace Louvre\BookingBundle\Form;

use Louvre\BookingBundle\Entity\Ticket;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('age', BirthdayType::class, array(
                'attr' => ['class' => 'ageDate'],
                ))
            ->add('country', CountryType::class, array(
                'data' => 'FR',
                ))
            ->add('discount', CheckboxType::class, array(
                'required' => false, 
                'attr' => ['class' => 'checkbox'],
                'label' => '',              
            ));
            
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Ticket::class,
        ));
    }


}
