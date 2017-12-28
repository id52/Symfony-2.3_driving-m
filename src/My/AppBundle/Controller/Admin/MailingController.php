<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;

class MailingController extends AbstractEntityController
{
    protected $routerList = 'admin_mailing';
    protected $orderBy = array('date' => 'DESC');
    protected $perms = array('ROLE_MOD_MAILING');
    protected $tmplItem = 'Mailing/item.html.twig';
    protected $tmplList = 'Mailing/list.html.twig';

    public function addAction(Request $request)
    {
        $paids = array(
            'no_paid' => 'paids.no_paid',
            'paid_2' => 'paids.paid_2',
            'paid_3' => 'paids.paid_3',
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
        foreach ($subjects as $subject) {
            /** @var $subject \My\AppBundle\Entity\Subject */

            $exams[$subject->getId()] = $subject->getTitle();
        }

        // default value — first region
        $defaultRegion = $this->em->getRepository('AppBundle:Region')->findOneBy(array());

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => 'user',
        ))
            ->add('paids', 'choice', array(
                'required'    => false,
                'empty_value' => 'choose_option',
                'choices'     => $paids,
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
                'choices'     => array('yes' => 'yes', 'no' => 'no'),
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
                'choices'     => array('yes' => 'yes', 'no' => 'no'),
            ))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $data = null;
        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
           ->orderBy('u.created_at')
           ->groupBy('u.id')
        ;

        $request_data = $request->get($filter_form->getName());

        if ($data = $request_data['paids']) {
            $is_offer = (!in_array($data, ['no_paid', 'paid_2', 'paid_3', 'paid_3_f']) && isset($paids[$data]));
            if ($data == 'no_paid') {
                $qb->andWhere('u.roles NOT LIKE :paid1')->setParameter(':paid1', '%"ROLE_USER_PAID"%');
            } elseif ($data == 'paid_2') {
                $qb
                    ->andWhere('u.roles LIKE :paid2')->setParameter(':paid2', '%"ROLE_USER_PAID2"%')
                    ->andWhere('u.roles NOT LIKE :paid3')->setParameter(':paid3', '%"ROLE_USER_PAID3"%')
                ;
            } elseif ($data == 'paid_3') {
                $qb
                    ->andWhere('u.roles LIKE :paid3')->setParameter(':paid3', '%"ROLE_USER_PAID3"%')
                    ->andWhere('u.reg_info LIKE :reg')->setParameter(':reg', '%"online"%')
                    ->leftJoin('u.payment_logs', 'plog')
                    ->andWhere('plog.comment NOT LIKE :moderator_id')->setParameter('moderator_id', '%moderator_id%')
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
        if ($data = $request_data['phone_mobile']) {
            $qb->andWhere('u.phone_mobile LIKE :phone_mobile')->setParameter(':phone_mobile', '%'.$data.'%');
        }
        if ($data = $request_data['passport_number']) {
            $qb->andWhere('u.passport_number LIKE :passport_number')->setParameter(':passport_number', '%'.$data.'%');
        }
        if ($data = $request_data['birthday']) {
            $date = $data['year'].'-'.$data['month'].'-'.$data['day'];
            if ($date != '--') {
                $qb->andWhere('u.birthday = :birthday')->setParameter(':birthday', $date);
            }
        }
        if ($data = $request_data['last_name']) {
            $qb->andWhere('u.last_name LIKE :last_name')->setParameter(':last_name', '%'.$data.'%');
        }
        if ($data = $request_data['first_name']) {
            $qb->andWhere('u.first_name LIKE :first_name')->setParameter(':first_name', '%'.$data.'%');
        }
        if ($data = $request_data['patronymic']) {
            $qb->andWhere('u.patronymic LIKE :patronymic')->setParameter(':patronymic', '%'.$data.'%');
        }
        if ($data = $request_data['region']) {
            $qb->andWhere('u.region = :region')->setParameter(':region', $data);
        } elseif (is_null($request_data)) {
            $qb->andWhere('u.region = :region')->setParameter(':region', $defaultRegion);
        }
        if ($data = $request_data['email']) {
            $qb->andWhere('u.email LIKE :email')->setParameter(':email', '%'.$data.'%');
        }
        if ($data = $request_data['webgroup']) {
            $qb->andWhere('u.webgroup = :webgroup')->setParameter(':webgroup', $data);
        }
        if ($data = $request_data['paradox_id']) {
            $qb->andWhere('u.paradox_id = :paradox_id')->setParameter(':paradox_id', $data);
        }
        if ($data = $request_data['phone_mobile_confirmed']) {
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
        if ($data = $request_data['show_from']) {
            $date = $data['year'].'-'.$data['month'].'-'.$data['day'];
            if ($date != '--') {
                $qb->andWhere('u.created_at >= :show_from')->setParameter(':show_from', $date);
            }
        }
        if ($data = $request_data['show_to']) {
            $date = $data['year'].'-'.$data['month'].'-'.$data['day'];
            if ($date != '--') {
                $qb->andWhere('u.created_at <= :show_to')->setParameter(':show_to', $date);
            }
        }
        if (isset($request_data['exams']) && $data = $request_data['exams']) {
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
        if (isset($request_data['final_exam']) && ($data = $request_data['final_exam']) && $data) {
            $qb
                ->leftJoin('u.final_exams_logs', 'fel')
                ->andWhere('fel.passed = :fel_passed')->setParameter(':fel_passed', true)
            ;
        }
        if (isset($request_data['in_paradox']) && ($data = $request_data['in_paradox']) && $data) {
            $qb->andWhere('u.paradox_id IS NOT NULL');
        }

        if (isset($request_data['mailing']) && '' != $request_data['mailing']) {
            $qb->andWhere('u.mailing = :mailing')->setParameter(':mailing', ($request_data['mailing'] == 'yes'));
        }

        if ($request->isMethod('post')) {
            $qb->select('u.id');
            $users = $qb->getQuery()->getArrayResult();
            $users_ids = array();
            foreach ($users as $user) {
                $users_ids[] = $user['id'];
            }

            $session = $request->getSession();
            $session->set('mailing_users_ids', implode(',', $users_ids));
            $session->set('mailing_filters', serialize($request_data));

            return $this->redirect($this->generateUrl($this->routerRoot.'_create'));
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        $services = $this->em->getRepository('AppBundle:Service')->findAll();
        $categories = $this->em->getRepository('AppBundle:Category')->findAll();

        return $this->render('AppBundle:Admin:Mailing/add.html.twig', array(
            'pagerfanta'     => $pagerfanta,
            'filter_form'    => $filter_form->createView(),
            'default_region' => $defaultRegion,
        ));
    }

    public function createAction(Request $request)
    {
        $session = $request->getSession();

        $users_ids = $session->get('mailing_users_ids');
        $users_ids = $users_ids ? explode(',', $users_ids) : null;
        if (!$users_ids) {
            $this->redirect($this->generateUrl('admin_mailing'));
        }

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->andWhere('u.id IN (:ids)')->setParameter(':ids', $users_ids)
        ;

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        $filters = (array)unserialize($session->get('mailing_filters'));

        /** @var $entity \My\AppBundle\Entity\Mailing */
        $entity = new $this->entityClassName();

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($request->isMethod('post')) {
            if ('delete' == $request->get('action')) {
                $uid = $request->get('uid');
                if (($key = array_search($uid, $users_ids)) !== false) {
                    unset($users_ids[$key]);
                }
                $session->set('mailing_users_ids', implode(',', $users_ids));

                return $this->redirect($request->getRequestUri());
            } else {
                if ($form->isValid()) {
                    $entity->setUsers($users_ids);
                    $entity->setFilters($filters);

                    $this->em->persist($entity);
                    $this->em->flush();

                    $session->remove('mailing_users_ids');
                    $session->remove('mailing_filters');

                    return $this->redirect($this->generateUrl($this->routerList));
                }
            }
        }

        $region = null;
        if (!empty($filters['region'])) {
            $region = $this->em->getRepository('AppBundle:Region')->find($filters['region']);
        }

        $webgroup = null;
        if (!empty($filters['webgroup'])) {
            $webgroup = $this->em->getRepository('AppBundle:Webgroup')->find($filters['webgroup']);
        }

        $exams = array();
        $subjects = $this->em->getRepository('AppBundle:Subject')->findAll();
        foreach ($subjects as $subject) { /** @var $subject \My\AppBundle\Entity\Subject */
            $exams[$subject->getId()] = $subject->getTitle();
        }

        return $this->render('AppBundle:Admin:Mailing/create.html.twig', array(
            'form'       => $form->createView(),
            'pagerfanta' => $pagerfanta,
            'filters'    => $filters,
            'region'     => $region,
            'webgroup'   => $webgroup,
            'exams'      => $exams,
        ));
    }

    public function itemAction(Request $request)
    {
        $id = $request->get('id');
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($request->isMethod('post')) {
            if ('delete' == $request->get('action')) {
                $uid = $request->get('uid');
                $users = $entity->getUsers();
                if (($key = array_search($uid, $users)) !== false) {
                    unset($users[$key]);
                }
                $entity->setUsers($users);
                $this->em->persist($entity);
                $this->em->flush();

                return $this->redirect($request->getRequestUri());
            } else {
                if ($form->isValid()) {
                    $this->em->persist($entity);
                    $this->em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'success_edited');
                    return $this->redirect($this->generateUrl($this->routerList));
                }
            }
        }

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->andWhere('u.id IN (:ids)')->setParameter(':ids', $entity->getUsers())
        ;

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        $filters = $entity->getFilters();

        $region = null;
        if (!empty($filters['region'])) {
            $region = $this->em->getRepository('AppBundle:Region')->find($filters['region']);
        }

        $webgroup = null;
        if (!empty($filters['webgroup'])) {
            $webgroup = $this->em->getRepository('AppBundle:Webgroup')->find($filters['webgroup']);
        }

        $exams = array();
        $subjects = $this->em->getRepository('AppBundle:Subject')->findAll();
        foreach ($subjects as $subject) {
            /** @var $subject \My\AppBundle\Entity\Subject */

            $exams[$subject->getId()] = $subject->getTitle();
        }

        $services = $this->em->getRepository('AppBundle:Service')->findAll();
        $categories = $this->em->getRepository('AppBundle:Category')->findAll();

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'       => $form->createView(),
            'entity'     => $entity,
            'pagerfanta' => $pagerfanta,
            'filters'    => $filters,
            'region'     => $region,
            'webgroup'   => $webgroup,
            'exams'      => $exams,
        ));
    }
}
