<?php

namespace My\AppBundle\EventListener;

use My\AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices;

class WhiteIpsListener
{
    protected $context;
    protected $router;
    protected $cookieOptions;

    public function __construct(
        SecurityContext $context,
        RouterInterface $router,
        $remembermeName,
        $remembermePath,
        $remembermeDomain
    ) {
        $this->context = $context;
        $this->router = $router;
        $this->cookieOptions = array(
            'name'   => $remembermeName,
            'path'   => $remembermePath,
            'domain' => $remembermeDomain,
        );
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ('fos_user_security_login' != $event->getRequest()->attributes->get('_route')) {
            $token = $this->context->getToken();
            if ($token) {
                /** @var $user \My\AppBundle\Entity\User */
                $user = $token->getUser();
                if ($user instanceof User) {
                    $white_ips = $user->getWhiteIps();
                    if ($white_ips) {
                        $ip = $event->getRequest()->getClientIp();
                        if (!in_array($ip, $white_ips)) {
                            $this->context->setToken(null);
                            $cookie = new Cookie(
                                $this->cookieOptions['name'],
                                null,
                                1,
                                $this->cookieOptions['path'],
                                $this->cookieOptions['domain']
                            );
                            $event->getRequest()->attributes->set(
                                AbstractRememberMeServices::COOKIE_ATTR_NAME,
                                $cookie
                            );
                            $url = $this->router->generate('fos_user_security_login');
                            $event->setResponse(new RedirectResponse($url));
                        }
                    }
                }
            }
        }
    }
}
