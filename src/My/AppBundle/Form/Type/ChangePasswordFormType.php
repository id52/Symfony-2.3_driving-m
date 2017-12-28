<?php

namespace My\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ChangePasswordFormType extends AbstractType
{
    private $securityContext;
    private $encoder;

    public function __construct(SecurityContextInterface $securityContext, EncoderFactoryInterface $encoder)
    {
        $this->securityContext = $securityContext;
        $this->encoder = $encoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_password', 'repeated', array(
                'type'            => 'password',
                'first_options'   => array('label' => 'form.new_password'),
                'second_options'  => array('label' => 'form.new_password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('old_password', 'password')

            // Check the current password is correct and plain_password is greater than 6 symbols
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                if (strlen(trim($form->get('new_password')->getData())) < 6) {
                    $form->get('new_password')->addError(new FormError('Пароль должен содержать не менее 6 символов.'));
                }

                $plainCurrentPassword = $form->get('old_password')->getData();
                $user = $this->securityContext->getToken()->getUser();
                $encoder = $this->encoder->getEncoder($user);
                $encoded = $encoder->encodePassword($plainCurrentPassword, $user->getSalt());

                if ($user->getPassword() !== $encoded) {
                    $form->get('old_password')->addError(new FormError('Текущий пароль неверен'));
                }
            });
    }

    public function getName()
    {
        return 'change_password';
    }
}
