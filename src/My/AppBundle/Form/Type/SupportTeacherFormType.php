<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SupportTeacherFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', 'entity', array(
                'class'       => 'AppBundle:SupportCategory',
                'property'    => 'name',
                'empty_value' => 'choose_root',
                'required'    => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('sc')
                        ->andWhere('sc.parent IS NULL')
                        ->andWhere('sc.type = :type')->setParameter('type', 'teacher');
                },
            ))
            ->add('name');
    }

    public function getName()
    {
        return 'support_category';
    }
}
