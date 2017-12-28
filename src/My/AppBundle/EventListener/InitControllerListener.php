<?php

namespace My\AppBundle\EventListener;

use Innocead\CaptchaBundle\Validator\Constraints\Captcha;
use My\AppBundle\Exception\AppResponseException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class InitControllerListener
{
    /** @var $em \Doctrine\ORM\EntityManager */
    protected $em;
    /** @var $twig \Twig_Environment */
    protected $twig;
    /** @var $router \Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;
    /** @var $csrfProvider \Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider */
    protected $csrfProvider;
    /** @var $container \Symfony\Component\DependencyInjection\Container */
    protected $container;

    protected $responseNewCookies = array();

    public function __construct($container)
    {
        $this->container = $container;

        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->twig = $this->container->get('twig');
        $this->router = $this->container->get('router');
        $this->csrfProvider = $this->container->get('form.csrf_provider');
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $user = null;
        $token = $this->container->get('security.context')->getToken();
        if (!is_null($token)) {
            if (is_object($token->getUser())) {
                $user = $token->getUser();
            }
        }

        if (!$user) {
            //add list of categories for regions and names of categories
            $discount_data = array();
            $qb = $this->em->getRepository('AppBundle:Region')->createQueryBuilder('r')
                ->leftJoin('r.categories_prices', 'cp', 'WITH', 'cp.active = :active')
                ->setParameter(':active', true)
                ->addSelect('cp')
                ->leftJoin('cp.category', 'c')
                ->addSelect('c')
                ->leftJoin('c.image', 'i')
                ->addSelect('i')
            ;
            $regions = $qb->getQuery()->execute();

            $start_date = new \DateTime('2014-01-01');
            $today = new \DateTime('today');
            foreach ($regions as $region) {
                /** @var $region \My\AppBundle\Entity\Region */

                $region_data = array(
                    'name'       => $region->getName(),
                    'categories' => array(),
                );
                foreach ($region->getCategoriesPrices() as $price) {
                    /** @var $price \My\AppBundle\Entity\CategoryPrice */

                    $category_data = array(
                        'name'  => $price->getCategory()->getName(),
                        'image' => $price->getCategory()->getImage()
                            ? $price->getCategory()->getImage()->getWebPath() : '',
//                        'price' => $price->getPrice(),
                    );
                    if ($region->getDiscount1Amount() > 0) {
                        if ($region->getDiscount1DateFrom() <= $today && $today <= $region->getDiscount1DateTo()) {
                            /** @var $final_date \DateTime */
                            $final_date = $region->getDiscount1DateTo();
                            $category_data['final_date'] = $final_date->format('r');
                        } elseif ($region->getDiscount1TimerPeriod() > 0) {
                            $final_date = clone $today;
                            $days = $start_date->diff($final_date)->format('%a');
                            $days_before_final = $region->getDiscount1TimerPeriod()
                                - $days % $region->getDiscount1TimerPeriod();
                            $category_data['final_date'] = $final_date
                                ->add(new \DateInterval('P'.$days_before_final.'D'))
                                ->format('r');
                        }
                        if (isset($category_data['final_date'])) {
                            $category_data['discount'] = $region->getDiscount1Amount();
                        }
                    }
                    $region_data['categories'][$price->getCategory()->getId()] = $category_data;
                }
                if (!empty($region_data['categories'])) {
                    $discount_data[$region->getId()] = $region_data;
                }
            }
            $this->twig->addGlobal('discount_data', $discount_data);

            //auth scrf token
            $this->twig->addGlobal('csrf_token_auth', $this->csrfProvider->generateCsrfToken('authenticate'));
            $this->twig->addGlobal('csrf_token_reg', $this->csrfProvider->generateCsrfToken('registration'));
        }

        //add articles link to menu
        /** @var $articleRepo \Doctrine\ORM\EntityRepository */
        $articleRepo = $this->em->getRepository('AppBundle:Article');
        $menu_items = $articleRepo->createQueryBuilder('a')
            ->addOrderBy('a.position')
            ->getQuery()->getResult();
        $this->twig->addGlobal('menu_items', $menu_items);

        /** @var $settings_repository \My\AppBundle\Repository\SettingRepository */
        $settings_repository = $this->em->getRepository('AppBundle:Setting');
        $settings = $settings_repository->getAllData();
        $cookies = $this->container->get('request')->cookies;
        if ($cookies->has('by_groupon')) {
            $settings['contacts_phone1_prefix'] = $settings['coupons_phone1_prefix'];
            $settings['contacts_phone1'] = $settings['coupons_phone1'];
            $settings['contacts_phone2_prefix'] = $settings['coupons_phone2_prefix'];
            $settings['contacts_phone2'] = $settings['coupons_phone2'];
        }
        $this->twig->addGlobal('settings', $settings);

        //get count of unread support dialogs
        if ($user) {
            /** @var $supportDialogRepo \My\AppBundle\Repository\SupportDialogRepository */
            $supportDialogRepo = $this->em->getRepository('AppBundle:SupportDialog');
            $dialogs_count = $supportDialogRepo->getCountOfUserUnreadCategoryDialogs($user);
            $this->twig->addGlobal('support_unread_dialogs_count', $dialogs_count);
            $dialogs_count = $supportDialogRepo->getCountOfUserUnreadTeachersDialogs($user);
            $this->twig->addGlobal('support_unread_teachers_dialogs_count', $dialogs_count);
        } else {
            $this->twig->addGlobal('support_unread_dialogs_count', 0);
        }

        $fb = $this->container->get('form.factory')->createNamedBuilder('sendmail');
        if (!$user) {
            $fb->add('name', 'text', array('constraints' => array(
                new NotBlank(),
            )));
            $fb->add('email', 'email', array('constraints' => array(
                new NotBlank(),
                new Email(),
            )));
        }
        $fb->add('message', 'textarea', array('constraints' => array(
            new NotBlank(),
            new Length(array('min' => 50)),
        )));
        if (!$user) {
            $fb->add('captcha', 'innocead_captcha', array('constraints' => array(
                new NotBlank(),
                new Captcha(),
            )));
        }
        $fb->setAction($this->router->generate('send_feedback'));
        $this->twig->addGlobal('sendmail_form', $fb->getForm()->createView());

        /** Не показывать определённый контент для пришедших с биглиона */
        if (!$cookies->has('strange')) {
            $refer = $this->container->get('request')->headers->get('referer');
            $is_strange = !$refer || preg_match('#biglion.ru#', $refer);
            $this->responseNewCookies[] = new Cookie('strange', $is_strange ? 1 : 0, time() + (365*24*60*60));
        } else {
            $is_strange = boolval($cookies->get('strange'));
        }
        $is_strange = false; // @TODO временно
        $this->twig->addGlobal('is_strange', $is_strange);

        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }
        $controller = $controller[0];

        $controller->em = $this->em;
        $controller->user = $user;
        $controller->settings = $settings;
        $controller->is_strange = $is_strange;

        if (method_exists($controller, 'init')) {
            call_user_func(array($controller, 'init'));
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof AppResponseException) {
            $event->setResponse($exception->getResponse());
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            foreach ($this->responseNewCookies as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }
    }
}
