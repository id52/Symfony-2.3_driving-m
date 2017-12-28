<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\Holiday;
use My\AppBundle\Entity\SupportCategory;
use My\AppBundle\Entity\SupportMessage;
use My\AppBundle\Form\Type\SupportMessageFormType;
use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class SupportController extends AbstractSettingsController
{
    protected $perms = array('ROLE_MOD_SUPPORT', 'ROLE_MOD_TEACHER');
    protected $routerSettings = 'admin_support_settings';
    protected $tmplSettings = 'Support/settings.html.twig';

    public function dialogsAction(Request $request)
    {
        $categoriesTree = array();
        $categories = $this->em->getRepository('AppBundle:SupportCategory')->createQueryBuilder('sc')
            ->orderBy('sc.createdAt')
            ->orderBy('sc.parent')
            ->getQuery()->getResult();
        foreach ($categories as $category) { /** @var $category \My\AppBundle\Entity\SupportCategory */
            if ($category->getParent() && $category->getType() !== 'teacher') {
                if (!isset($categoriesTree[$category->getParent()->getName()])) {
                    $categoriesTree[$category->getParent()->getName()] = array();
                }
                $categoriesTree[$category->getParent()->getName()][$category->getId()] = $category->getName();
            } else {
                if ($user = $category->getUser()) {
                    $name = $user->getFullName();
                    $categoriesTree[$name][$category->getId()] = $name;
                }
            }
        }

        /** @var $form_factory \Symfony\Component\Form\FormFactory */
        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('support_dialog', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => 'support_dialog',
        ))
            ->add('category_name', 'choice', array(
               'required'    => false,
               'empty_value' => 'choose_option',
               'choices'     => $categoriesTree,
            ))
            ->add('user_first_name', 'text', array('required' => false))
            ->add('user_last_name', 'text', array('required' => false))
            ->add('user_patronymic', 'text', array('required' => false))
            ->add('user_email', 'text', array('required' => false))
            ->add('answered', 'choice', array(
                'required'    => false,
                'empty_value' => 'choose_option',
                'choices'     => array('yes' => 'yes', 'no' => 'no')
            ))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $qb = $this->em->getRepository('AppBundle:SupportDialog')->getModeratorAvailableDialogs($this->getUser(), true);
        $qb->addOrderBy('sd.last_message_time', 'DESC');

        //filter dialogs
        if ($category = $filter_form->get('category_name')->getData()) {
            $qb->andWhere('sd.category = :category')->setParameter('category', $category);
        }
        if ($ufn = $filter_form->get('user_first_name')->getData()) {
            $qb->andWhere('u.first_name = :ufn')->setParameter('ufn', $ufn);
        }
        if ($uln = $filter_form->get('user_last_name')->getData()) {
            $qb->andWhere('u.last_name = :uln')->setParameter('uln', $uln);
        }
        if ($up = $filter_form->get('user_patronymic')->getData()) {
            $qb->andWhere('u.patronymic = :up')->setParameter('up', $up);
        }
        if ($ue = $filter_form->get('user_email')->getData()) {
            $qb->andWhere('u.email = :ue')->setParameter('ue', $ue);
        }
        if ($answered = $filter_form->get('answered')->getData()) {
            $qb->andWhere('sd.answered = :answered')->setParameter('answered', 'yes' == $answered);
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Admin:Support/dialogs.html.twig', array(
            'pagerfanta'  => $pagerfanta,
            'filter_form' => $filter_form->createView(),
        ));
    }

    public function dialogShowAction(Request $request, $id)
    {
        $dialog = $this->em->getRepository('AppBundle:SupportDialog')->find($id);
        if (!$dialog) {
            throw $this->createNotFoundException('Dialog for id "'.$id.'" not found.');
        }

        $message = new SupportMessage();
        $form = $this->createForm(new SupportMessageFormType(), $message);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $dialog->setLastMessageText($message->getText());
            $dialog->setLastMessageTime(new \DateTime());
            $dialog->setLastModerator($this->getUser());
            $dialog->setAnswered(true);
            $dialog->setUserRead(false);

            $message->setDialog($this->em->getReference('AppBundle:SupportDialog', $id));
            $message->setUser($this->getUser());

            $this->em->persist($message);
            $this->em->flush();

            //send notifying email
            $this->get('app.notify')->sendSupportAnswered($dialog->getUser());

            return $this->redirect($this->generateUrl('admin_support_dialog_show', array('id' => $id)));
        }

        return $this->render('AppBundle:Admin:Support/dialog_show.html.twig', array(
            'form'   => $form->createView(),
            'dialog' => $dialog,
        ));
    }

    public function statisticsAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $now = new \DateTime('tomorrow midnight');
        $monthAgo = new \DateTime('today midnight');
        $monthAgo = $monthAgo->modify('-1 month');

        /** @var $form_factory \Symfony\Component\Form\FormFactory */
        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('statistics', 'form', array(), array('csrf_protection' => false))
            ->add('from', 'date', array('data' => $monthAgo))
            ->add('to', 'date', array('data' => $now))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $from = $filter_form->get('from')->getData();
        $to = $filter_form->get('to')->getData();
        $s_category_repo = $this->em->getRepository('AppBundle:SupportCategory');
        $data = array(
            'teachers'      => $s_category_repo->getSupportTeachersStatistics($from, $to),
            'subCategories' => $s_category_repo->getSupportStatistics($from, $to),
        );
        return $this->render('AppBundle:Admin:Support/statistics.html.twig', array(
            'data'        => $data,
            'filter_form' => $filter_form->createView(),
            'form'        => $filter_form->get('from')->getData(),
            'to'          => $filter_form->get('to')->getData(),
        ));
    }

    public function teachersAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $teachers = $this->em->getRepository('AppBundle:SupportCategory')->createQueryBuilder('sc')
            ->select('sc')
            ->innerJoin('sc.user', 'u')->addSelect('u')
            ->where('sc.type = :type')->setParameter('type', 'teacher')
            ->addOrderBy('u.last_name')
            ->addOrderBy('u.first_name')
            ->addOrderBy('u.patronymic')
            ->getQuery()->getResult();

        return $this->render('AppBundle:Admin:Support/teachers.html.twig', array(
            'teachers' => $teachers,
        ));
    }

    public function teacherAddAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => 'user',
        ))
            ->add('email', 'text', array('required' => false))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $qb = $this->em->getRepository('AppBundle:User')->findNotTeachers();

        if ($data = $filter_form->get('email')->getData()) {
            $qb->andWhere('u.email LIKE :email')->setParameter(':email', '%'.$data.'%');
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Admin:Support/teacher.html.twig', array(
            'pagerfanta'  => $pagerfanta,
            'filter_form' => $filter_form->createView(),
        ));
    }

    public function teacherAssignUserAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $user = $this->em->getRepository('AppBundle:User')->find($id);
        $teacher = $user->getTeacher();
        if (!$teacher) {
            $teacher = new SupportCategory();
            $teacher->setName('');
            $teacher->setType('teacher');
            $teacher->setUser($user);
            $user->addModeratedSupportCategory($teacher);
            $user->addRole('ROLE_MOD_TEACHER');

            $this->em->persist($teacher);
            $this->em->persist($user);
            $this->em->flush();
        };

        return $this->redirect($this->generateUrl('admin_support_teacher_edit', array(
            'id' => $teacher->getId(),
        )));
    }

    public function teacherEditAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        /** @var $teacher \My\AppBundle\Entity\SupportCategory */
        $teacher = $this->em->getRepository('AppBundle:SupportCategory')->createQueryBuilder('sc')
            ->where('sc.id = :category_id')->setParameter('category_id', $id)
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if (!$teacher) {
            throw $this->createNotFoundException();
        };

        $form_factory = $this->container->get('form.factory');
        $fb = $form_factory->createBuilder();
        $subjects = $this->em->getRepository('AppBundle:Subject')->createQueryBuilder('s')
            ->leftJoin('s.image', 'i')->addSelect('i')
            ->leftJoin('s.themes', 't')->addSelect('t')
            ->leftJoin('t.slice', 'ts')->addSelect('ts')
            ->leftJoin('t.versions', 'v')->addSelect('v')
            ->leftJoin('v.category', 'c')->addSelect('c')
            ->andWhere('v IS NOT NULL')
            ->addOrderBy('t.subject')
            ->addOrderBy('t.position')
            ->addOrderBy('v.category')
            ->addOrderBy('v.start_date')
            ->getQuery()->getResult();
        foreach ($subjects as $subject) { /** @var $subject \My\AppBundle\Entity\Subject */
            foreach ($subject->getThemes() as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
                $choices = array();
                foreach ($theme->getVersions() as $version) { /** @var $version \My\AppBundle\Entity\TrainingVersion */
                    $v_name = $version->getCategory()->getName().' ('.$version->getStartDate()->format('d.m.Y').')';
                    $choices[$version->getId()] = $v_name;
                }
                $fb->add($theme->getId(), 'choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'choices'  => $choices,
                    'attr'     => array('class' => 'inline'),
                ));
            }
        }
        $fb->setData($teacher->getTVersions());
        $form = $fb->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $teacher->setTVersions($form->getData());
            $this->em->persist($teacher);
            $this->em->flush();
            return $this->redirect($this->generateUrl('admin_support_teachers'));
        }

        return $this->render('AppBundle:Admin/Support:teacher_edit.html.twig', array(
            'teacher'  => $teacher,
            'subjects' => $subjects,
            'form'     => $form->createView(),
        ));
    }

    public function teacherRemoveAssignedUserAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        /** @var $teacher \My\AppBundle\Entity\SupportCategory */
        $teacher = $this->em->getRepository('AppBundle:SupportCategory')->createQueryBuilder('sc')
            ->where('sc.id = :category_id')->setParameter('category_id', $id)
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if ($teacher) {
            $moderatorCategories = $teacher->getUser()->getModeratedSupportCategories();
            $moderator = $moderatorCategories->filter(function ($entry) use ($id) {
                /** @var $entry \My\AppBundle\Entity\SupportCategory */
                return $entry->getId() === intval($id);
            });
            if ($moderator->count()) {
                $teacher->getUser()->removeModeratedSupportCategory($moderator->first());
            }
            $teacher->getUser()->removeRole('ROLE_MOD_TEACHER');
            $this->em->persist($teacher);
            $this->em->remove($teacher);
            $this->em->flush();
        }

        return $this->redirect($this->generateUrl('admin_support_teachers'));
    }

    public function holidaysAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $startDate = new \DateTime('01.01.'.(date('Y')-1));
        $holidays = $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h')
            ->orderBy('h.entry_value')
            ->andWhere('h.entry_value >= :prevYear')->setParameter('prevYear', $startDate)
            ->getQuery()->getResult();

        if ($request->isMethod('post')) {
            $answer = new \stdClass;
            $answer->success = false;
            if ($request->request->get('entryValue') !== null) {
                $date = new \DateTime($request->request->get('entryValue'));
                $holidays = $this->em->getRepository('AppBundle:Holiday')->findBy(array(
                    'entry_value' => $date,
                ));
                if ($holidays) {
                    foreach ($holidays as $holiday) {
                        $this->em->remove($holiday);
                        $this->em->flush();
                    }
                } else {
                    $holiday = new Holiday();
                    $holiday->setEntryValue($date);
                    if (in_array($date->format('N'), array(6, 7))) {
                        $holiday->setException(true);
                    } else {
                        $holiday->setException(false);
                    }
                    $this->em->persist($holiday);
                    $this->em->flush();
                }

                $answer->success = true;
            }
            return new JsonResponse($answer);
        }

        return $this->render('AppBundle:Admin:Support/holidays.html.twig', array(
            'holidays' => $holidays,
        ));
    }

    public function getXmlHolidaysAction(Request $request)
    {
        $files = $request->files->get('files');
        /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = $files[0];

        $strXml = file_get_contents($file->getRealPath());

        if (!$strXml) {
            return new JsonResponse([
                'message' => 'Не удалось загрузить файл с праздниками.',
                'success' => false]);
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($strXml);
        if (!$xml) {
            return new JsonResponse([
                'message' => 'Файл с праздиниками загружен, но не удалось его обработать.',
                'success' => false]);
        }

        $holidays = [];
//        $year = date('Y');
        $year = $xml->attributes()->year;
        foreach ($xml->days->day as $day) {
            if ($day['r']) {
                continue;
            }
            $txtDate = $year.'-'.str_replace('.', '-', $day['d']);
            if ($day['t'] != 2) {
                $holidays[] = [$txtDate, $day['t'] == 3 ? true : false];
            }
        }

        $this->em->getRepository('AppBundle:Holiday')->createQueryBuilder('h')
            ->delete()
            ->andWhere('h.entry_value IN (:holidays)')->setParameter('holidays', $holidays)
            ->getQuery()->execute();

        foreach ($holidays as $holidayDate) {
            $holiday = new Holiday();
            $holiday->setEntryValue(new \DateTime($holidayDate[0]));
            if ($holidayDate[1]) {
                $holiday->setException(true);
            }
            $this->em->persist($holiday);
        }
        $this->em->flush();

        return new JsonResponse([
            'message' =>  'Импортированно '.count($holidays).' праздничных дней',
            'success' => true,
        ]);
    }

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('support_days_to_answer', 'integer', array('attr' => array('class' => 'span1')));

        $fb->add('support_answered_email_enabled', 'checkbox', array('required' => false));
        $fb->add('support_answered_email_title', 'text');
        $fb->add('support_answered_email_text', 'textarea');

        $fb->add('support_answered_email_admin_enabled', 'checkbox', array('required' => false));
        $fb->add('support_answered_email_admin_title', 'text');
        $fb->add('support_answered_email_admin_text', 'textarea');

        return $fb;
    }
}
