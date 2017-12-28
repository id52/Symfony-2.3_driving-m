<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleProfileFormType extends AbstractType
{
    protected $password = '';
    protected $paids = array();

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setPaids($paids)
    {
        $this->paids = $paids;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('last_name')
            ->add('first_name')
            ->add('patronymic')
            ->add('email', 'email')
            ->add('phone_mobile', null, array('help' => 'simple_profile_phone_mobile_help'))
            ->add('region', 'entity', array(
                'class'       => 'AppBundle:Region',
                'required'    => true,
                'empty_value' => 'choose_option',
            ))
            ->add('category', 'entity', array(
                'class'       => 'AppBundle:Category',
                'required'    => true,
                'empty_value' => 'choose_option',
            ))
            ->add('with_at', 'checkbox', array(
                'required' => false,
                'mapped'   => false,
            ))
            ->add('paid', 'choice', array(
                'mapped'      => false,
                'empty_value' => 'choose_option',
                'choices'     => $this->paids,
            ))
            ->add('closed_subjects', 'entity', array(
                'label'    => 'Заблокировать доступ к',
                'attr'     => ['class' => 'closed_subjects'],
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped'   => false,
                'class'    => 'AppBundle:Subject',
            ))
            ->add('close_final_exam', 'checkbox', array(
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('validation_groups' => array('simple_profile', 'Registration')));
    }

    public function getName()
    {
        return 'simple_profile';
    }
}
