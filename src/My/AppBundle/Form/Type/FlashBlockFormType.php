<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FlashBlockFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', null, array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Regex('#^[-_A-z0-9]+$#'),
            )))
            ->add('title')
            ->add('is_simple', null, array('required' => false))
        ;
    }

    public function getName()
    {
        return 'flash_block';
    }
}
