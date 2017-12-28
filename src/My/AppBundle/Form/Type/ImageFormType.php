<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uploadFile', 'file', array(
                'attr'           => array('accept' => 'image/*'),
                'error_bubbling' => true,
                'constraints'    => array(
                    new Image(array('maxSize' => '10M')),
                ),
            ))
        ;
    }

    public function getName()
    {
        return 'image';
    }
}
