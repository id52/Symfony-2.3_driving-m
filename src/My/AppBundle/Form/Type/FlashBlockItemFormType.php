<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FlashBlockItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'choice', array(
                'empty_value' => ' ------ ',
                'choices' => array(
                    '1_who'       => 'Кто',
                    '2_what'      => 'Что',
                    '3_where'     => 'Где',
                    '4_action'    => 'Действие',
                    '5_condition' => 'Условие',
                ),
            ))
            ->add('parent')
            ->add('title')
            ->add('text', null, array('attr' => array('class' => 'ckeditor')))
        ;
    }

    public function getName()
    {
        return 'flash_block_item';
    }
}
