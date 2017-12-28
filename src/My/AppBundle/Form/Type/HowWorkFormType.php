<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class HowWorkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_id', 'hidden')
            ->add('title')
            ->add('desc', 'textarea', array(
                'attr' => array('class' => 'ckeditor'),
                'constraints' => array(new NotBlank()),
            ))
        ;
    }

    public function getName()
    {
        return 'how_work';
    }
}
