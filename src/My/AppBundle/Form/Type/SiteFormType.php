<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SiteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_id', 'hidden')
            ->add('region', null, array(
                'required'    => true,
                'empty_value' => 'choose_option',
            ))
            ->add('active', 'checkbox', array('required' => false))
            ->add('active_auth', 'checkbox', array('required' => false))
            ->add('show', 'checkbox', array('required' => false))
            ->add('show_auth', 'checkbox', array('required' => false))
            ->add('title')
        ;
    }

    public function getName()
    {
        return 'site';
    }
}
