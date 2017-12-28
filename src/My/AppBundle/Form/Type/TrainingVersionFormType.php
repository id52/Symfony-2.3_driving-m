<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

class TrainingVersionFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $version \My\AppBundle\Entity\TrainingVersion */
        $version = $options['data'];

        $builder
            ->add('category', 'entity', array(
                'class'       => 'AppBundle:Category',
                'empty_value' => 'choose_option',
            ))
            ->add('start_date', null, array(
                'data'        => ($version && $version->getStartDate()) ? $version->getStartDate() : new \DateTime(),
                'constraints' => array(new GreaterThan(new \DateTime('-1 day')))
            ))
        ;
    }

    public function getName()
    {
        return 'training_version';
    }
}
