<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FlashBlockItemSimpleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_id', 'hidden')
            ->add('title')
            ->add('subtitle')
            ->add('text', null, array('attr' => array('class' => 'ckeditor')))
        ;
    }

    public function getName()
    {
        return 'flash_block_item';
    }
}
