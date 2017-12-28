<?php

namespace My\AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Innocead\CaptchaBundle\Validator\Constraints\Captcha;
use My\AppBundle\Entity\CategoryPrice;
use My\AppBundle\Entity\Image;
use My\AppBundle\Entity\Review;
use My\AppBundle\Form\Type\ImageFormType;
use My\AppBundle\Pagerfanta\Pagerfanta;
use My\PaymentBundle\Entity\Log as PaymentLog;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use My\AppBundle\Util\Time;

class DefaultController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;

    public $settings = array();

    public $is_strange = true;

    public function articleAction($id)
    {
        $id = trim(trim($id, '\/'));
        $article = $this->em->getRepository('AppBundle:Article')->findOneBy(array('url' => $id));

        if (!$article) {
            throw $this->createNotFoundException('Статья не найдена');
        }

        if ($article->getPrivate() && !$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedHttpException;
        }

        return $this->render('AppBundle:Default:article.html.twig', array(
            'article' => $article,
        ));
    }

    public function indexAction(Request $request)
    {
        $cntxt = $this->get('security.context');
        if ($cntxt->isGranted('ROLE_USER_PAID')) {
            return $this->redirect($this->generateUrl('my_profile'));
        }

        if ($cntxt->isGranted('ROLE_USER')) {
            $session = $this->get('session');
            if ($session->get('payment')) {
                return $this->render('AppBundle:My:choose_payment.html.twig');
            }

            $region = $this->user->getRegion();
            $category = $this->user->getCategory();
            $price = $category->getPriceByRegion($region);
            $reg_info = $this->user->getRegInfo();
            $finalPrice = $price->getSum1($reg_info['with_at']);

            if ($this->user->getOffer()) {
                $offerPrice = $this->em->getRepository('AppBundle:OfferPrice')->findOneBy([
                    'category' => $category,
                    'region'   => $region,
                    'at'       => $reg_info['with_at'],
                    'offer'    => $this->user->getOffer()
                ]);

                if (!$offerPrice) {
                    throw $this->createNotFoundException('Not found offer price');
                }

                $finalPrice = $offerPrice->getPrice();
            }

            switch ($reg_info['pay_method']) {
                case 'online':
                    $text = $this->settings['first_payment_text'];
                    $sum  = $finalPrice;
                    break;
                case 'online2':
                    $text = $this->settings['first_payment_2_text'];
                    $sum  = $price->getSum2();
                    break;
/*
                case 'online3':
                    $text = $this->settings['first_payment_3_text'];
                    $sum = $price->getPriceEdu();
                    $sum = round($sum * 0.5 / 100) * 100;
                    break;
*/
                default:
                    throw $this->createNotFoundException('Not found pay method');
            }

            $form_factory = $this->container->get('form.factory');
            $form = $form_factory->createNamedBuilder('access')
                ->add('access', 'checkbox', array(
                    'required' => true,
                    'constraints' => array(new NotBlank()),
                ))
                ->add('privacy', 'checkbox', array(
                    'required' => true,
                    'constraints' => array(new NotBlank()),
                ))
                ->getForm();
            $form->handleRequest($request);
            if ($form->isValid() && !$cntxt->isGranted('ROLE_USER_PAID')) {
                $comments = array('categories' => $category->getId());
                switch ($reg_info['pay_method']) {
                    case 'online':
                        $comments['paid'] = '1,2,3';
                        break;
                    case 'online2':
                        $comments['paid'] = '1,2';
                        break;
                    case 'online3':
                        $comments['paid'] = '1';
                        break;
                    default:
                        throw $this->createNotFoundException('Not found pay method');
                }

                $promo_key = trim($request->get('promo_key'));
                $promo = $this->get('promo');
                $discount = $promo->getDiscountByKey($promo_key, 'first');

                if ($discount > 0) {
                    $sum = max($sum - $discount, 0);
                    $comments['promo_key'] = $promo_key;
                }

                if ($this->user->getOffer()) {
                    $comments['offer'] = $this->user->getOffer()->getId();
                }

                if ($sum == 0) {
                    $log = new PaymentLog();
                    $log->setUser($this->user);
                    $log->setSum($sum);
                    $log->setComment(json_encode($comments));
                    $log->setPaid(true);
                    $this->em->persist($log);

                    $this->get('promo')->activateKey($promo_key, 'first', $this->getUser()->getId());

                    switch ($reg_info['pay_method']) {
                        case 'online':
                            $this->user->addRole('ROLE_USER_PAID');
                            $this->user->setPayment1Paid(new \DateTime());
                            $this->user->addRole('ROLE_USER_PAID2');
                            $this->user->setPayment2Paid(new \DateTime());
                            $this->user->addRole('ROLE_USER_PAID3');
                            $this->user->setPayment3Paid(new \DateTime());
                            break;
                        case 'online2':
                            $this->user->addRole('ROLE_USER_PAID');
                            $this->user->setPayment1Paid(new \DateTime());
                            $this->user->addRole('ROLE_USER_PAID2');
                            $this->user->setPayment2Paid(new \DateTime());
                            break;
                        case 'online3':
                            $this->user->addRole('ROLE_USER_PAID');
                            $this->user->setPayment1Paid(new \DateTime());
                            break;
                        default:
                            throw $this->createNotFoundException('Not found pay method');
                    }
                    $this->user->setPayment1Paid(new \DateTime());
                    $this->em->persist($this->user);

                    $this->em->flush();

                    $authManager = $this->get('security.authentication.manager');
                    $token = $cntxt->getToken();
                    $token->setUser($this->user);
                    $token = $authManager->authenticate($token);
                    $cntxt->setToken($token);

                    $this->get('app.notify')->sendAfterFirstPayment($this->user);

                    return $this->redirect($this->generateUrl('homepage'));
                }

                $session->set('payment', array(
                    'sum'     => $sum,
                    'comment' => $comments,
                    'promo_key' => $promo_key,
                ));
                $session->save();
                return $this->redirect($this->generateUrl('homepage'));
            }

            $sex = $this->user->getSex();
            $dear = ($sex ? ($sex == 'female' ? 'Уважаемая' : 'Уважаемый') : 'Уважаемый/ая');
            $text = str_replace('{{ dear }}', $dear, $text);
            $text = str_replace('{{ last_name }}', $this->user->getLastName(), $text);
            $text = str_replace('{{ first_name }}', $this->user->getFirstName(), $text);
            $text = str_replace('{{ patronymic }}', $this->user->getPatronymic(), $text);
            $text = str_replace('{{ demo_period }}', $this->settings['access_time_after_2_payment'], $text);

            return $this->render('AppBundle:Default:index.html.twig', array(
                'text' => $text,
                'sum'  => $sum,
                'form' => $form->createView(),
            ));
        } else {
            $regions_repo = $this->em->getRepository('AppBundle:Region');
            $region = $regions_repo->findOneBy(array());

            if (!$region) {
                throw $this->createNotFoundException('Not found default region');
            }

            $categories_repo = $this->em->getRepository('AppBundle:Category');
            $categories_orig = $categories_repo->createQueryBuilder('c')
                ->leftJoin('c.categories_prices', 'cp', 'WITH', 'cp.active = :active')->setParameter(':active', true)
                ->andWhere('cp.region = :region')->setParameter(':region', $region)
                ->getQuery()->execute();

            $icm = $this->get('liip_imagine.cache.manager');

            $categories = array();
            foreach ($categories_orig as $category) { /** @var $category \My\AppBundle\Entity\Category */
                $image = '';
                if ($category->getImage()) {
                    $image = $icm->getBrowserPath($category->getImage()->getWebPath(), 'category');
                }
                $price = $category->getPriceByRegion($region);
                $categories[$category->getId()] = array(
                    'id'        => $category->getId(),
                    'image'     => $image,
                    'name'      => $category->getName(),
                    'theory'    => $category->getTheory(),
                    'practice'  => $category->getPractice(),
                    'training'  => $category->getTraining(),
                    'price'     => CategoryPrice::getSumView($price->getSum()),
                    'price_dsc' => CategoryPrice::getSumView($price->getSum1(false)),
                );
            }

            $faqs = $this->em->getRepository('AppBundle:Faq')->createQueryBuilder('f')
                ->addOrderBy('f.position', 'ASC')
                ->getQuery()->getResult();

            $offers = $this->em->getRepository('AppBundle:Offer')->getActiveOffers(true);

            $is_video = file_exists($this->get('kernel')->getRootDir().'/../web/uploads/wysiwyg/index.mp4');

            $how_work = $this->em->getRepository('AppBundle:HowWork')->createQueryBuilder('hw')
                ->addOrderBy('hw.position')
                ->getQuery()->execute();

            $how_work_titles = array();
            foreach ($how_work as $hw) { /** @var $hw \My\AppBundle\Entity\HowWork */
                $how_work_titles[] = $hw->getTitle();
            }

            return $this->render('AppBundle:Default:index_guest.html.twig', array(
                'faqs'            => $faqs,
                'categories'      => $categories,
                'offers'          => $offers,
                'is_video'        => $is_video,
                'how_work'        => $how_work,
                'how_work_titles' => $how_work_titles,
            ));
        }
    }

    public function unsubscribePayment1PreCheckAction($email)
    {
        return $this->render('AppBundle:Default:unsubscribe_payment_1_pre_check.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribePayment1Action($email)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        if (!$user) {
            throw $this->createNotFoundException('Not found user for email "'.$email.'"');
        }

        $user->setPayment1PaidNotNotify(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->render('AppBundle:Default:unsubscribe.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribePayment2PreCheckAction($email)
    {
        return $this->render('AppBundle:Default:unsubscribe_payment_2_pre_check.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribePayment2Action($email)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        if (!$user) {
            throw $this->createNotFoundException('Not found user for email "'.$email.'"');
        }

        $user->setPayment2PaidNotNotify(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->render('AppBundle:Default:unsubscribe.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribePayment3PreCheckAction($email)
    {
        return $this->render('@App/Default/unsubscribe_payment_3_pre_check.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribePayment3Action($email)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        if (!$user) {
            throw $this->createNotFoundException('Not found user for email "'.$email.'"');
        }

        $user->setPayment3PaidNotNotify(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->render('AppBundle:Default:unsubscribe.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribeMailingPreCheckAction($email)
    {
        return $this->render('AppBundle:Default:unsubscribe_mailing_pre_check.html.twig', array(
            'email' => $email,
        ));
    }

    public function unsubscribeMailingAction($email)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        if (!$user) {
            throw $this->createNotFoundException('Not found user for email "'.$email.'"');
        }

        $user->setMailing(false);
        $this->em->persist($user);
        $this->em->flush();

        return $this->render('AppBundle:Default:unsubscribe.html.twig', array(
            'email' => $email,
        ));
    }

    // Auto mailing with special promo codes for users who didn't pay
    public function unsubscribeOverduePreCheckAction($email)
    {
        return $this->render('AppBundle:Default:unsubscribe_overdue_pre_check.html.twig', array(
            'email' => $email,
        ));
    }

    // Auto mailing with special promo codes for users who didn't pay
    public function unsubscribeOverdueAction($email)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
        if (!$user) {
            throw $this->createNotFoundException('Not found user for email "'.$email.'"');
        }

        $user->setOverdueUnsubscribed(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->render('AppBundle:Default:unsubscribe.html.twig', array(
            'email' => $email,
        ));
    }

    public function regAction(Request $request)
    {
        $session        = $request->getSession();
        $s_name         = 'reg';
        $s_data         = $session->get($s_name);
        $offerId        = 0;
        $offer          = null;
        $offerPrices    = null;
        $category_id    = 4;
        $category       = null;
        $categories     = [];
        $categories_arr = [];
        $categories_at  = [];
        $with_at        = false;

        $region = $this->em->getRepository('AppBundle:Region')->findOneBy([]);
        if (!$region) {
            throw $this->createNotFoundException('Не найден регион');
        }

        if ($request->get('offer') === '0') {
            $s_data['stage'] = 'category';
            unset($s_data['offer']);
            unset($s_data['category_id']);
            unset($s_data['with_at']);
        }

        if ($request->get('offer') > 0) {
            $s_data['offer']       = $request->get('offer');
            $s_data['stage']       = 'pay_method';
            $s_data['pay_method']  = 'online';
        }

        $categories_orig = $this->em->getRepository('AppBundle:Category')->createQueryBuilder('c')
            ->leftJoin('c.categories_prices', 'cp', 'WITH', 'cp.active = :active')
            ->setParameter(':active', true)
            ->andWhere('cp.region = :region')->setParameter(':region', $region)
            ->getQuery()->execute();

        if (isset($s_data['offer'])) {
            $offerId = $s_data['offer'];

            $offer = $this->em->getRepository('AppBundle:Offer')->findOneBy([
                'id'        => $offerId,
                'is_public' => true,
            ]);

            if (!$offer) {
                throw $this->createNotFoundException('Специальное предложение не найдено');
            }

            if (!empty($offer->getStartedAt()) and (new \DateTime() < $offer->getStartedAt())) {
                throw $this->createNotFoundException(sprintf('Время специального предложения на наступило'));
            }

            if (!empty($offer->getEndedAt()) and (new \DateTime() > $offer->getEndedAt())) {
                throw $this->createNotFoundException(sprintf('Время специального предложения закончилось'));
            }

            $offerPrices = $this->em->getRepository('AppBundle:OfferPrice')->createQueryBuilder('op')
                ->andWhere('op.region = :region')->setParameter('region', $region)
                ->andWhere('op.offer = :offer')->setParameter('offer', $offer)
                ->andWhere('op.price > :price')->setParameter('price', 0)
                ->orderBy('op.at')
                ->getQuery()->execute();

            foreach ($offerPrices as $offerPrice) { /** @var $offerPrice \My\AppBundle\Entity\OfferPrice */
                $catId = $offerPrice->getCategory()->getId();

                $categories_arr[$catId] = [
                    'image'   => $offerPrice->getCategory()->getImage(),
                    'name'    => $offerPrice->getCategory()->getName(),
                    'with_at' => $offerPrice->getCategory()->getWithAt(),
                ];

                $categories_at[$catId]['at'][]              = $offerPrice->getAt();
                $categories_arr[$catId]['transmissionType'] = $this->getTransmissionType($categories_at[$catId]['at']);
            }

            foreach ($categories_orig as $category) { /** @var $category \My\AppBundle\Entity\Category */
                $categories[$category->getId()] = $category;
            }
        } else {
            foreach ($categories_orig as $category) { /** @var $category \My\AppBundle\Entity\Category */
                $categories[$category->getId()] = $category;
                $categories_arr[$category->getId()] = array(
                    'image'   => $category->getImage(),
                    'name'    => $category->getName(),
                    'with_at' => $category->getWithAt(),
                );
            }
        }

        if ($request->get('with_at')) {
            $s_data['with_at'] = $request->get('with_at') === '1';
        }

        if (isset($s_data['with_at'])) {
            $with_at = (bool)$s_data['with_at'];
        }

        if ($request->get('category')) {
            $s_data['category_id'] = intval($request->get('category'));
        }

        if ($request->get('pay_method')) {
            $s_data['pay_method'] = $request->get('pay_method');
        }

        if (isset($s_data['category_id'])) {
            $category_id = intval($s_data['category_id']);
        }

        $category = isset($categories[$category_id]) ? $categories[$category_id] : reset($categories);
        if (!$category) {
            throw $this->createNotFoundException('Не найдена категория');
        }

        if (!isset($s_data['stage'])) {
            $s_data['stage'] = 'category';
        }

        if ($request->get('offer') > 0) {
            $s_data['category_id'] = $category->getId();

            if (isset($categories_arr[$category->getId()]['with_at'])) {
                $s_data['with_at'] = $categories_arr[$category->getId()]['with_at'];

                if ($request->get('with_at') === '0' and $s_data['with_at'] == 1) {
                    $s_data['with_at'] = 0;
                }

            } else {
                throw $this->createNotFoundException('Не найдена категория');
            }
        }

        if ($request->getQueryString() && !$request->get('act')) {
            $session->set($s_name, $s_data);
            $session->save();

            return $this->redirect($this->generateUrl('reg'));
        }

        if ($category) {
            if ('now' == $request->get('act')) {
                $s_data['stage'] = 'pay_method';
                $s_data['pay_method'] = 'online';
                $session->set($s_name, $s_data);
                $session->save();

                return $this->redirect($this->generateUrl('reg'));
            }

            if ('first' == $request->get('act')) {
                $s_data['stage'] = 'category';
                $session->set($s_name, $s_data);
                $session->save();
            }

            if ($request->getQueryString() && !$request->get('act')) {
                $session->set($s_name, $s_data);
                $session->save();

                return $this->redirect($this->generateUrl('reg'));
            }

            switch ($s_data['stage']) {
                case 'category':
                    if ($request->isMethod('post')) {
                        $s_data['stage']       = 'change_pay';
                        $s_data['category_id'] = $category->getId();
                        $s_data['with_at']     = ($category->getWithAt() && (bool)$request->get('with_at'));

                        $session->set($s_name, $s_data);
                        $session->save();

                        return $this->redirect($this->generateUrl('reg'));
                    }

                    $html = $this->renderView('AppBundle:Default:Reg/category.html.twig', array(
                        'categories'    => $categories_arr,
                        'category_id'   => $category_id,
                        'with_at'       => $with_at,
                        'categories_at' => $categories_at,
                        'offerId'       => $offerId,
                        'offerPrices'   => $offerPrices,
                    ));

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array(
                            'html' => $html,
                        ));
                    }
                    return $this->render('AppBundle:Default:Reg/wrap.html.twig', array(
                        'categories'    => $categories_arr,
                        'html'          => $html,
                        'categories_at' => $categories_at,
                        'offerId'       => $offerId,
                        'offerPrices'   => $offerPrices,
                    ));
                    break;
                case 'change_pay':
                    if ('back' == $request->get('act')) {
                        $s_data['stage'] = 'category';
                        $session->set($s_name, $s_data);
                        $session->save();

                        return $this->redirect($this->generateUrl('reg'));
                    }

                    if ($request->isMethod('post')) {
                        $s_data['stage'] = 'pay_method';
                        $s_data['pay_method'] = ('online' == $request->get('pay_method') ? 'online' : 'office');
                        $session->set($s_name, $s_data);
                        $session->save();

                        return $this->redirect($this->generateUrl('reg'));
                    }

                    $html = $this->renderView('AppBundle:Default:Reg/change_pay.html.twig', array(
                        'category' => $category,
                        'with_at'  => $category->getWithAt() && $with_at,
                    ));

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse(array(
                            'html' => $html,
                        ));
                    }

                    return $this->render('AppBundle:Default:Reg/wrap.html.twig', array(
                        'categories' => $categories_arr,
                        'html'       => $html,
                    ));
                    break;
                case 'pay_method':
                    if ('back' == $request->get('act')) {
                        $s_data['stage'] = 'change_pay';
                        $session->set($s_name, $s_data);
                        $session->save();

                        return $this->redirect($this->generateUrl('reg'));
                    }

                    if ('online' == $request->get('act')) {
                        $s_data['pay_method'] = $request->get('act');
                        $session->set($s_name, $s_data);
                        $session->save();

                        return $this->redirect($this->generateUrl('reg'));
                    }

                    if ($request->isMethod('post') and $s_data['pay_method']!='office') {
                        //$s_data['pay_method'] = 'online';
                        if (in_array($request->get('pay_method'), array('online2', 'online3'))) {
                            $s_data['pay_method'] = $request->get('pay_method');
                        }
                        $session->set($s_name, $s_data);
                        $session->save();

                        return $this->redirect($this->generateUrl('fos_user_registration_register'));
                    }

                    $with_at    = ($category->getWithAt() && $with_at);
                    $price      = $category->getPriceByRegion($region);
                    $finalPrice = $price->getSum($with_at);

                    if ($offer) {
                        $offerPrice = $this->em->getRepository('AppBundle:OfferPrice')->findOneBy([
                            'category' => $category->getId(),
                            'region'   => $region->getId(),
                            'at'       => $with_at,
                            'offer'    => $offer->getId(),
                        ]);

                        if (!$offerPrice) {
                            throw $this->createNotFoundException('Не найдена цена специального предложения');
                        }

                        $finalPrice = $offerPrice->getPrice();
                    }

                    $until = null;
                    if (isset($offer) and $offer->getEndedAt()) {
                        $endTime = $offer->getEndedAt();
                        $remTime = $endTime ? $endTime->diff(new \DateTime('now')) : null;
                        $until   = Time::getAllSeconds($remTime);
                    }

                    if (in_array($s_data['pay_method'], array('online', 'online2', 'online3'))) {
                        $html = $this->renderView('AppBundle:Default:Reg/pay_online.html.twig', array(
                                'category'    => $category,
                                'price'       => $price,
                                'with_at'     => $with_at,
                                'until'       => $until,
                                'offer'       => $offer,
                                'final_price' => $finalPrice,
                                'offerId'     => $offerId,
                        ));
                        if ($request->isXmlHttpRequest()) {
                                return new JsonResponse(array(
                                    'online'  => true,
                                    'html'    => $html,
                                    'offerId' => $offerId,
                                ));
                        }
                        return $this->render('AppBundle:Default:Reg/wrap.html.twig', array(
                            'categories' => $categories_arr,
                            'html'       => $html,
                            'offerId'    => $offerId,
                        ));
                    } else {
                        $html = $this->renderView('AppBundle:Default:Reg/pay_office.html.twig', array(
                            'cur_price' => $finalPrice,
                            'category'  => $category,
                            'with_at'   => $with_at,
                            'offerId'   => $offerId,
                        ));
                        if ($request->isXmlHttpRequest()) {
                            return new JsonResponse(array(
                                'html'    => $html,
                                'offerId' => $offerId,
                            ));
                        }
                        return $this->render('AppBundle:Default:Reg/wrap.html.twig', array(
                            'categories'   => $categories_arr,
                            'html'         => $html,
                        ));
                    }
                    break;
            }
        }

        $session->remove($s_name);
        $session->save();

        return $this->redirect($this->generateUrl('reg'));
    }

    public function regOfflinePrintAction(Request $request)
    {
        $session = $request->getSession();
        $s_name = 'reg';
        $s_data = $session->get($s_name);

        if (!$s_data) {
            return $this->redirect($this->generateUrl('reg'));
        }

        $region = $this->em->getRepository('AppBundle:Region')->findOneBy(array());
        if (!$region) {
            throw $this->createNotFoundException('Not found default region');
        }

        /** @var $category \My\AppBundle\Entity\Category */
        $category_id = isset($s_data['category_id']) ? intval($s_data['category_id']) : 0;
        $category = $this->em->getRepository('AppBundle:Category')->createQueryBuilder('c')
            ->leftJoin('c.categories_prices', 'cp', 'WITH', 'cp.active = :active')->setParameter(':active', true)
            ->andWhere('cp.region = :region')->setParameter(':region', $region)
            ->andWhere('c.id = :category_id')->setParameter(':category_id', $category_id)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if (!$category) {
            throw $this->createNotFoundException('Not found category');
        }

        if (!isset($s_data['with_at'])) {
            throw $this->createNotFoundException('Not required params');
        }

        $with_at    = ($category->getWithAt() && $s_data['with_at']);
        $price      = $category->getPriceByRegion($region);
        $finalPrice = $price->getSum($with_at);
        $offerId    = 0;

        if (isset($s_data['offer'])) {
            $offerPrice = $this->em->getRepository('AppBundle:OfferPrice')->findOneBy([
                'category' => $category,
                'region'   => $region,
                'at'       => $with_at,
                'offer'    => $s_data['offer'],
            ]);

            if (!$offerPrice) {
                throw $this->createNotFoundException('Not found offer price');
            }

            $finalPrice = $offerPrice->getPrice();
            $offerId = $s_data['offer'];
        }

        return $this->render('AppBundle:Default/Reg:offline_print.html.twig', array(
            'cur_price' => $finalPrice,
            'category'  => $category,
            'with_at'   => $with_at,
            'offerId'   => $offerId,
        ));
    }

    public function sendFeedbackAction(Request $request)
    {
        $fb = $this->get('form.factory')->createNamedBuilder('sendmail');
        if (!$this->user) {
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
        if (!$this->user) {
            $fb->add('captcha', 'innocead_captcha', array('constraints' => array(
                new NotBlank(),
                new Captcha(),
            )));
        }
        $form = $fb->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            if (!$this->user) {
                $from = array($form->get('email')->getData() => $form->get('name')->getData());
            } else {
                $from = array($this->user->getEmail() => $this->user->getFullName());
            }

            /** @var $email \Swift_Mime_Message */
            $email = \Swift_Message::newInstance()
                ->setFrom($from)
                ->setTo('avtoschkola.mgtu@yandex.ru')
                ->setSubject('АО - Обратная связь')
                ->setBody($form->get('message')->getData())
            ;
            $this->get('swiftmailer.mailer.directly_mailer')->send($email);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'success' => true,
                ));
            }

            return $this->redirect($this->generateUrl('homepage'));
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'errors' => $this->getErrorMessages($form),
            ));
        }

        return $this->render('AppBundle:Default:send_feedback.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function contactsAction()
    {
        return $this->render('AppBundle:Default:contacts.html.twig');
    }

    public function branchesAction()
    {
        $region = $this->em->getRepository('AppBundle:Region')->findOneBy(array());

        if (!$region) {
            throw $this->createNotFoundException('Not found default region');
        }

        $filials = $this->em->getRepository('AppBundle:Filial')->findAll();

        return $this->render('AppBundle:Default:branches.html.twig', array(
            'map_path' => '/uploads/images/filial_map.'.$region->getId().'.png',
            'filials'  => $filials,
        ));
    }

    public function getBranchAction($url)
    {
        $filial = $this->em->getRepository('AppBundle:Filial')->findOneBy(array('url' => $url));
        if (!$filial) {
            throw $this->createNotFoundException('Filial not found');
        }

        return $this->render('AppBundle:Default:_branch.html.twig', array(
            'filial' => $filial,
        ));
    }

    public function branchPrintAction($url)
    {
        $filial = $this->em->getRepository('AppBundle:Filial')->findOneBy(array('url' => $url));
        if (!$filial || !$filial->getActive()) {
            throw $this->createNotFoundException('Filial not found');
        }

        return $this->render('AppBundle:Default:branch_print.html.twig', array(
            'filial' => $filial,
        ));
    }

    public function sitesAction()
    {
        $region = $this->em->getRepository('AppBundle:Region')->findOneBy(array());

        if (!$region) {
            throw $this->createNotFoundException('Not found default region');
        }

        $sites = $this->em->getRepository('AppBundle:Site')->findAll();

        return $this->render('AppBundle:Default:sites.html.twig', array(
            'map_path' => '/uploads/images/site_map.'.$region->getId().'.png',
            'sites'  => $sites,
        ));
    }

    public function getSiteAction($url)
    {
        $site = $this->em->getRepository('AppBundle:Site')->findOneBy(array('url' => $url));
        if (!$site) {
            throw $this->createNotFoundException('Site not found');
        }

        return $this->render('AppBundle:Default:_site.html.twig', array(
            'site' => $site,
        ));
    }

    public function faqAction()
    {
        $faqs = $this->em->getRepository('AppBundle:Faq')->findBy(array(), array('position' => 'ASC'));

        return $this->render('AppBundle:Default:faq.html.twig', array(
            'faqs' => $faqs,
        ));
    }

    public function newsAction(Request $request)
    {
        $qb = $this->em->getRepository('AppBundle:News')->createQueryBuilder('n')
            ->andWhere('n.publish = :is_publish')->setParameter(':is_publish', true)
            ->addOrderBy('n.created_at', 'DESC')
        ;

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Default:news.html.twig', array(
            'pagerfanta' => $pagerfanta,
        ));
    }

    public function newsShowAction($id)
    {
        $news_repo = $this->em->getRepository('AppBundle:News');

        $news = $news_repo->find($id);
        if (!$news or !$news->getPublish()) {
            throw $this->createNotFoundException();
        }

        $other_news = $news_repo->createQueryBuilder('n')
            ->andWhere('n.id != :id')->setParameter(':id', $id)
            ->andWhere('n.publish = :is_publish')->setParameter(':is_publish', true)
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->execute();

        return $this->render('AppBundle:Default:news_show.html.twig', array(
            'news'       => $news,
            'other_news' => $other_news,
        ));
    }

    public function offersAction()
    {
        if ($this->is_strange) {
            throw $this->createNotFoundException();
        }

        $offers = $this->em->getRepository('AppBundle:Offer')->getActiveOffers(true);

        foreach ($offers as &$offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $description = $offer->getDescription();

            if (preg_match_all('/{{ link_([0-9]+) }}/', $description, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $key => $id) {
                        $link = $this->generateUrl('reg', ['offer' => $id], true);
                        $description = str_replace($matches[0][$key], $link, $description);
                    }
                }
            }

            if (preg_match_all('/{{ link_([0-9]+)_([0-9]+)_([0-9]+) }}/', $description, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $key => $id) {
                        $link = $this->generateUrl(
                            'reg',
                            [
                                'offer'    => $matches[1][$key],
                                'category' => $matches[2][$key],
                                'with_at'  => $matches[3][$key],
                            ],
                            true
                        );
                        $description = str_replace($matches[0][$key], $link, $description);
                    }
                }
            }

            $offer->setDescription($description);
        }

        return $this->render('AppBundle:Default:offers.html.twig', array(
            'offers' => $offers,
        ));
    }

    public function studentsAction()
    {
        if ($this->is_strange) {
            throw $this->createNotFoundException();
        }

        return $this->render('AppBundle:Default:offers_only_student.html.twig', array(
            'offers' => $this->em->getRepository('AppBundle:OfferStudent')->getActiveOffers(true),
        ));
    }

    public function checkStudentsOffersAction()
    {
        $offers = $this->em->getRepository('AppBundle:OfferStudent')->findAll();

        if (count($offers) == 0) {
            $status = 'empty';
        } else {
            $status = 'full';
        }

        return $this->render('AppBundle:Default:offers_only_student_check.html.twig', array(
            'status' => $status,
        ));
    }

    public function reviewsAction(Request $request)
    {
        $fb = $this->get('form.factory')->createNamedBuilder('review');
        if (!$this->user) {
            $fb->add('sex', 'choice', array(
                'expanded' => true,
                'choices'  => array(
                    'male'   => 'male',
                    'female' => 'female',
                )
            ));
            $fb->add('fio', 'text', array(
                'label'       => 'Ф.И.О.',
                'constraints' => array(new NotBlank()),
            ));
            $fb->add('email', 'email', array(
                'label'       => 'E-mail',
                'constraints' => array(
                    new NotBlank(),
                    new Email(),
                ),
            ));
            $fb->add('category', 'entity', array(
                'label'         => 'Категория',
                'empty_value'   => 'choose_option',
                'class'         => 'AppBundle:Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->addOrderBy('c.name');
                },
                'constraints'   => array(new NotBlank()),
            ));
        }
        $fb->add('message', 'textarea', array(
            'label'       => 'Сообщение',
            'constraints' => array(
                new NotBlank(),
                new Length(array('min' => 50)),
            ),
        ));
        $form = $fb->getForm();

        if ($request->isMethod('post') && $request->isXmlHttpRequest()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $review = new Review();
                if ($this->user) {
                    $review->setUser($this->user);
                } else {
                    $review->setSex($form->get('sex')->getData());
                    $fio = array_pad(explode(' ', $form->get('fio')->getData()), 3, '');
                    $review->setLastName(!empty($fio[1]) ? $fio[0] : $fio[1]);
                    $review->setFirstName(empty($fio[1]) ? $fio[0] : $fio[1]);
                    $review->setPatronymic($fio[2]);
                    $review->setEmail($form->get('email')->getData());
                    $review->setCategory($form->get('category')->getData());
                }
                $review->setMessage($form->get('message')->getData());
                $this->em->persist($review);

                if (!$this->user) {
                    if ($review->getPhoto()) {
                        $review->getPhoto()->setReview(null);
                    }
                    $photo_id = intval($request->getSession()->get('image_id'));
                    $photo = $this->em->getRepository('AppBundle:Image')->find($photo_id);
                    if ($photo) {
                        $photo->setReview($review);
                    }
                    $request->getSession()->remove('image_id');
                }

                $this->em->flush();

                return new JsonResponse(array('success' => true));
            } else {
                return new JsonResponse(array('errors' => $this->getErrorMessages($form, 'review')));
            }
        }

        $qb = $this->em->getRepository('AppBundle:Review')->createQueryBuilder('r')
            ->andWhere('r.moderated = :is_moderated')->setParameter(':is_moderated', true)
            ->addOrderBy('r.created_at', 'DESC')
        ;

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Default:reviews.html.twig', array(
            'pagerfanta' => $pagerfanta,
            'form'       => $form->createView(),
            'photoForm'  => $this->createForm(new ImageFormType(), new Image())->createView(),
        ));
    }

    public function officesAction()
    {
        $offices = $this->em->getRepository('AppBundle:Office')->createQueryBuilder('o')
            ->andWhere('o.active = :active')->setParameter(':active', true)
            ->addOrderBy('o.region')
            ->addOrderBy('o.position')
            ->getQuery()->execute();

        return $this->render('AppBundle:Default:offices.html.twig', array(
            'offices' => $offices,
        ));
    }

    public function officePrintAction($id)
    {
        $office = $this->em->getRepository('AppBundle:Office')->find($id);
        if (!$office || !$office->getActive()) {
            throw $this->createNotFoundException('Office not found');
        }

        return $this->render('AppBundle:Default:office_print.html.twig', array(
            'office' => $office,
        ));
    }

    public function nalogAction()
    {
        return $this->render('AppBundle:Default:nalog.html.twig');
    }

    public function payAction($type)
    {
        $cntxt = $this->get('security.context');
        if (!$cntxt->isGranted('ROLE_USER')) {
            throw $this->createNotFoundException();
        }

        if (!in_array($type, array('psb', 'robokassa'))) {
            throw $this->createNotFoundException();
        }

        $session = $this->get('session');
        $payment = $session->get('payment');
        $userId = $this->getUser()->getId();
        if (!$payment || !isset($payment['sum']) || !isset($payment['comment'])) {
            throw $this->createNotFoundException();
        }

        if (isset($payment['promo_key'])) {
            $this->get('promo')->writeRezervInKey($payment['promo_key'], 'first', $userId);
        }

        $log = new PaymentLog();
        $log->setUser($this->user);
        $log->setSum($payment['sum']);
        $log->setSType($type);
        $log->setComment(json_encode($payment['comment']));
        $this->em->persist($log);
        $this->em->flush();

        switch ($type) {
            case 'psb':
                return $this->redirect($this->generateUrl('psb_query', array(
                    'id'  => $log->getId(),
                    'uid' => $this->user->getId()
                )));
            case 'robokassa':
                return $this->forward('PaymentBundle:Robokassa:query', array(
                    'id'  => $log->getId(),
                    'uid' => $this->user->getId(),
                    'sum' => $payment['sum'],
                ));
            default:
                throw $this->createNotFoundException();
        }
    }

    public function imageAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $result = array();

        if ($request->isMethod('post')) {
            $form = $this->createForm(new ImageFormType(), new Image());
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var $image \My\AppBundle\Entity\Image */
                $image = $form->getData();
                $this->em->persist($image);
                $this->em->flush();

                $request->getSession()->set('image_id', $image->getId());

                $result['image_src'] = $this->get('liip_imagine.cache.manager')
                    ->getBrowserPath($image->getWebPath(), $request->get('filter', 'no_filter'));
            } else {
                foreach ($form->getErrors() as $error) {
                    $result['errors'][] = $error->getMessage();
                }
            }
        } else {
            $result['errors'][] = $this->get('translator')->trans('errors.not_post');
        }

        return new JsonResponse($result);
    }

    public function promoAction()
    {
        $response = new RedirectResponse($this->generateUrl('homepage'));
        $response->headers->setCookie(new Cookie('by_groupon', true, time()+60*60*24*20));
        return $response;
    }

    /**
     * @param $form \Symfony\Component\Form\Form
     * @param $name string
     *
     * @return array
     */
    protected function getErrorMessages($form, $name = '')
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }
            $errors[$name] = $error->getMessage();
        }
        if ($form->count()) {
            foreach ($form as $child) { /** @var $child \Symfony\Component\Form\Form */
                if (!$child->isValid()) {
                    $cname = ($name ? $name.'_' : '').$child->getName();
                    $errors = array_merge($errors, $this->getErrorMessages($child, $cname));
                }
            }
        }
        return $errors;
    }

    private function getTransmissionType($arr)
    {
        if (count($arr) == 2) {
            return 'automatic&manual';
        } elseif (isset($arr[0])) {
            return $arr[0] ? 'automatic' : 'manual';
        }

        return false;
    }
}
