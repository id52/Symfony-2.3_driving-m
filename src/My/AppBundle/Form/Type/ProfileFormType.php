<?php

namespace My\AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assets;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $user \My\AppBundle\Entity\User */
        $user = $options['data'];

        $builder
            ->add('last_name')
            ->add('first_name')
            ->add('patronymic')
            ->add('sex', 'choice', array(
                'expanded' => true,
                'choices' => array(
                    'male'    => 'male',
                    'female'  => 'female',
                ),
            ))
            ->add('birthday', 'birthday', array(
                'years' => range(1930, date('Y')),
                'data'  => ($user && $user->getBirthday()) ? $user->getBirthday() : new \DateTime('01-01-1995'),
            ))
            ->add('birth_country', null, array('required' => true))
            ->add('birth_region', null, array('required' => true))
            ->add('birth_city', null, array('required' => true))
            ->add('foreign_passport', null, array('required' => false))
            ->add('foreign_passport_number')
            ->add('passport_number', 'passport_number', array('help' => 'profile_passport_number_help'))
            ->add('passport_rovd', null, array('required' => true))
            ->add('passport_rovd_number', null, array('required' => true))
            ->add('passport_rovd_date', null, array(
                'years' => range(1990, date('Y')),
                'data'  => ($user && $user->getPassportRovdDate())
                    ? $user->getPassportRovdDate() : new \DateTime('01-01-2010'),
            ))
            ->add('not_registration', null, array('required' => false))
            ->add('registration_country')
            ->add('registration_region')
            ->add('registration_city')
            ->add('registration_street')
            ->add('registration_house')
            ->add('registration_stroenie')
            ->add('registration_korpus')
            ->add('registration_apartament', null, array('required' => true))
            ->add('place_country')
            ->add('place_region')
            ->add('place_city')
            ->add('place_street')
            ->add('place_house')
            ->add('place_stroenie')
            ->add('place_korpus')
            ->add('place_apartament', null, array('required' => true))
            ->add('work_place', null, array(
                'required'    => true,
                'constraints' => array(new Assets\NotBlank()),
            ))
            ->add('work_position', null, array(
                'required'    => true,
                'constraints' => array(new Assets\NotBlank()),
            ))
            ->add('phone_mobile', null, array('help' => 'profile_phone_mobile_help'))
            ->add('region_place', 'entity', array(
                'empty_value'   => 'choose_option',
                'class'         => 'AppBundle:RegionPlace',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('rp')
                        ->andWhere('rp.region = :region')->setParameter(':region', $user->getRegion())
                        ->addOrderBy('rp.name')
                    ;
                },
                'required'      => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('validation_groups' => array('profile')));
    }

    public function getName()
    {
        return 'profile';
    }
}
