<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Pagerfanta\Pagerfanta;
use My\PaymentBundle\Entity\RevertLog;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RevertMoneyController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;

    public function init()
    {
        if ($this->get('security.context')->isGranted('ROLE_MOD_ACCOUNTANT') === false) {
            throw $this->createNotFoundException();
        }
    }

    public function listAction(Request $request)
    {
        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', null, array(
            'csrf_protection'    => false,
            'translation_domain' => 'user',
        ))
            ->add('phone_mobile', 'text', array('required' => false))
            ->add('last_name', 'text', array('required' => false))
            ->add('first_name', 'text', array('required' => false))
            ->add('patronymic', 'text', array('required' => false))
            ->add('email', 'text', array('required' => false))
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :paid')->setParameter(':paid', '%"ROLE_USER_PAID"%')
            ->orderBy('u.created_at')
        ;

        if ($data = $filter_form->get('phone_mobile')->getData()) {
            $qb->andWhere('u.phone_mobile LIKE :phone_mobile')->setParameter(':phone_mobile', '%'.$data.'%');
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
        if ($data = $filter_form->get('email')->getData()) {
            $qb->andWhere('u.email LIKE :email')->setParameter(':email', '%'.$data.'%');
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));

        return $this->render('AppBundle:Admin/RevertMoney:list.html.twig', array(
            'pagerfanta'     => $pagerfanta,
            'filter_form'    => $filter_form->createView(),
        ));
    }

    public function userCardAction($id)
    {
        $user = $this->em->find('AppBundle:User', $id);
        if (!$user) {
            throw $this->createNotFoundException('User for id "'.$id.'" not found.');
        }

        $reg_info = $user->getRegInfo();

        $payments_logs = $this->em->getRepository('PaymentBundle:Log')->createQueryBuilder('l')
            ->andWhere('l.user = :user')->setParameter(':user', $user)
            ->andWhere('l.paid = :paid')->setParameter(':paid', true)
            ->leftJoin('l.revert_logs', 'rl')->addSelect('rl')
            ->addOrderBy('l.updated_at')
            ->getQuery()->getResult();

        $categories = array();
        $categories[1] = array('name'  => 'Обучение (теоретическая часть)');
        $categories[2] = array('name'  => 'Обучение (сдача тестов и экзаменов)');
        $categories[3] = array('name'  => 'Вождение'.($reg_info['with_at'] ? ' (автомат)' : ''));

        $services = array();
        $services_orig = $this->em->getRepository('AppBundle:Service')->createQueryBuilder('s')
            ->addSelect('rp.price')
            ->leftJoin('s.regions_prices', 'rp')
            ->andWhere('rp.active = :active')->setParameter(':active', true)
            ->andWhere('s.type != :type OR s.type IS NULL')->setParameter(':type', 'site_access')
            ->andWhere('rp.region = :region')->setParameter(':region', $user->getRegion())
            ->getQuery()->getArrayResult();
        foreach ($services_orig as $service) {
            $services[$service[0]['id']] = array_merge($service[0], array('price' => $service['price']));
        }

        $paid_payments = array();
        $revertLogs = array();
        foreach ($payments_logs as $plog) { /** @var $plog \My\PaymentBundle\Entity\Log */
            $comment = json_decode($plog->getComment(), true);
            $log = array(
                'id'         => $plog->getId(),
                's_type'     => $plog->getSType(),
                's_id'       => $plog->getSId(),
                'int_ref'    => $plog->getIntRef(),
                'sum'        => $plog->getSum(),
                'paid'       => $plog->getPaid(),
                'comment'    => $comment,
                'created_at' => $plog->getCreatedAt(),
                'updated_at' => $plog->getUpdatedAt(),
                'categories' => array(),
                'services'   => array(),
                'reverts'    => false,
            );

            $reverts = $plog->getRevertLogs();
            foreach ($reverts as $revert) {
                if ($revert->getPaid()) {
                    $revertLogs[] = $revert;
                    $log['reverts'] = true;
                }
            }

            //ID-модератора, который добавил пользователя
            $moderatorId = (!empty($comment['moderator_id'])) ? $comment['moderator_id'] : null;

            $log['services'] = array();
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
                    if ($moderatorId) {
                        $log['moderator_id'] = $moderatorId;
                    }
                    $paid_payments[] = $log;
                }
            }

            $log['categories'] = array();
            if (!empty($comment['categories']) && !empty($comment['paid'])) {
                $paid_nums = explode(',', $comment['paid']);
                foreach ($paid_nums as $paid_num) {
                    if (isset($categories[$paid_num])) {
                        $log['categories'][$paid_num] = $categories[$paid_num];
                    }
                }
                if (count($log['categories']) > 0) {
                    if ($moderatorId) {
                        $log['moderator_id'] = $moderatorId;
                    }
                    $paid_payments[] = $log;
                }
            }
        }

        return $this->render('AppBundle:Admin/RevertMoney:user_card.html.twig', array(
            'user'            => $user,
            'paid_payments'   => $paid_payments,
            'revert_payments' => $revertLogs,
        ));
    }

    public function revertAction($logId)
    {
        $log = $this->em->getRepository('PaymentBundle:Log')->find($logId);
        if (!$log) {
            throw $this->createNotFoundException('Log for id "'.$logId.'" not found.');
        }

        $reverts = $log->getRevertLogs();
        foreach ($reverts as $revert) {
            if ($revert->getPaid()) {
                throw $this->createNotFoundException('A refund already made.');
            }
        }

        $user = $log->getUser();
        $roles = $user->getRoles();
        $comment = $comment = json_decode($log->getComment(), true);
        if (isset($comment['paid'])) {
            $paid_nums = explode(',', $comment['paid']);

            if (in_array('2', $paid_nums)
                && !in_array('3', $paid_nums)
                && in_array('ROLE_USER_PAID3', $roles)
            ) {
                throw $this->createNotFoundException('Not canceled the second payment.');
            }
        }

        $revertLog = new RevertLog();
        $revertLog->setPaymentLog($log);
        $revertLog->setModerator($this->getUser());
        $this->em->persist($revertLog);
        $this->em->flush();

        return $this->redirect($this->generateUrl('psb_query', array(
            'id'     => $log->getId(),
            'uid'    => $log->getUser()->getId(),
            'trtype' => 14,
        )));
    }
}
