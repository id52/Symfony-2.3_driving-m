<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SliceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $slice \My\AppBundle\Entity\Slice */
        $slice = $options['data'];

        $builder
            ->add('after_theme', 'entity', array(
                'class'         => 'AppBundle:Theme',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->addOrderBy('t.subject')
                        ->addOrderBy('t.position')
                    ;
                },
                'property'      => 'title',
                'group_by'      => 'subject.title',
                'empty_value'   => 'choose_option',
            ))
            ->add('versions', 'entity', array(
                'class'         => 'AppBundle:TrainingVersion',
                'expanded'      => true,
                'multiple'      => true,
                'query_builder' => function (EntityRepository $er) use ($slice) {
                    $qb = $er->createQueryBuilder('v')
                        ->addOrderBy('v.category')
                        ->addOrderBy('v.start_date')
                    ;
                    if ($slice->getAfterTheme()) {
                        $qb
                            ->leftJoin('v.themes', 't')
                            ->andWhere('t.id = :theme')->setParameter(':theme', $slice->getAfterTheme())
                        ;
                    }
                    return $qb;
                },
            ))
        ;
    }

    public function getName()
    {
        return 'slice';
    }
}
