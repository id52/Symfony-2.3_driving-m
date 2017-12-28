<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AutomailingController extends AbstractSettingsController
{
    protected $perms = array('ROLE_MOD_FINANCE');
    protected $routerSettings = 'admin_automailing_settings';
    protected $tmplSettings = 'Automailing/settings.html.twig';

    public function promoKeysAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $qb = $this->em->getRepository('AppBundle:PromoKey')->createQueryBuilder('pk')
            ->andWhere('pk.source = :source')->setParameter(':source', 'auto_overdue')
            ->orderBy('pk.created', 'DESC')
        ;

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($request->get('page', 1));
        $pagerfanta->setMaxPerPage(50);

        return $this->render('AppBundle:Admin:Automailing/promo_keys.html.twig', array(
            'pagerfanta'  => $pagerfanta,
        ));
    }

    public function statisticsAction(Request $request)
    {
        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('user', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => 'user',
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
        ;
        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($request);

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->andWhere('u.payment_1_paid IS NULL')
        ;
        if ($data = $filter_form->get('show_from')->getData()) {
            $qb->andWhere('u.created_at >= :show_from')->setParameter(':show_from', $data);
        }
        if ($data = $filter_form->get('show_to')->getData()) {
            $qb->andWhere('u.created_at <= :show_to')->setParameter(':show_to', $data);
        }

        //unpaid
        $unpaid = $qb->getQuery()->getScalarResult();
        $unpaid = $unpaid[0][1];

        //paid by promo code
        $qb = $this->em->getRepository('PaymentBundle:Log')->createQueryBuilder('l')
            ->select('COUNT(u)')
            ->andWhere('l.comment LIKE :comments')->setParameter(':comments', '%categories%')
            ->leftJoin('l.user', 'u')
            ->andWhere('l.user IS NOT NULL')
            ->leftJoin('l.promoKey', 'pk')
            ->andWhere('l.promoKey IS NOT NULL')
            ->andWhere('pk.source = :pk_source')->setParameter(':pk_source', 'auto_overdue')
            ->andWhere('pk.type = :pk_type')->setParameter(':pk_type', 'site_access')
        ;
        if ($data = $filter_form->get('show_from')->getData()) {
            $qb->andWhere('u.created_at >= :show_from')->setParameter(':show_from', $data);
        }
        if ($data = $filter_form->get('show_to')->getData()) {
            $qb->andWhere('u.created_at <= :show_to')->setParameter(':show_to', $data);
        }
        $paidPromo = $qb->getQuery()->getScalarResult();
        $paidPromo = $paidPromo[0][1];

        $qb = $this->em->getRepository('PaymentBundle:Log')->createQueryBuilder('l')
            ->select('SUM(l.sum) summ, pk.overdue_letter_num num')
            ->andWhere('l.comment LIKE :comments')->setParameter(':comments', '%categories%')
            ->leftJoin('l.user', 'u')
            ->andWhere('l.user IS NOT NULL')
            ->leftJoin('l.promoKey', 'pk')
            ->andWhere('l.promoKey IS NOT NULL')
            ->andWhere('pk.source = :pk_source')->setParameter(':pk_source', 'auto_overdue')
            ->andWhere('pk.type = :pk_type')->setParameter(':pk_type', 'site_access')
            ->groupBy('pk.overdue_letter_num')
        ;
        if ($data = $filter_form->get('show_from')->getData()) {
            $qb->andWhere('u.created_at >= :show_from')->setParameter(':show_from', $data);
        }
        if ($data = $filter_form->get('show_to')->getData()) {
            $qb->andWhere('u.created_at <= :show_to')->setParameter(':show_to', $data);
        }
        $dataByDay = $qb->getQuery()->getScalarResult();

        return $this->render('AppBundle:Admin:Automailing/statistics.html.twig', array(
            'filter_form' => $filter_form->createView(),
            'unpaid'      => $unpaid,
            'paidPromo'   => $paidPromo,
            'dataByDay'   => $dataByDay,
        ));
    }

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        for ($i = 1; $i <= 5; $i ++) {
            $fb->add('notify_no_payments_'.$i, 'integer', array('attr' => array('class' => 'span1')));
//          $fb->add('notify_no_payments_promo_expiration_'.$i, 'integer', array('attr' => array('class' => 'span1')));
//          $fb->add('notify_no_payments_promo_discount_'.$i, 'integer', array('attr' => array('class' => 'span1')));
        }

        return $fb;
    }
}
