<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assets;

class PrecheckFormType extends ProfileFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('close_final_exam', 'checkbox', array(
                'required' => false,
            ))
            ->add('closed_subjects', 'entity', array(
                'label'    => 'Заблокировать доступ к',
                'attr'     => ['class' => 'closed_subjects'],
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped'   => false,
                'class'    => 'AppBundle:Subject',
            ))
        ;
    }
}
