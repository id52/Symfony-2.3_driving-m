<?php

namespace My\AppBundle\Form\Type;

use My\AppBundle\Form\DataTransformer\PassportNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PassportNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('series')
            ->add('number')
            ->addViewTransformer(new PassportNumberTransformer())
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['bs'] = true;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('error_bubbling' => false));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'passport_number';
    }
}
