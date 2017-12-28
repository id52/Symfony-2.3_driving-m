<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FaqFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', 'text')
            ->add('answer', 'textarea', array('attr' => array('class' => 'ckeditor')))
            ->add('status', null, array('required' => false))
        ;
    }

    public function getName()
    {
        return 'faq';
    }
}
