<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SubjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_id', 'hidden')
            ->add('title')
            ->add('brief_description')
            ->add('description', null, array('attr' => array('style' => 'height:100px')))
            ->add('versions', 'entity', array(
                'class'         => 'AppBundle:TrainingVersion',
                'expanded'      => true,
                'multiple'      => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->addOrderBy('v.category')
                        ->addOrderBy('v.start_date')
                    ;
                },
            ))
        ;
    }

    public function getName()
    {
        return 'subject';
    }
}
