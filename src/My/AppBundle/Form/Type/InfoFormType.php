<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

class InfoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('text', null, array('attr' => array('class' => 'ckeditor')))
            ->add('release_time', null, array(
                'data' => (new \DateTime('+10 min')),
                'constraints' => array(new GreaterThan(new \DateTime()))
            ))
        ;
    }

    public function getName()
    {
        return 'info';
    }
}
