<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

class MailingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $mailing \My\AppBundle\Entity\Mailing */
        $mailing = $options['data'];

        $builder
            ->add('name')
            ->add('title')
            ->add('message', null, array(
                'attr' => array('class' => 'ckeditor'),
                'help' => 'mailing_message_help',
            ))
            ->add('date', null, array(
                'data' => ($mailing && $mailing->getDate()) ? $mailing->getDate() : new \DateTime('+1 day'),
                'constraints' => array(new GreaterThan(new \DateTime()))
            ))
            ->add('forceSending', null, array('required' => false))
        ;
    }

    public function getName()
    {
        return 'mailing';
    }
}
