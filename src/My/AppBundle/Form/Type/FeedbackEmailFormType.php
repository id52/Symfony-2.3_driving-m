<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FeedbackEmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('email', 'email', array('help' => '<b style="color:red">ТОЛЬКО mail.ru !!!</b>'))
            ->add('password', 'password')
            ->add('subjects', 'textarea')
        ;
    }

    public function getName()
    {
        return 'feedbackEmail';
    }
}
