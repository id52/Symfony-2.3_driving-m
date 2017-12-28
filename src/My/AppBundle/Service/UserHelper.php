<?php

namespace My\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use My\AppBundle\Entity\User;
use My\AppBundle\Entity\UserConfirmation;
use My\SmsUslugiRuBundle\Service\SmsUslugiRu;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Routing\RouterInterface;

class UserHelper
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;
    /** @var \My\SmsUslugiRuBundle\Service\SmsUslugiRu */
    protected $smsUslugi;
    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;
    /** @var \My\AppBundle\Service\Notify */
    protected $notify;
    /** @var \FOS\UserBundle\Model\UserManagerInterface */
    protected $userManager;

    protected $siteHost;

    public function __construct(
        EntityManager $em,
        SmsUslugiRu $smsUslugiRu,
        RouterInterface $router,
        Notify $notify,
        UserManagerInterface $userManager,
        $siteHost
    ) {
        $this->em = $em;
        $this->smsUslugi = $smsUslugiRu;
        $this->router = $router;
        $this->notify = $notify;
        $this->userManager = $userManager;
        $this->siteHost = $siteHost;
    }

    /**
     * Отправляет sms/email для подтверждения регистрации пользователя
     *
     * @param User $user
     * @param null $password
     * @param boolean $forceSend отправлять СМС даже если интервал меньше 3-х минут
     */
    public function sendMessages(User $user, $password, $forceSend)
    {
        $userConfirmation = $this->em->getRepository('AppBundle:UserConfirmation')->findOneBy(array('user' => $user));

        if ($userConfirmation) {
            $userConfirmation->setSmsCode($this->generateCode(4));
        } else {
            $userConfirmation = new UserConfirmation();
            $userConfirmation->setSmsCode($this->generateCode(4));
            $userConfirmation->setUser($user);
            $userConfirmation->setPhone($user->getPhoneMobile());
        }
        $this->em->persist($userConfirmation);
        $this->em->flush();

        $uniqueUrl = $this->router->generate('fos_user_confirmation', array(
            'hash' => $userConfirmation->getHash(),
        ), true);

        $settings_repository = $this->em->getRepository('AppBundle:Setting');
        $settings = $settings_repository->getAllData();
        $subject = $settings['add_user_title'];
        $message = $settings['add_user_text'];
        $message = str_replace('{{ phone }}', '+7'.$userConfirmation->getUser()->getPhoneMobile(), $message);
        $message = str_replace('{{ email }}', $user->getEmail(), $message);
        $message = str_replace('{{ password }}', $password, $message);
        $message = str_replace('{{ category }}', $user->getCategory()->getName(), $message);
        $message = str_replace('{{ kpp }}', ($user->getCategory()->getWithAt() ? 'А' : 'М').'КПП', $message);
        $message = str_replace('{{ url }}', $uniqueUrl, $message);
        $this->notify->sendEmail($user, $subject, $message);

        $this->sendConfirmationSms($userConfirmation, $forceSend, $password);
    }

    public function generateCode($length = 8)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= rand(0, 9);
        }
        return $code;
    }

    public function sendConfirmationSms(UserConfirmation $userConfirmation, $forceSend, $password = null)
    {
        if (!$forceSend) {
            $current = new \DateTime();
            $diff = $current->getTimestamp() - $userConfirmation->getLastSent()->getTimestamp();
            if ($diff < 180) {
                return false;
            }
        }

        $text = 'Код: '.$userConfirmation->getSmsCode().';';
        $text .= ' Сайт: http://'.$this->siteHost.';';
        $text .= ' Логин: '.$userConfirmation->getUser()->getEmail().';';
        if (!$password) {
            $password = $this->generateCode(8);
            $user = $userConfirmation->getUser();
            $user->setPlainPassword($password);
            $this->userManager->updateUser($user);
        }
        $text .= ' Пароль: '.$password;

        $this->smsUslugi->query('+7'.$userConfirmation->getUser()->getPhoneMobile(), $text);

        $userConfirmation->setLastSent(new \DateTime());
        $this->em->persist($userConfirmation);
        $this->em->flush();

        return true;
    }
}
