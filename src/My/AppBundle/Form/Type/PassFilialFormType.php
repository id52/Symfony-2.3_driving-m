<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PassFilialFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_id', 'hidden')
            ->add('region', null, array(
                'required'    => true,
                'empty_value' => 'choose_option',
            ))
            ->add('active', null, array('required' => false))
            ->add('title')
            ->add('address')
            ->add('station')
            ->add('work_time')
            ->add('address_geo')
            ->add('map_code')
        ;
    }

    public function getName()
    {
        return 'pass_filial';
    }
}
