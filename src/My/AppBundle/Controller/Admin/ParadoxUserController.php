<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Pagerfanta\Pagerfanta;
use My\PaymentBundle\Entity\Log as PaymentLog;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints as Assert;

class ParadoxUserController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;
    public $settings = array();

    public function init()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_MOD_PARADOX_USERS')) {
            throw $this->createNotFoundException();
        }
    }

    public function listAction(Request $request)
    {
//        $categories = $this->em->getRepository('AppBundle:Category')->findAll();
        $services = $this->em->getRepository('AppBundle:Service')->findAll();
        $typePaids = array(
            'paid_in_offline' => 'paids.offline',
            'paid_is_online' => 'paids.online',
        );
        $paids = array(
            'no_paid' => 'paids.no_paid',
            'all_online' => 'paids.all_online',
            'paid_2'   => 'paids.paid_2',
            'paid_3'   => 'paids.paid_3',
            'all_offline' => 'paids.all_offline',
            'paid_3_f' => 'paids.paid_3_f',
        );
        $offers = $this->em->getRepository('AppBundle:Offer')->findBy(array(), array('ended_at' => 'ASC'));
        foreach ($offers as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $paids[$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
        }

        $offers_student = $this->em->getRepository('AppBundle:OfferStudent')
            ->findBy(array(), array('ended_at' => 'ASC'));
        foreach ($offers_student as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            $paids['student-'.$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
        }

        $exams = array();
        $subjects = $this->em->getRepository('AppBundle:Subject')->findAll();
        foreach ($subjects as $subject) { /** @var $subject \My\AppBundle\Entity\Subject */
            $exams[$subject->getId()] = $subject->getTitle();
        }

        $additional_services = array();
        foreach ($services as $service) {
            if (!$service->getType()) {
                $additional_services[$service->getId()] = $service->getName();
            }
        }

        // default value — first region
        $defaultRegion = $this->em->getRepository('AppBundle:Region')->findOneBy(array());
        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => 'user',
        ))
            ->add('all', 'checkbox', array('required' => false))
            ->add('type_paids', 'choice', array(
                'required'    => false,
                'empty_value' => 'choose_option',
                'choices'     => $typePaids,
            ))
            ->add('paids', 'choice', array(
               'required'    => false,
               'empty_value' => 'choose_option',
               'choices'     => $paids,
            ))
            ->add('additional_paids', 'choice', array(
               'required' => false,
               'multiple' => true,
               'choices'  => $additional_services,
            ))
            ->add('paid2_from', 'date', array(
                'years'       => range(2010, date('Y') + 1),
                'required'    => false,
                'empty_value' => '--',
            ))
            ->add('paid2_to', 'date', array(
                'years'       => range(2010, date('Y') + 1),
                'required'    => false,
                'empty_value' => '--',
            ))
            ->add('paid3_from', 'date', array(
                'years'       => range(2010, date('Y') + 1),
                'required'    => false,
                'empty_value' => '--',
            ))
            ->add('paid3_to', 'date', array(
                'years'       => range(2010, date('Y') + 1),
                'required'    => false,
                'empty_value' => '--',
            ))
            ->add('category', 'entity', array(
               'class'       => 'AppBundle:Category',
               'required'    => false,
               'empty_value' => 'choose_option',
            ))
            ->add('phone_mobile', 'text', array('required' => false))
            ->add('passport_number', 'text', array('required' => false))
            ->add('birthday', 'birthday', array(
               'years'       => range(1930, date('Y')),
               'required'    => false,
               'empty_value' => '--',
            ))
            ->add('last_name', 'text', array('required' => false))
            ->add('first_name', 'text', array('required' => false))
            ->add('patronymic', 'text', array('required' => false))
            ->add('region', 'entity', array(
               'class'       => 'AppBundle:Region',
               'required'    => false,
               'empty_value' => 'choose_option',
               'data'        => $defaultRegion,
            ))
            ->add('email', 'text', array('required' => false))
            ->add('webgroup', 'entity', array(
               'class'       => 'AppBundle:Webgroup',
               'required'    => false,
               'empty_value' => 'choose_option',
            ))
            ->add('paradox_id', 'text', array('required' => false))
            ->add('phone_mobile_confirmed', 'choice', array(
               'required'    => false,
               'empty_value' => 'choose_option',
               'choices'     => array(
                   'yes' => 'yes',
                   'no'  => 'no',
               ),
            ))
            ->add('show_from', 'date', array(
               'years'       => range(2010, date('Y') + 1),
               'required'    => false,
               'empty_value' => '--',
            ))
            ->add('show_to', 'date', array(
               'years'       => range(2010, date('Y') + 1),
               'required'    => false,
               'empty_value' => '--',
            ))
            ->add('exams', 'choice', array(
               'required' => false,
               'multiple' => true,
               'expanded' => true,
               'choices'  => $exams,
            ))
            ->add('final_exam', 'checkbox', array('required' => false))
            ->add('in_paradox', 'checkbox', array('required' => false))
            ->add('mailing', 'choice', array(
                'required'    => false,
                'empty_value' => 'choose_option',
                'choices'     => array('yes' => 'yes', 'no' => 'no')
            ))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);
        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->orderBy('u.created_at')
            ->groupBy('u.id')
        ;

        $data = null;
        if (!(($data = $filter_form->get('all')->getData()) && $data)) {
            $qb
                ->andWhere('u.moderated = :moderated')->setParameter(':moderated', true)
                ->andWhere('u.paradox_id IS NULL')
                ->andWhere('u.roles NOT LIKE :role_admin')->setParameter(':role_admin', '%"ROLE_ADMIN"%')
                ->andWhere('u.roles NOT LIKE :role_mod')->setParameter(':role_mod', '%"ROLE_MOD_%')
            ;
        }

        if ($data = $filter_form->get('paids')->getData()) {
            $is_offer = (!in_array($data, ['no_paid', 'paid_2', 'paid_3', 'paid_3_f']) && isset($paids[$data]));

            if ($data == 'no_paid') {
                $qb->andWhere('u.roles NOT LIKE :paid1')->setParameter(':paid1', '%"ROLE_USER_PAID"%');
            } elseif ($data == 'all_online') {
                $qb
                    ->orWhere('u.reg_info LIKE :type1 OR u.reg_info LIKE :type2')
                    ->setParameter(':type1', '%"online"%')
                    ->setParameter(':type2', '%"online2"%')
                ;
            } elseif ($data == 'paid_2') {
                $qb
                    ->andWhere('u.reg_info LIKE :type')->setParameter(':type', '%"online2"%')
                ;
            } elseif ($data == 'paid_3') {
                $qb
                    ->andWhere('u.roles LIKE :paid3')->setParameter(':paid3', '%"ROLE_USER_PAID3"%')
                    ->andWhere('u.reg_info LIKE :reg')->setParameter(':reg', '%"online"%')
                    ->leftJoin('u.payment_logs', 'plog')
                    ->andWhere('plog.comment NOT LIKE :moderator_id')->setParameter('moderator_id', '%moderator_id%')
                ;
            } elseif ($data == 'all_offline') {
                $qb
                    ->andWhere('u.roles LIKE :paid3')->setParameter(':paid3', '%"ROLE_USER_PAID3"%')
                    ->leftJoin('u.payment_logs', 'plog')
                    ->andWhere('plog.comment LIKE :moderator_id')->setParameter('moderator_id', '%moderator_id%')
                    ;
            } elseif ($data == 'paid_3_f') {
                $qb
                    ->andWhere('u.roles LIKE :paid3')->setParameter(':paid3', '%"ROLE_USER_PAID3"%')
                    ->leftJoin('u.payment_logs', 'plog')
                    ->andWhere('plog.comment LIKE :moderator_id')->setParameter('moderator_id', '%moderator_id%')
                    ->andWhere('plog.comment NOT LIKE :offer_id')->setParameter('offer_id', '%offer_id%')
                    ->andWhere('plog.comment NOT LIKE :offer_student_id')
                    ->setParameter('offer_student_id', '%offer_student_id%')
                ;
            } elseif ($is_offer) {
                $qb
                    ->leftJoin('u.payment_logs', 'plog')
                    ->andWhere('regexp(plog.comment, :offer_id) != false OR regexp(plog.comment, :offer_student_id)
                    != false')
                    ->setParameter(':offer_id', '"offer_id":'.$data.'[,}]+')
                    ->setParameter(':offer_student_id', '"offer_student_id":'.explode('-', $data)[1].'[,}]+')
                ;
            }
        }
        if ($data = $filter_form->get('additional_paids')->getData()) {
            foreach ($filter_form->get('additional_paids')->getData() as $num => $paid) {
                $qb
                    ->leftJoin('u.payment_logs', 'log'.$num)
                    ->andWhere('regexp(log'.$num.'.comment, :additional_paids'.$num.') != false')
                    ->setParameter(':additional_paids'.$num, '"services":"[0-9,]*'.$paid.'[0-9,]*"')
                ;
            }
        }
        $paid2From = $filter_form->get('paid2_from')->getData();
        $paid2To = $filter_form->get('paid2_to')->getData();
        if ($paid2From && !$paid2To) {
            $qb->andWhere('u.payment_2_paid >= :paid2_from')->setParameter(':paid2_from', $paid2From->format('Y-m-d'));
        } elseif ($paid2To && !$paid2From) {
            $qb->andWhere('u.payment_2_paid <= :paid2_to')->setParameter(':paid2_to', $paid2To->format('Y-m-d'));
        } elseif ($paid2To && $paid2From) {
            $qb->andWhere('u.payment_2_paid >= :paid2_from')->setParameter(':paid2_from', $paid2From->format('Y-m-d'));
            $qb->andWhere('u.payment_2_paid <= :paid2_to')->setParameter(':paid2_to', $paid2To->format('Y-m-d'));
        }
        $paid3From = $filter_form->get('paid3_from')->getData();
        $paid3To = $filter_form->get('paid3_to')->getData();
        if ($paid3From && !$paid3To) {
            $qb->andWhere('u.payment_3_paid >= :paid3_from')->setParameter(':paid3_from', $paid3From->format('Y-m-d'));
        } elseif ($paid3To && !$paid3From) {
            $qb->andWhere('u.payment_3_paid <= :paid3_to')->setParameter(':paid3_to', $paid3To->format('Y-m-d'));
        } elseif ($paid3To && $paid3From) {
            $qb->andWhere('u.payment_3_paid >= :paid3_from')->setParameter(':paid3_from', $paid3From->format('Y-m-d'));
            $qb->andWhere('u.payment_3_paid <= :paid3_to')->setParameter(':paid3_to', $paid3To->format('Y-m-d'));
        }

        if ($data = $filter_form->get('category')->getData()) {
            $qb->andWhere('u.category = :category')->setParameter(':category', $data);
        }
        if ($data = $filter_form->get('phone_mobile')->getData()) {
            $qb->andWhere('u.phone_mobile LIKE :phone_mobile')->setParameter(':phone_mobile', '%'.$data.'%');
        }
        if ($data = $filter_form->get('passport_number')->getData()) {
            $qb->andWhere('u.passport_number LIKE :passport_number')->setParameter(':passport_number', '%'.$data.'%');
        }
        if ($data = $filter_form->get('birthday')->getData()) {
            $qb->andWhere('u.birthday = :birthday')->setParameter(':birthday', $data);
        }
        if ($data = $filter_form->get('last_name')->getData()) {
            $qb->andWhere('u.last_name LIKE :last_name')->setParameter(':last_name', '%'.$data.'%');
        }
        if ($data = $filter_form->get('first_name')->getData()) {
            $qb->andWhere('u.first_name LIKE :first_name')->setParameter(':first_name', '%'.$data.'%');
        }
        if ($data = $filter_form->get('patronymic')->getData()) {
            $qb->andWhere('u.patronymic LIKE :patronymic')->setParameter(':patronymic', '%'.$data.'%');
        }
        if ($data = $filter_form->get('region')->getData()) {
            $qb->andWhere('u.region = :region')->setParameter(':region', $data);
        }
        if ($data = $filter_form->get('email')->getData()) {
            $qb->andWhere('u.email LIKE :email')->setParameter(':email', '%'.$data.'%');
        }
        if ($data = $filter_form->get('webgroup')->getData()) {
            $qb->andWhere('u.webgroup = :webgroup')->setParameter(':webgroup', $data);
        }
        if ($data = $filter_form->get('paradox_id')->getData()) {
            $qb->andWhere('u.paradox_id = :paradox_id')->setParameter(':paradox_id', $data);
        }
        if ($data = $filter_form->get('phone_mobile_confirmed')->getData()) {
            if ($data == 'yes') {
                $qb
                    ->andWhere('u.phone_mobile_status = :phone_mobile_status')
                    ->setParameter(':phone_mobile_status', 'confirmed')
                ;
            } else {
                $qb
                    ->andWhere('u.phone_mobile_status != :phone_mobile_status')
                    ->setParameter(':phone_mobile_status', 'confirmed')
                ;
            }
        }
        if ($data = $filter_form->get('show_from')->getData()) {
            $qb->andWhere('u.created_at >= :show_from')->setParameter(':show_from', $data);
        }
        if ($data = $filter_form->get('show_to')->getData()) {
            $qb->andWhere('u.created_at <= :show_to')->setParameter(':show_to', $data);
        }
        if ($data = $filter_form->get('exams')->getData()) {
            foreach ($data as $eid) {
                $qb2 = $this->em->getRepository('AppBundle:ExamLog')->createQueryBuilder('el_'.$eid)
                    ->andWhere('el_'.$eid.'.user = u')
                    ->andWhere('el_'.$eid.'.subject = :el_subject_'.$eid)
                    ->andWhere('el_'.$eid.'.passed = :el_passed_'.$eid)
                ;
                $qb
                    ->setParameter(':el_subject_'.$eid, $eid)
                    ->setParameter(':el_passed_'.$eid, true)
                    ->andWhere($qb->expr()->exists($qb2))
                ;
            }
        }
        if (($data = $filter_form->get('final_exam')->getData()) && $data) {
            $qb
                ->leftJoin('u.final_exams_logs', 'fel')
                ->andWhere('fel.passed = :fel_passed')->setParameter(':fel_passed', true)
            ;
        }
        if (($data = $filter_form->get('in_paradox')->getData()) && $data) {
            $qb->andWhere('u.paradox_id IS NOT NULL');
        }
        if ($data = $filter_form->get('mailing')->getData()) {
            $qb->andWhere('u.mailing = :mailing')->setParameter(':mailing', ($data == 'yes'));
        }

        if ($request->get('csv')) {
            $response = new StreamedResponse();
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="paradox_users.csv"');
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->setCharset('utf8');
            $response->setCallback(function () use ($qb) {
                $handle = fopen('php://output', 'w+');
                fputcsv($handle, [
                    'Фамилия',
                    'Имя',
                    'Отчество',
                    'E-mail',
                    'Телефон',
                    'Дата регистрации',
                    'Дата первой оплаты',
                    'Дата второй оплаты',
                ], ';');
                $rows = $qb->getQuery()->iterate();
                foreach ($rows as $row) {
                    /** @var $user \My\AppBundle\Entity\User */
                    $user = $row[0];
                    fputcsv($handle, [
                        $user->getLastName(),
                        $user->getFirstName(),
                        $user->getPatronymic(),
                        $user->getEmail(),
                        $user->getPhoneMobileStatus() == 'confirmed' ? ('8'.$user->getPhoneMobile()) : '-',
                        date_format($user->getCreatedAt(), 'Y-m-d'),
                        $user->getPayment1Paid() ? date_format($user->getPayment1Paid(), 'Y-m-d') : '-',
                        $user->getPayment2Paid() ? date_format($user->getPayment2Paid(), 'Y-m-d') : '-',
                    ], ';');
                    $this->em->detach($user);
                }
                fclose($handle);
            });
            return $response;
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Admin/ParadoxUser:list.html.twig', array(
            'pagerfanta'     => $pagerfanta,
            'filter_form'    => $filter_form->createView(),
            'default_region' => $defaultRegion,
        ));
    }

    public function viewAction($id)
    {
        $userRepo = $this->em->getRepository('AppBundle:User');
        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t moderate yourself');
        }

        $notPayment1PaidNotify  = $user->getPayment1PaidNotNotify();
        $notPayment2PaidNotify  = $user->getPayment2PaidNotNotify();
        $notPayment3PaidNotify  = $user->getPayment3PaidNotNotify();
        $notMailing             = !$user->getMailing();
        $notOverdueUnsubscribed = $user->getOverdueUnsubscribed();
        $notAllMailing          = $user->getNotAllMailing();

        $userUnsubscrible = false;
        if ($notPayment1PaidNotify
            && $notPayment2PaidNotify
            && $notPayment3PaidNotify
            && $notMailing
            && $notOverdueUnsubscribed
            && $notAllMailing
        ) {
            $userUnsubscrible = true;
        }

        $categories = array();
        $categories_orig = $this->em->getRepository('AppBundle:Category')->findAll();
        foreach ($categories_orig as $category) { /** @var $category \My\AppBundle\Entity\Category */
            $categories[$category->getId()] = $category;
        }

        $services = array();
        $services_orig = $this->em->getRepository('AppBundle:Service')->findAll();
        foreach ($services_orig as $service) { /** @var $service \My\AppBundle\Entity\Service */
            $services[$service->getId()] = $service;
        }

        $payments = array();
        $logs = $this->em->getRepository('PaymentBundle:Log')->createQueryBuilder('l')
            ->andWhere('l.user = :user')->setParameter(':user', $user)
            ->andWhere('l.paid = :paid')->setParameter(':paid', true)
            ->leftJoin('l.revert_logs', 'rl')->addSelect('rl')
            ->addOrderBy('l.updated_at', 'ASC')
            ->getQuery()->getArrayResult();
        foreach ($logs as $log) { /** @var $log \My\PaymentBundle\Entity\Log */
            $log['reverts'] = false;
            $reverts = $log['revert_logs'];
            foreach ($reverts as $revert) {
                if ($revert['paid']) {
                    $log['reverts'] = true;
                    break;
                }
            }

            $log['comment'] = json_decode($log['comment'], true);
            $comment = $log['comment'];
            $log['categories'] = array();
            $log['services'] = array();

            //Модератор, который добавил пользователя
            $moderatorName = null;
            if (!empty($comment['moderator_id'])) {
                /** @var $moderator \My\AppBundle\Entity\User */
                $moderator = $userRepo->find($comment['moderator_id']);
                if ($moderator) {
                    $moderatorName = $moderator->getFullName();
                }
            }

            if (!empty($comment['categories'])) {
                $categories_ids = explode(',', $comment['categories']);
                foreach ($categories_ids as $category_id) {
                    if (isset($categories[$category_id])) {
                        $log['categories'][$category_id] = $categories[$category_id];
                    }
                }
                if (count($log['categories']) > 0) {
                    if ($moderatorName) {
                        $log['moderator_name'] = $moderatorName;
                    }
                    $payments[] = $log;
                }
            }

            if (!empty($comment['services'])) {
                $services_ids = explode(',', $comment['services']);
                foreach ($services_ids as $service_id) {
                    if (isset($services[$service_id])) {
                        $log['services'][$service_id] = $services[$service_id];
                    } else {
                        /** @CAUTION наследие %) */
                        $log['services'][$service_id] = array('name' => 'Доступ к теоретическому курсу');
                    }
                }
                if (count($log['services']) > 0) {
                    if ($moderatorName) {
                        $log['moderator_name'] = $moderatorName;
                    }
                    $payments[] = $log;
                }
            }
        }

        $version = $this->em->getRepository('AppBundle:TrainingVersion')->getVersionByUser($user);

        if ($version) {
            $subjects_repository = $this->em->getRepository('AppBundle:Subject');
            $subjects = $subjects_repository->findAllAsArray($user, $version);
        } else {
            $subjects = array();
        }

        $final_exams_logs_repository = $this->em->getRepository('AppBundle:FinalExamLog');
        $passed_date = $final_exams_logs_repository->getPassedDate($user);
        $is_passed = (bool)$passed_date;

        $exp_limit = null;
        if ($user->getPayment3Paid()) {
            $exp_limit = clone $user->getPayment3Paid();
            $exp_limit->add(new \DateInterval('P'.$this->settings['access_time_after_3_payment'].'D'));
        }

        return $this->render('AppBundle:Admin:ParadoxUser/view.html.twig', array(
            'user'              => $user,
            'user_unsubscrible' => $userUnsubscrible,
            'payments'          => $payments,
            'subjects'          => $subjects,
            'passed_date'       => $passed_date,
            'is_passed'         => $is_passed,
            'is_expired'        => $exp_limit && $exp_limit < new \DateTime(),
        ));
    }

    public function editEmailAction(Request $request, $id)
    {
        $user = $this->em->find('AppBundle:User', $id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t edit yourself');
        }

        if ($user->isEnabled()) {
            throw $this->createNotFoundException('Пользователь уже подтвердил E-mail');
        }

        if (!$user->getOffline()) {
            throw $this->createNotFoundException('Пользователь добавлен не из админки');
        }

        $form_factory = $this->container->get('form.factory');
        $form = $form_factory->createNamedBuilder('user', 'form', $user, array(
            'validation_groups'  => 'only_email',
            'translation_domain' => 'user',
        ))
            ->add('email', 'text')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $plainPassword = UserController::generatePassword();
            $user->setPlainPassword($plainPassword);
            $this->container->get('app.user_helper')->sendMessages($user, $plainPassword, true);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirect($this->generateUrl('admin_paradox_user_view', ['id' => $user->getId()]));
        }

        return $this->render('AppBundle:Admin:ParadoxUser/edit_email.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    public function setAction(Request $request, $id)
    {
        $user = $this->em->find('AppBundle:User', $id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t edit yourself');
        }

        if ($user->getParadoxId()) {
            throw $this->createNotFoundException('This user has already moved to the paradox.');
        }

        $form_factory = $this->container->get('form.factory');
        $form = $form_factory->createNamedBuilder('user', 'form', $user, array(
            'validation_groups'  => 'paradox',
            'translation_domain' => 'user',
        ))
            ->add('paradox_id', null, array(
                'required'          => true,
                'constraints'       => array(new Assert\NotBlank(array('groups' => 'paradox'))),
                'validation_groups' => 'paradox',
            ))
            ->add('webgroup', null, array(
                'empty_value'       => 'choose_option',
                'required'          => true,
                'constraints'       => array(new Assert\NotBlank(array('groups' => 'paradox'))),
                'validation_groups' => 'paradox',
            ))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirect($this->generateUrl('admin_paradox_users'));
        }

        return $this->render('AppBundle:Admin:ParadoxUser/set.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function toPrecheckAction($id)
    {
        $user = $this->em->find('AppBundle:User', $id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t edit yourself');
        }

        if ($user->getParadoxId()) {
            throw $this->createNotFoundException('This user has already moved to the paradox.');
        }

        $user->setModerated(false);
        $this->em->persist($user);
        $this->em->flush();

        return $this->redirect($this->generateUrl('admin_paradox_users'));
    }

    public function lockAction($state, $id)
    {
        $notify = $this->get('app.notify');

        $user = $this->em->find('AppBundle:User', $id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t lock and unlock yourself');
        }

        if ($state) {
            $user->setLocked(true);
            if ($this->settings['lock_user_enabled']) {
                $subject = $this->settings['lock_user_title'];
                $text = $this->settings['lock_user_text'];
                $notify->sendEmail($user, $subject, $text, 'text/html');
            }
        } else {
            $user->setLocked(false);
            if ($this->settings['unlock_user_enabled']) {
                $subject = $this->settings['unlock_user_title'];
                $text = $this->settings['unlock_user_text'];
                $notify->sendEmail($user, $subject, $text, 'text/html');
            }
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirect($this->generateUrl('admin_paradox_user_view', array('id' => $id)));
    }

    public function payAction(Request $request, $id)
    {
        $user = $this->em->find('AppBundle:User', $id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }
        if (in_array('ROLE_USER_PAID', $user->getRoles())) {
            throw $this->createNotFoundException('User for id "'.$id.'" has payment.');
        }

        $region = $user->getRegion();
        $category = $user->getCategory();
        $reg_info = $user->getRegInfo();
        $with_at = $reg_info['with_at'];
        $paids = array(
            'paid_3_f' => 'paids.paid_3_f',
        );
        $offers = $this->em->getRepository('AppBundle:Offer')->getActiveOffers();
        $offersStudents = $this->em->getRepository('AppBundle:OfferStudent')->getActiveOffers();

        foreach ($offers as $offer) {
            /** @var $offer \My\AppBundle\Entity\Offer */
            $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
            if ($sum > 0) {
                $paids[$offer->getId()] = 'Спецпредложение: '.$offer->getTitle();
            }
        }
        foreach ($offersStudents as $offer) { /** @var $offer \My\AppBundle\Entity\Offer */
            /** @var $offer \My\AppBundle\Entity\Offer */
            $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
            if ($sum > 0) {
                $paids[$offer->getId()] = 'Спецпредложение: ' . $offer->getTitle();
            }
        }

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', array(), array(
            'translation_domain' => 'user',
        ))
            ->add('paid', 'choice', array(
                'empty_value' => 'choose_option',
                'choices'     => $paids,
            ))
        ;
        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $paid = $form->get('paid')->getData();
            $is_offer = (!in_array($paid, ['paid_1', 'paid_2', 'paid_3', 'paid_3_f']) && isset($paids[$paid]));
            $moderatorId = $this->getUser()->getId();
            $price = $category->getPriceByRegion($region);

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
                    }
                    if ($offer) {
                        $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
                        $comment['paid'] = '1,2,3';
                        $comment['offer_student_id'] = $offer->getId();
                    }
                }

                $log = new PaymentLog();
                $log->setUser($user);
                $log->setSum($sum);
                $log->setPaid(true);
                $log->setComment(json_encode($comment));
                $this->em->persist($log);
                $this->em->flush();
            }

            return $this->redirect($this->generateUrl('admin_paradox_user_view', array('id' => $id)));
        }

        return $this->render('AppBundle:Admin:ParadoxUser/pay.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function paradoxUserProlongAction($id)
    {
        $user = $this->em->getRepository('AppBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        if ($user == $this->getUser()) {
            throw $this->createNotFoundException('You can\'t prolong yourself');
        }

        $exp_limit = null;
        if ($user->getPayment3Paid()) {
            $exp_limit = clone $user->getPayment3Paid();
            $exp_limit->add(new \DateInterval('P'.$this->settings['access_time_after_3_payment'].'D'));
        }
        if (!$exp_limit || $exp_limit >= new \DateTime()) {
            throw $this->createNotFoundException('Can\'t prolong');
        }

        $date = clone $user->getPayment3Paid();
        $date->add(new \DateInterval('P1Y'));
        $user->setPayment3Paid($date);
        $user->setExpired(false);

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirect($this->generateUrl('admin_paradox_user_view', array('id' => $id)));
    }
}
