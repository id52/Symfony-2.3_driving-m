<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ThemeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $theme \My\AppBundle\Entity\Theme */
        $theme = $options['data'];

        $builder
            ->add('subject', 'entity', array(
                'class'       => 'AppBundle:Subject',
                'property'    => 'title',
                'empty_value' => 'choose_option',
            ))
            ->add('title')
            ->add('text', null, array('attr' => array('class' => 'ckeditor')))
            ->add('versions', 'entity', array(
                'class'         => 'AppBundle:TrainingVersion',
                'expanded'      => true,
                'multiple'      => true,
                'query_builder' => function (EntityRepository $er) use ($theme) {
                    $qb = $er->createQueryBuilder('v')
                        ->addOrderBy('v.category')
                        ->addOrderBy('v.start_date')
                    ;
                    if ($theme->getSubject()) {
                        $qb
                            ->leftJoin('v.subjects', 's')
                            ->andWhere('s.id = :subject')->setParameter(':subject', $theme->getSubject())
                        ;
                    }
                    return $qb;
                },
            ))
        ;
    }

    public function getName()
    {
        return 'theme';
    }
}
