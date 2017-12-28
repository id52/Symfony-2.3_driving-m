<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $question \My\AppBundle\Entity\Question */
        $question = $options['data'];

        $builder
            ->add('image_id', 'hidden')
            ->add('theme', 'entity', array(
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
            ->add('is_pdd', null, array('required' => false))
            ->add('num')
            ->add('text', null, array('attr' => array('style' => 'height:100px')))
            ->add('description', null, array('attr' => array('style' => 'height:100px')))
            ->add('versions', 'entity', array(
                'class'         => 'AppBundle:TrainingVersion',
                'expanded'      => true,
                'multiple'      => true,
                'query_builder' => function (EntityRepository $er) use ($question) {
                    $qb = $er->createQueryBuilder('v')
                        ->addOrderBy('v.category')
                        ->addOrderBy('v.start_date')
                    ;
                    if ($question->getTheme()) {
                        $qb
                            ->leftJoin('v.themes', 't')
                            ->andWhere('t.id = :theme')->setParameter(':theme', $question->getTheme())
                        ;
                    }
                    return $qb;
                },
            ))
        ;
    }

    public function getName()
    {
        return 'question';
    }
}
