<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class PromoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            $form = $event->getForm();

            $form->add('active', 'checkbox', array(
                'required'    => false,
            ));
            $form->add('name', 'text', array('constraints' => array(new Assert\NotBlank())));
            $form->add('type', 'choice', array(
                'disabled'    => $entity && $entity->getId(),
                'empty_value' => 'choose_option',
                'choices'     => array(
                    'keys'  => 'types.keys',
                    'users' => 'types.users',
                ),
            ));
            $form->add('keys', 'integer', array(
                'disabled'    => $entity && $entity->getId(),
                'mapped'      => false,
                'required'    => false,
                'attr'        => array('class' => 'span1'),
                'constraints' => array(new Assert\GreaterThan(0)),
                'data'        => $entity && $entity->getId() ? count($entity->getKeys()) : 1,
            ));
            $form->add('max_activations', 'integer', array(
                'disabled'    => $entity && $entity->getId(),
                'required'    => false,
                'attr'        => array('class' => 'span1'),
                'constraints' => array(new Assert\GreaterThan(0)),
                'data'        => $entity && $entity->getId() ? $entity->getMaxActivations() : 1,
            ));
/*
            $form->add('payment_type', 'choice', array(
                'disabled'    => $entity && $entity->getId(),
                'empty_value' => 'choose_option',
                'choices'     => array(
                    'first'  => 'payment_types.first',
                    'second' => 'payment_types.second',
                ),
            ));
*/
            $form->add('discount', 'money', array(
                'currency' => 'RUB',
                'precision' => 0,
                'attr' => array('class' => 'span1'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                ),
            ));
            $form->add('used_from', 'datetime', array('years' => range(date('Y') - 1, date('Y'))));
            $form->add('used_to', 'datetime', array('years' => range(date('Y'), date('Y') + 5)));
        });
    }

    public function getName()
    {
        return 'promo';
    }
}
