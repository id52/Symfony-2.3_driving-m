<?php

namespace My\AppBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\SupportDialog;
use My\AppBundle\Entity\SupportMessage;
use My\AppBundle\Entity\User;
use My\AppBundle\Form\Type\ProfileFormType;
use My\AppBundle\Form\Type\SimpleProfileFormType;
use My\AppBundle\Pagerfanta\Pagerfanta;
use My\AppBundle\Util\SubjectTrait;
use My\PaymentBundle\Entity\Log as PaymentLog;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends AbstractSettingsController
{
    use SubjectTrait;

    protected $perms = array('ROLE_MOD');
    protected $routerSettings = 'admin_users_settings';
    protected $tmplSettings = 'User/settings.html.twig';

    public function profileUserViewAction($id, $company)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_MOD_PARADOX_USERS')
            && false === $this->get('security.context')->isGranted('ROLE_MOD_ADD_USER')
        ) {
            throw $this->createNotFoundException();
        }

        $userRepo = $this->em->getRepository('AppBundle:User');
        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t moderate yourself');
        }

        return $this->render('AppBundle:Admin/User:profile_user_view.html.twig', array(
            'user'        => $user,
            'company'     => $company
        ));
    }

    public function addAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_MOD_ADD_USER')) {
            throw $this->createNotFoundException();
        }

        $validSubjects = $this->getValidSubjects();

        $userManager = $this->get('fos_user.user_manager');

        $region_places = array();
        $region_places_source = $this->em->getRepository('AppBundle:RegionPlace')->createQueryBuilder('rp')
            ->leftJoin('rp.region', 'r')
            ->getQuery()->getResult();
        foreach ($region_places_source as $rp) { /** @var $rp \My\AppBundle\Entity\RegionPlace */
            if (!isset($region_places[$rp->getRegion()->getId()])) {
                $region_places[$rp->getRegion()->getId()] = array();
            }
            $region_places[$rp->getRegion()->getId()][$rp->getId()] = $rp->getName();
        }

        $region_categories = $this->getRegionCategories();

        $paids = array(
            'paid_3_f' => 'paids.paid_3_f',
        );

        $offers = $this->em->getRepository('AppBundle:Offer')->getActiveOffers();
        $offersStudents = $this->em->getRepository('AppBundle:OfferStudent')->getActiveOffers();

        foreach ($offers as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $paids[$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
        }
        foreach ($offersStudents as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $paids[$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
        }

        $closedSubjects = $this->em->getRepository('AppBundle:Subject')->findAll();

        $closedSubjectsChoices = [];
        foreach ($closedSubjects as $closedSubject) {
            $closedSubjectsChoices[$closedSubject->getId()] = $closedSubject->getTitle();
        }

        /** @var $user \My\AppBundle\Entity\User */
        $user = $userManager->createUser();
        $user->setCloseFinalExam(true);
        /** @var $form \Symfony\Component\Form\Form */

        $formType = new ProfileFormType();
        $form = $this->createForm($formType, $user, array(
            'validation_groups'  => array('profile', 'Registration'),
            'translation_domain' => 'profile',
        ))
            ->add('email', 'email')
            ->add('moderated', 'checkbox', array('required' => false))
            ->add('paid', 'choice', array(
                'mapped'      => false,
                'empty_value' => 'choose_option',
                'choices'     => $paids,
            ))
            ->add('category', 'entity', array(
                'class'       => 'AppBundle:Category',
                'required'    => true,
                'empty_value' => 'choose_option',
            ))
            ->add('region', 'entity', array(
                'class'       => 'AppBundle:Region',
                'required'    => true,
                'empty_value' => 'choose_option',
            ))
            ->add('with_at', 'checkbox', array(
                'required' => false,
                'mapped'   => false,
            ))
            ->add('region_place', 'entity', array(
                'empty_value' => 'choose_option',
                'class'       => 'AppBundle:RegionPlace',
                'required'    => false,
            ))
            ->add('paradox_id', 'integer', array(
                'required' => false,
            ))
            ->add('webgroup', 'entity', array(
                'empty_value' => 'choose_option',
                'class'       => 'AppBundle:Webgroup',
                'required'    => false,
            ))
            ->add('close_final_exam', 'checkbox', array(
                'required' => false,
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
        ;

        if ($request->isMethod('post')) {
            $profile = $request->get('profile');

            $region = null;
            if (isset($profile['region'])) {
                $region = $this->em->getRepository('AppBundle:Region')->find($profile['region']);
            }

            $category = null;
            if (isset($profile['category'])) {
                $category = $this->em->getRepository('AppBundle:Category')->find($profile['category']);
            }

            if ($region and $category and isset($profile['closed_subjects'])) {
                $closedSubjectIds = $profile['closed_subjects'];

                foreach ($closedSubjectIds as $closedSubjectId) {
                    $closedSubject = $this->em->getRepository('AppBundle:Subject')->find($closedSubjectId);
                    if ($closedSubject) {
                        if (!isset($validSubjects[$region->getId()][$category->getId()][$closedSubject->getId()])
                        ) {
                            $form->get('closed_subjects')->addError(new FormError('Нет предмета'));
                        }
                    }
                }
            }

            $plainPassword = $this->generatePassword();
            $user->setPlainPassword($plainPassword);

            $form->handleRequest($request);

            $validator = $this->get('validator');

            $not_registration = $form->get('not_registration')->getData();
            if ($not_registration) {
                $names = array(
                    'place_country',
                    'place_region',
                    'place_city',
                    'place_street',
                    'place_house',
                );
            } else {
                $names = array(
                    'registration_country',
                    'registration_region',
                    'registration_city',
                    'registration_street',
                    'registration_house',
                );
            }
            foreach ($names as $name) {
                $field = $form->get($name);
                $errors = $validator->validateValue($field->getData(), new Assert\NotBlank());
                if (count($errors) > 0) {
                    $field->addError(new FormError($errors->get(0)->getMessage()));
                }
            }

            /** @var $region \My\AppBundle\Entity\Region */
            $region = $form->get('region')->getData();
            if ($region) {
                if (!empty($region_places[$region->getId()])) {
                    $field = $form->get('region_place');
                    /** @var $region_place \My\AppBundle\Entity\RegionPlace */
                    $region_place = $field->getData();
                    if (!$region_place || !isset($region_places[$region->getId()][$region_place->getId()])) {
                        $field->addError(new FormError('Выберите один из предложенных вариантов.'));
                    }
                }
            }

            if ($form->isValid()) {
                $closedSubjectIds = $form->get('closed_subjects')->getData();

                foreach ($closedSubjectIds as $closedSubjectId) {
                    $closedSubject = $this->em->getRepository('AppBundle:Subject')->find($closedSubjectId);
                    if ($closedSubject) {
                        $user->addClosedSubject($closedSubject);
                        $closedSubject->addClosedUser($user);
                        $this->em->persist($closedSubject);
                    }
                }

                $user->addRole('ROLE_USER_FULL_PROFILE');
                $user->setNotAllMailing(false);
                $user->setConfirmationToken(null);
                $user->setEnabled($user->getModerated());
                $user->setOffline(true);
                if (!$user->getModerated()) {
                    $user->setPhoneMobileStatus('sended');
                } else {
                    $user->setPhoneMobileStatus('confirmed');
                }

                $paid = $form->get('paid')->getData();
                $moderatorId = ($this->getUser() instanceof User) ? $this->getUser()->getId() : null;
                $region = $user->getRegion();
                $category = $user->getCategory();
                $price = $category->getPriceByRegion($region);
                $with_at = ($category->getWithAt() && $form->get('with_at')->getData());
                $user->setRegInfo(array('with_at' => $with_at));
                $is_offer = (!in_array($paid, ['paid_1', 'paid_2', 'paid_3', 'paid_3_f']) && isset($paids[$paid]));

                //set roles
                if ($paid == 'paid_2' || $paid == 'paid_3' || $paid == 'paid_3_f' || $is_offer) {
                    $user->addRole('ROLE_USER_PAID');
                    $user->setPayment1Paid(new \DateTime());
                    $user->setPayment1PaidNotNotify(false);
                    $user->addRole('ROLE_USER_PAID2');
                    $user->setPayment2Paid(new \DateTime());
                    $user->setPayment2PaidNotNotify(false);
                }
                if ($paid == 'paid_3' || $paid == 'paid_3_f' || $is_offer) {
                    $user->addRole('ROLE_USER_PAID3');
                    $user->setPayment3Paid(new \DateTime());
                    $user->setPayment3PaidNotNotify(false);
                }

                if ($paid == 'paid_2' || $paid == 'paid_3' || $paid == 'paid_3_f' || $is_offer) {
                    $sum = 0;
                    $comment = array(
                        'categories'   => $category->getId(),
                        'moderator_id' => $moderatorId,
                        'paid'         => '',
                    );
                    if ($paid == 'paid_2') {
                        $sum = $price->getSum2();
                        $comment['paid'] = '1,2';
                    }
                    if ($paid == 'paid_3') {
                        $sum = $price->getSum1($with_at);
                        $comment['paid'] = '1,2,3';
                    }
                    if ($paid == 'paid_3_f') {
                        $sum = $price->getSum($with_at);
                        $comment['paid'] = '1,2,3';
                    }
                    if ($is_offer) {
                        $offer = $this->em->find('AppBundle:Offer', $paid);
                        if ($offer) {
                            $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
                            $comment['paid'] = '1,2,3';
                            $comment['offer_id'] = $offer->getId();
                        }
                        if (!$offer) {
                            $offer = $this->em->find('AppBundle:OfferStudent', $paid);

                            if ($offer) {
                                $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
                                $comment['paid'] = '1,2,3';
                                $comment['offer_student_id'] = $offer->getId();
                            }
                        }
                    }

                    $log = new PaymentLog();
                    $log->setUser($user);
                    $log->setSum($sum);
                    $log->setPaid(true);
                    $log->setComment(json_encode($comment));
                    $this->em->persist($log);
                }

                $userManager->updateUser($user);
                $this->em->flush(); //save logs etc

                if (!$user->getModerated()) {
                    $this->container->get('app.user_helper')->sendMessages($user, $plainPassword, true);
                }

                $this->get('session')->getFlashBag()->add('success', 'success_added');

                if ($this->get('security.context')->isGranted('ROLE_MOD_PARADOX_USERS')) {
                    $url = $this->generateUrl('admin_paradox_user_view', array('id' => $user->getId()));
                } else {
                    $url = $this->generateUrl('admin_users_add');
                }
                return $this->redirect($url);
            }
        }

        return $this->render('AppBundle:Admin:User/add.html.twig', array(
            'form'               => $form->createView(),
            'region_places_tree' => $region_places,
            'region_categories'  => $region_categories,
            'validSubjects'      => $validSubjects,
        ));
    }

    public function addSimpleAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_MOD_ADD_USER')) {
            throw $this->createNotFoundException();
        }

        $validSubjects = $this->getValidSubjects();

        $userManager = $this->get('fos_user.user_manager');
        /** @var $user \My\AppBundle\Entity\User */
        $user = $userManager->createUser();
        $user->setCloseFinalExam(true);

        $paids = array(
            'paid_3_f' => 'paids.paid_3_f',
        );

        $offers = $this->em->getRepository('AppBundle:Offer')->getActiveOffers();
        $offersStudents = $this->em->getRepository('AppBundle:OfferStudent')->getActiveOffers();

        foreach ($offers as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $paids[$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
        }
        foreach ($offersStudents as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $paids[$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
        }

        $closedSubjects = $this->em->getRepository('AppBundle:Subject')->findAll();

        $closedSubjectsChoices = [];
        foreach ($closedSubjects as $closedSubject) {
            $closedSubjectsChoices[$closedSubject->getId()] = $closedSubject->getTitle();
        }

        $formType = new SimpleProfileFormType();
        $formType->setPaids($paids);

        $form = $this->createForm($formType, $user, array(
            'translation_domain' => 'simple_profile',
        ));

        if ($request->isMethod('post')) {
            $simpleProfile = $request->get('simple_profile');

            $region = null;
            if (isset($simpleProfile['region'])) {
                $region = $this->em->getRepository('AppBundle:Region')->find($simpleProfile['region']);
            }

            $category = null;
            if (isset($simpleProfile['category'])) {
                $category = $this->em->getRepository('AppBundle:Category')->find($simpleProfile['category']);
            }

            if ($region and $category and isset($simpleProfile['closed_subjects'])) {
                $closedSubjectIds = $simpleProfile['closed_subjects'];

                foreach ($closedSubjectIds as $closedSubjectId) {
                    $closedSubject = $this->em->getRepository('AppBundle:Subject')->find($closedSubjectId);
                    if ($closedSubject) {
                        if (!isset($validSubjects[$region->getId()][$category->getId()][$closedSubject->getId()])
                        ) {
                            $form->get('closed_subjects')->addError(new FormError('Нет предмета'));
                        }
                    }
                }
            }

            $plainPassword = $this->generatePassword();
            $user->setPlainPassword($plainPassword);

            $form->handleRequest($request);
            if ($form->isValid()) {
                $closedSubjectIds = $form->get('closed_subjects')->getData();
                foreach ($closedSubjectIds as $closedSubjectId) {
                    $closedSubject = $this->em->getRepository('AppBundle:Subject')->find($closedSubjectId);
                    if ($closedSubject) {
                        $user->addClosedSubject($closedSubject);
                        $closedSubject->addClosedUser($user);
                        $this->em->persist($closedSubject);
                    }
                }

                $user->setNotAllMailing(false);
                $user->setConfirmationToken(null);
                $user->setEnabled(false);
                $user->setOffline(true);
                $user->setPhoneMobileStatus('sended');

                $paid = $form->get('paid')->getData();
                $moderatorId = ($this->getUser() instanceof User) ? $this->getUser()->getId() : null;
                $region = $user->getRegion();
                $category = $user->getCategory();
                $price = $category->getPriceByRegion($region);
                $with_at = ($category->getWithAt() && $form->get('with_at')->getData());
                $user->setRegInfo(array('with_at' => $with_at));
                $is_offer = (!in_array($paid, ['paid_1', 'paid_2', 'paid_3', 'paid_3_f']) && isset($paids[$paid]));

                //set roles
                if ($paid == 'paid_2' || $paid == 'paid_3' || $paid == 'paid_3_f' || $is_offer) {
                    $user->addRole('ROLE_USER_PAID');
                    $user->setPayment1Paid(new \DateTime());
                    $user->setPayment1PaidNotNotify(false);
                    $user->addRole('ROLE_USER_PAID2');
                    $user->setPayment2Paid(new \DateTime());
                    $user->setPayment2PaidNotNotify(false);
                }
                if ($paid == 'paid_3' || $paid == 'paid_3_f' || $is_offer) {
                    $user->addRole('ROLE_USER_PAID3');
                    $user->setPayment3Paid(new \DateTime());
                    $user->setPayment3PaidNotNotify(false);
                }

                if ($paid == 'paid_2' || $paid == 'paid_3' || $paid == 'paid_3_f' || $is_offer) {
                    $sum = 0;
                    $comment = array(
                        'categories'   => $category->getId(),
                        'moderator_id' => $moderatorId,
                        'paid'         => '',
                    );
                    if ($paid == 'paid_2') {
                        $sum = $price->getSum2();
                        $comment['paid'] = '1,2';
                    }
                    if ($paid == 'paid_3') {
                        $sum = $price->getSum1($with_at);
                        $comment['paid'] = '1,2,3';
                    }
                    if ($paid == 'paid_3_f') {
                        $sum = $price->getSum($with_at);
                        $comment['paid'] = '1,2,3';
                    }
                    if ($is_offer) {
                        $offer = $this->em->find('AppBundle:Offer', $paid);
                        if ($offer) {
                            $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
                            $comment['paid'] = '1,2,3';
                            $comment['offer_id'] = $offer->getId();
                        }

                        if (!$offer) {
                            $offer = $this->em->find('AppBundle:OfferStudent', $paid);

                            if ($offer) {
                                $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
                                $comment['paid'] = '1,2,3';
                                $comment['offer_student_id'] = $offer->getId();
                            }
                        }
                    }

                    $log = new PaymentLog();
                    $log->setUser($user);
                    $log->setSum($sum);
                    $log->setPaid(true);
                    $log->setComment(json_encode($comment));
                    $this->em->persist($log);
                }

                $userManager->updateUser($user);
                $this->em->flush(); //save logs etc

                if (!$user->getModerated()) {
                    $this->container->get('app.user_helper')->sendMessages($user, $plainPassword, true);
                }

                $this->get('session')->getFlashBag()->add('success', 'success_added');

                if ($this->get('security.context')->isGranted('ROLE_MOD_PARADOX_USERS')) {
                    $url = $this->generateUrl('admin_paradox_user_view', array('id' => $user->getId()));
                } else {
                    $url = $this->generateUrl('admin_users_add_simple');
                }
                return $this->redirect($url);
            }
        }

        return $this->render('AppBundle:Admin:User/add_simple.html.twig', array(
            'form'               => $form->createView(),
            'region_categories'  => $this->getRegionCategories(),
            'validSubjects'      => $validSubjects,
        ));
    }

    public function accessAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', array(
            'with_roles' => true,
        ), array(
            'csrf_protection'    => false,
            'translation_domain' => 'user',
        ))
            ->add('with_roles', 'checkbox', array('required' => false))
            ->add('email', 'text', array('required' => false))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u');

        if ($filter_form->get('with_roles')->getData()) {
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('u.roles', ':role_admin'),
                    $qb->expr()->like('u.roles', ':role_mod'),
                    $qb->expr()->like('u.roles', ':role_tester')
                ))
                ->setParameter(':role_admin', '%"ROLE_ADMIN"%')
                ->setParameter(':role_mod', '%"ROLE_MOD_%')
                ->setParameter(':role_tester', '%"ROLE_TESTER_TRAINING"%')
            ;
        }
        if ($data = $filter_form->get('email')->getData()) {
            $qb->andWhere('u.email LIKE :email')->setParameter(':email', '%'.$data.'%');
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Admin:User/access.html.twig', array(
            'pagerfanta'  => $pagerfanta,
            'filter_form' => $filter_form->createView(),
        ));
    }

    public function accessEditAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $user = $this->em->getRepository('AppBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t edit yourself');
        }

        $u_roles = array(
            'ROLE_ADMIN'               => 'ROLE_ADMIN',
            'ROLE_MOD_CONTENT'         => 'ROLE_MOD_CONTENT',
            'ROLE_MOD_PRECHECK_USERS'  => 'ROLE_MOD_PRECHECK_USERS',
            'ROLE_MOD_PARADOX_USERS'   => 'ROLE_MOD_PARADOX_USERS',
            'ROLE_MOD_ADD_USER'        => 'ROLE_MOD_ADD_USER',
            'ROLE_MOD_SUPPORT'         => 'ROLE_MOD_SUPPORT',
            'ROLE_MOD_TEACHER'         => 'ROLE_MOD_TEACHER',
            'ROLE_MOD_FINANCE'         => 'ROLE_MOD_FINANCE',
            'ROLE_MOD_PROMO'           => 'ROLE_MOD_PROMO',
            'ROLE_MOD_MAILING'         => 'ROLE_MOD_MAILING',
            'ROLE_MOD_ACCOUNTANT'      => 'ROLE_MOD_ACCOUNTANT',
            'ROLE_TESTER_TRAINING'     => 'ROLE_TESTER_TRAINING',
            'ROLE_TRANSLATOR_TJ'       => 'ROLE_TRANSLATOR_TJ',
            'ROLE_TRANSLATOR_UZ'       => 'ROLE_TRANSLATOR_UZ',
            'ROLE_TRANSLATOR_KG'       => 'ROLE_TRANSLATOR_KG',
        );

        $form_factory = $this->container->get('form.factory');
        $fb = $form_factory->createNamedBuilder('user', 'form', $user, array(
            'validation_groups'  => false,
            'translation_domain' => 'user',
        ));
        $fb->add('u_roles', 'choice', array(
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'choices'  => $u_roles,
        ));
        $fb->add('u_white_ips', 'textarea', array(
            'required' => false,
            'help'     => 'user_u_white_ips_help',
        ));
        $fb->add('moderated_support_categories', 'entity', array(
            'class'         => 'AppBundle:SupportCategory',
            'multiple'      => true,
            'expanded'      => true,
            'required'      => false,
            'query_builder' => function (EntityRepository $er) use ($user) {
                return $er->createQueryBuilder('sc')
                    ->andWhere('sc.type = :t AND sc.parent IS NOT NULL')
                    ->setParameter('t', 'category')
                    ->orderBy('sc.createdAt')
                    ->orderBy('sc.parent')
                ;
            },
        ));
        $form = $fb->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirect($this->generateUrl('admin_users_access_edit', array('id' => $user->getId())));
        }

        return $this->render('AppBundle:Admin:User/access_edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function sendEmailToAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted(array(
                'ROLE_MOD_PRECHECK_USERS',
                'ROLE_MOD_PARADOX_USERS',
        ))) {
            throw $this->createNotFoundException();
        }

        $user = $this->em->getRepository('AppBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        $categoriesTree = array();
        $categories = $this->em->getRepository('AppBundle:SupportCategory')->createQueryBuilder('sc')
            ->orderBy('sc.createdAt')
            ->andWhere('sc.type != :type')->setParameter('type', 'teacher')
            ->orderBy('sc.parent')
            ->getQuery()->getResult();
        foreach ($categories as $category) { /** @var $category \My\AppBundle\Entity\SupportCategory */
            if ($category->getParent()) {
                if (!isset($categoriesTree[$category->getParent()->getName()])) {
                    $categoriesTree[$category->getParent()->getName()] = array();
                }
                $categoriesTree[$category->getParent()->getName()][$category->getId()] = $category->getName();
            }
        }

        $form_factory = $this->container->get('form.factory');
        $form = $form_factory->createNamedBuilder('send_email', 'form', null, array(
            'translation_domain' => 'send_email',
        ))
            ->add('subject', 'choice', array(
                'choices'     => $categoriesTree,
                'empty_value' => '-----',
                'constraints' => array(new Assert\NotBlank()),
            ))
            ->add('message', 'textarea', array(
                'constraints' => array(new Assert\NotBlank()),
            ))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $category_id = $form->get('subject')->getData();
            $category = $this->em->getRepository('AppBundle:SupportCategory')->find($category_id);

            $dialog = new SupportDialog();
            $dialog->setCategory($category);
            $dialog->setUser($user);
            $dialog->setLastMessageText($form->get('message')->getData());
            $dialog->setLastMessageTime(new \DateTime());
            $dialog->setLastModerator($this->getUser());
            $dialog->setUserRead(false);
            $dialog->setAnswered(true);
            $this->em->persist($dialog);

            $message = new SupportMessage();
            $message->setDialog($dialog);
            $message->setText($form->get('message')->getData());
            $message->setUser($this->getUser());
            $this->em->persist($message);

            $this->em->flush();

            $this->get('app.notify')->sendSupportAnsweredAdmin($dialog->getUser());

            return $this->redirect($this->generateUrl('admin_support_dialog_show', array(
                'id' => $dialog->getId(),
            )));
        }

        return $this->render('AppBundle:Admin:User/send_email_to.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('add_user_title', 'text');
        $fb->add('add_user_text', 'textarea');

        return $fb;
    }

    public static function generatePassword($length = 8)
    {
        $password = '';
        for ($i = 0; $i < $length; $i ++) {
            $password .= rand(0, 9);
        }
        return $password;
    }

    public function unsubscribeForAllAction($id, $type)
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => $id));
        if (!$user) {
            throw $this->createNotFoundException('Not found user for id "'.$id.'"');
        }

        if ($type == 'paradox') {
            $url = $this->generateUrl('admin_paradox_user_view', ['id' => $id]);
        } elseif ($type == 'precheck') {
            $url = $this->generateUrl('admin_precheck_user_view', ['id' => $id]);
        } else {
            $url = $this->generateUrl('admin_paradox_users');
        }

        $user->setPayment1PaidNotNotify(true);
        $user->setPayment2PaidNotNotify(true);
        $user->setPayment3PaidNotNotify(true);
        $user->setMailing(false);
        $user->setOverdueUnsubscribed(true);
        $user->setNotAllMailing(true);

        $this->em->persist($user);
        $this->em->flush();

        $mailing = $this->em->getRepository('AppBundle:Mailing')->findAll();

        foreach ($mailing as $mail) {
            $array_users = $mail->getUsers();
            $userId = array_search($id, $array_users);

            if ($userId !== false) {
                unset($array_users[$userId]);
                $mail->setUsers($array_users);
                $this->em->persist($mail);
                $this->em->flush();
            }
        }

        return $this->render('AppBundle:Admin:unsubscribe.html.twig', array(
            'userName' => $user->getLastName().' '.$user->getFirstName(),
            'url'      => $url,
        ));
    }

    public function usersWithClosedSubjectsAction(Request $request)
    {
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $this->createFormBuilder([], ['translation_domain' => 'user']);
        $fb->add('last_name', 'text', ['required' => false]);
        $fb->add('first_name', 'text', ['required' => false]);
        $fb->add('patronymic', 'text', ['required' => false]);
        $fb->add('email', 'text', ['required' => false]);
        $fb->add('phone_mobile', 'text', ['required' => false]);
        $fb->setMethod('get');

        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->leftJoin('u.closed_subjects', 'cs')
            ->andWhere('cs is NOT NULL')
            ->orWhere('u.close_final_exam = :close_final_exam')->setParameter('close_final_exam', true)
        ;

        if ($data = $filter_form->get('last_name')->getData()) {
            $qb->andWhere('u.last_name LIKE :last_name')->setParameter(':last_name', '%'.$data.'%');
        }
        if ($data = $filter_form->get('first_name')->getData()) {
            $qb->andWhere('u.first_name LIKE :first_name')->setParameter(':first_name', '%'.$data.'%');
        }
        if ($data = $filter_form->get('patronymic')->getData()) {
            $qb->andWhere('u.patronymic LIKE :patronymic')->setParameter(':patronymic', '%'.$data.'%');
        }

        $data = $filter_form->get('phone_mobile')->getData();
        if ($data) {
            preg_match('#^\+7 \((\d{3})\) (\d{3})\-(\d{2})\-(\d{2})$#misu', $data, $matches);
            if (count($matches)) {
                $phone = $matches[1].$matches[2].$matches[3].$matches[4];
                $qb->andWhere('u.phone_mobile = :phone_mobile')->setParameter(':phone_mobile', $phone);
            }
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setMaxPerPage(20);
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Admin:User/users_with_closed_subjects.html.twig', array(
            'pagerfanta'  => $pagerfanta,
            'filter_form' => $filter_form->createView(),
        ));
    }

    public function editClosedSubjectsAction(Request $request, $id)
    {
        /** @var $user \My\AppBundle\Entity\User */
        $user = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->leftJoin('u.closed_subjects', 'cs')
            ->andWhere('cs is NOT NULL')
            ->orWhere('u.close_final_exam = :close_final_exam')->setParameter('close_final_exam', true)
            ->andWhere('u.id = :id')->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $regionId              = $user->getRegion()->getId();
        $categoryId            = $user->getCategory()->getId();
        $validSubjects         = $this->getValidSubjects();
        $closedSubjectsChoices = [];  /** @var $version \My\AppBundle\Entity\TrainingVersion */

        foreach ($user->getCategory()->getTrainingVersions() as $version) {
            foreach ($version->getSubjects() as $subject) {  /** @var $subject \My\AppBundle\Entity\Subject */
                if (isset($validSubjects[$regionId][$categoryId][$subject->getId()])) {
                    $closedSubjectsChoices[$subject->getId()] = $subject->getTitle();
                }
            }
        }

        $closedSubjects = [];
        foreach ($user->getClosedSubjects() as $subject) {
            $closedSubjects[$subject->getId()] = $subject->getId();
        }

        $fb = $this->createFormBuilder([], []);
        $fb->add('closed_subjects', 'choice', array(
            'label'    => 'Заблокировать доступ к',
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices'  => $closedSubjectsChoices,
            'data'     => $closedSubjects,
        ));
        $fb->add('close_final_exam', 'checkbox', array(
            'required' => false,
            'label'    => 'Запретить финальный экзамен',
            'data'     => $user->getCloseFinalExam(),
        ));

        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) { /** @var $closedSubject \My\AppBundle\Entity\Subject */
            foreach ($user->getClosedSubjects() as $closedSubject) {
                $closedSubject->removeClosedUser($user);
                $this->em->persist($closedSubject);
            }

            $closedSubjectIds = $form->get('closed_subjects')->getData();
            foreach ($closedSubjectIds as $closedSubjectId) {
                $closedSubject = $this->em->getRepository('AppBundle:Subject')->find($closedSubjectId);
                if ($closedSubject) {
                    $user->addClosedSubject($closedSubject);
                    $closedSubject->addClosedUser($user);
                    $this->em->persist($closedSubject);
                }
            }

            $user->setCloseFinalExam($form->get('close_final_exam')->getData());
            $this->em->persist($user);
            $this->em->flush();

            $this->get('session')->getFlashBag()->add('success', 'success_edited');
            return $this->redirect($this->generateUrl('admin_users_with_closed_subjects'));
        }

        return $this->render('AppBundle:Admin:User/edit_closed_subjects.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
