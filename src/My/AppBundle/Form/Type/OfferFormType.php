<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class OfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_id', 'hidden')
            ->add('is_public', null, array('required' => false))
            ->add('title')
            ->add('subtitle')
            ->add('text1', 'textarea', array(
                'attr' => array('class' => 'ckeditor'),
                'constraints' => array(new NotBlank()),
            ))
            ->add('text2', 'textarea', array(
                'constraints' => array(new NotBlank()),
            ))
            ->add('text3', 'textarea', array(
                'constraints' => array(new NotBlank()),
            ))
            ->add('text4', 'textarea', array(
                'constraints' => array(new NotBlank()),
            ))
            ->add('desc', 'textarea', array(
                'attr' => array('class' => 'ckeditor'),
                'constraints' => array(new NotBlank()),
            ))
            ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')))
            ->add('started_at')
            ->add('ended_at')
        ;
    }

    public function getName()
    {
        return 'offer';
    }
}
