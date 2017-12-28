<?php

namespace My\FOSUserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends BaseController
{
    public function loginAction()
    {
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->container->get('request');

        /* @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $error = $error->getMessage();
        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

        /** @var $translator \Symfony\Bundle\FrameworkBundle\Translation\Translator */
        $translator = $this->container->get('translator');

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => $lastUsername));

        if ('User account is disabled.' == $error) {
            $url = '';
            $changeEmailUrl = '';
            if ($user) {
                $token = $user->getConfirmationToken();
                if (!$token) {
                    $tokenGenerator = new TokenGenerator();
                    $token = $tokenGenerator->generateToken();
                    $user->setConfirmationToken($token);
                    $em->persist($user);
                    $em->flush();
                }

                $router = $this->container->get('router');
                $url = $router->generate('fos_user_register_resend', array('token' => $token));
                $changeEmailUrl = $router->generate('fos_user_change_email', array('token' => $token));
            }

            $error = $translator->trans($error, array(
                '%url%' => $url,
                '%change_email_url%' => $changeEmailUrl,
            ), 'FOSUserBundle');
        } elseif ('User account is locked.' == $error) {
            $data = $this->container->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Setting')
                ->findOneBy(array('_key' => 'error_account_locked'));
            if ($data) {
                $placeholders = array(
                    '{{ last_name }}'  => $user->getLastName(),
                    '{{ first_name }}' => $user->getFirstName(),
                    '{{ patronymic }}' => $user->getPatronymic(),
                );
                $error = str_replace(array_keys($placeholders), array_values($placeholders), $data->getValue());
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'error' => $translator->trans($error, array(), 'FOSUserBundle'),
            ));
        } else {
            return $this->renderLogin(array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token'    => $csrfToken,
            ));
        }
    }
}
