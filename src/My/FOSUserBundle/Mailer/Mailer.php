<?php

namespace My\FOSUserBundle\Mailer;

use My\AppBundle\Service\Notify;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;

class Mailer implements MailerInterface
{
    protected $notify;

    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $this->notify->sendConfirmationRegistration($user);
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        $this->notify->sendResettingPassword($user);
    }
}
