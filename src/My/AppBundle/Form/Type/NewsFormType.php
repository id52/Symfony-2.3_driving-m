<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class NewsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', 'textarea')
            ->add('text', 'textarea', array('attr' => array('class' => 'ckeditor')))
            ->add('publish', 'checkbox', array('required' => false))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $item = $event->getData();
                $form = $event->getForm();
                if ($item && $item->getId()) {
                    $form->add('created_at');
                }
            });
    }

    public function getName()
    {
        return 'news';
    }
}
