<?php

namespace My\AppBundle\Controller;

use Doctrine\ORM\Query;
use My\AppBundle\Exception\AppResponseException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\LockedException;

abstract class MyAbstract extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;

    public $settings = array();

    public function init()
    {
        $cntxt = $this->get('security.context');
        $twig = $this->container->get('twig');

        $now = new \DateTime();

        if ($this->user->isLocked()) {
            throw new LockedException();
        }

        if (!$cntxt->isGranted('ROLE_MOD') && $cntxt->isGranted('ROLE_USER_PAID3')) {
            $limit = clone $this->user->getPayment3Paid();
            $limit->add(new \DateInterval('P'.$this->settings['access_time_after_3_payment'].'D'));

            if ($limit < $now) {
                $this->user->setExpired(true);
                $this->em->persist($this->user);
                $this->em->flush();
                throw new AccountExpiredException();
            }
        }

        $notify = $this->user->getRequiredNotify();
        if ($notify) {
            $this->user->setRequiredNotify(null);
            $this->em->persist($this->user);
            $this->em->flush();

            throw new AppResponseException($this->redirect($this->generateUrl('my_notify_read', array(
                'id' => $notify->getId(),
            ))));
        }

        if ($this->user->getOffline()
            && !$cntxt->isGranted('ROLE_USER_FULL_PROFILE')
            && $this->getRequest()->get('_route') != 'my_profile_edit'
            && !$this->getRequest()->isXmlHttpRequest()
        ) {
            throw new AppResponseException($this->redirect($this->generateUrl('my_profile_edit')));
        }

        if ($cntxt->isGranted('ROLE_USER_PAID2') && !$cntxt->isGranted('ROLE_USER_PAID3')) {
            $popup_info = $this->user->getPopupInfo();
            if (!isset($popup_info['paid_2'])) {
                $popup_info['paid_2'] = array();
            }

            $limit = clone $this->user->getPayment2Paid();
            $limit->add(new \DateInterval('P'.$this->settings['access_time_after_2_payment'].'D'));

            $n_popups = array();
            $v_popups = array();
            for ($i = 1; $i <= 10; $i ++) {
                $days = $this->settings['access_time_end_popup_after_2_payment_'.$i];
                if ($days > 0) {
                    $date = clone $limit;
                    $date->sub(new \DateInterval('P'.$days.'D'));
                    if ($date < $now) {
                        $n_popups[] = $i;
                        if (in_array($i, $popup_info['paid_2'])) {
                            $v_popups[] = $i;
                        }
                    }
                }
            }

            if ($n_popups != $v_popups) {
                $twig = $this->container->get('twig');
                $title = $this->settings['before_access_time_end_after_2_payment_popup_title'];
                $twig->addGlobal('before_access_time_end_after_2_payment_popup_title', $title);

                $text = $this->settings['before_access_time_end_after_2_payment_popup_text'];
                if ($this->user->getSex()) {
                    $sex = $this->user->getSex() == 'female' ? 'Уважаемая' : 'Уважаемый';
                } else {
                    $sex = 'Уважаемый/ая';
                }
                $text = str_replace('{{ dear }}', $sex, $text);
                $text = str_replace('{{ last_name }}', $this->user->getLastName(), $text);
                $text = str_replace('{{ first_name }}', $this->user->getFirstName(), $text);
                $text = str_replace('{{ patronymic }}', $this->user->getPatronymic(), $text);
                $text = str_replace('{{ demo_period }}', $this->settings['access_time_after_2_payment'], $text);
                for ($i = 1; $i <= 5; $i ++) {
                    $text = str_replace('{{ sign_'.$i.' }}', $this->settings['sign_'.$i], $text);
                }
                $text = str_replace('{{ end_date }}', $limit->format('d.m.Y'), $text);
                $text = str_replace('{{ days_before_end }}', intval($limit->diff($now)->format('%a')) + 1, $text);

                $region = $this->user->getRegion();
                $category = $this->user->getCategory();
                $price = $category->getPriceByRegion($region);
                $reg_info = $this->user->getRegInfo();
                $payments_logs = $this->em->getRepository('PaymentBundle:Log')->createQueryBuilder('l')
                    ->andWhere('l.user = :user')->setParameter(':user', $this->user)
                    ->andWhere('l.paid = :paid')->setParameter(':paid', true)
                    ->leftJoin('l.revert_log', 'rl')->addSelect('rl')
                    ->addOrderBy('l.updated_at')
                    ->getQuery()->getResult();
                $paid_sum2 = 0;
                foreach ($payments_logs as $log) { /** @var $log \My\PaymentBundle\Entity\Log */
                    $comment = json_decode($log->getComment(), true);
                    if (!empty($comment['categories']) && !empty($comment['paid']) && !$log->getRevertLog()) {
                        if ($comment['paid'] == '1,2') {
                            $paid_sum2 = $log->getSum();
                        }
                    }
                }
                $text = str_replace('{{ sum }}', $price->getSum2p2($reg_info['with_at'], $paid_sum2), $text);

                $twig->addGlobal('before_access_time_end_after_2_payment_popup_text', $text);
            }
        }

        $infosCnt = $this->em->getRepository('AppBundle:Info')->createQueryBuilder('i')
            ->select('COUNT(i.id) as cnt')
            ->andWhere('i.release_time <= :time')->setParameter('time', new \DateTime())
            ->getQuery()->getSingleScalarResult();

        $user = $this->getUser(); /** @var $user \My\AppBundle\Entity\User */

        $unreadedInfosCnt = $infosCnt - count($user->getReadInfos());
        $twig->addGlobal('unreadedInfosCnt', $unreadedInfosCnt);
    }

    protected function getErrorMessages(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $errors['errors'][] = $error->getMessage();
        }

        foreach ($form->all() as $child) { /** @var $child \Symfony\Component\Form\Form */
            if (!$child->isValid()) {
                $errors['children'][$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    protected function getUserStat()
    {
        $version = $this->em->getRepository('AppBundle:TrainingVersion')->getVersionByUser($this->user);
        if (!$version) {
            throw $this->createNotFoundException('Training version not found.');
        }

        $questions = $this->em->getRepository('AppBundle:Question')->createQueryBuilder('q')
            ->leftJoin('q.theme', 't')->addSelect('t')
            ->leftJoin('t.subject', 's')
            ->andWhere('q.is_pdd = :is_pdd')->setParameter(':is_pdd', true)
            ->leftJoin('q.versions', 'v')
            ->andWhere('v = :version')->setParameter(':version', $version)
            ->addOrderBy('t.position')
            ->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->execute();

        $qids = array();
        $themes_nums = array();
        $num = 0;
        foreach ($questions as $question) { /** @var $question \My\AppBundle\Entity\Question */
            $theme_id = $question->getTheme()->getId();
            $qids[$question->getId()] = $theme_id;
            if (!isset($themes_nums[$theme_id])) {
                $themes_nums[$theme_id] = ++$num;
            }
        }

        $themes_stat = array();
        $all_stat = array();

        $logs = $this->em->getRepository('AppBundle:TestLog')->createQueryBuilder('tl')
            ->andWhere('tl.user = :user')->setParameter(':user', $this->user)
            ->addOrderBy('tl.started_at', 'DESC')
            ->getQuery()->getArrayResult();
        $this->processingLogs($logs, $qids, $themes_nums, $themes_stat, $all_stat);

        $logs = $this->em->getRepository('AppBundle:TestKnowledgeLog')->createQueryBuilder('tkl')
            ->andWhere('tkl.user = :user')->setParameter(':user', $this->user)
            ->addOrderBy('tkl.started_at', 'DESC')
            ->getQuery()->getArrayResult();
        $this->processingLogs($logs, $qids, $themes_nums, $themes_stat, $all_stat);

        foreach ($themes_stat as $key => $value) {
            $themes_stat[$key]['proc'] = round($value['correct'] / $value['all'] * 100);
        }

        uasort($themes_stat, function ($a, $b) {
            if ($a['proc'] == $b['proc']) {
                if ($a['all'] == $b['all']) {
                    return $a['num'] < $b['num'] ? -1 : 1;
                } else {
                    return $a['all'] > $b['all'] ? -1 : 1;
                }
            } else {
                return $a['proc'] > $b['proc'] ? -1 : 1;
            }
        });

        krsort($all_stat);

        return array(
            'themes' => $themes_stat,
            'all'    => $all_stat,
        );
    }

    private function processingLogs($logs, $qids, $themes_nums, &$themes_stat, &$all_stat)
    {
        foreach ($logs as $log) {
            /** @var $started_at \DateTime */
            $started_at = $log['started_at'];
            $started_at_key = $started_at->format('YmdHis');

            if (!isset($all_stat[$started_at_key])) {
                $all_stat[$started_at_key] = array(
                    'started_at' => $log['started_at'],
                    'ended_at'   => $log['ended_at'],
                    'passed'     => $log['passed'],
                    'themes'     => array(),
                );
            }

            foreach ($log['answers'] as $key => $answer) {
                if (is_array($answer) && isset($qids[$log['questions'][$key]])) {
                    $theme_id = $qids[$log['questions'][$key]];

                    if (!isset($themes_stat[$theme_id])) {
                        $themes_stat[$theme_id] = array(
                            'num'     => $themes_nums[$theme_id],
                            'correct' => 0,
                            'all'     => 0,
                        );
                    }

                    if (!isset($all_stat[$started_at_key]['themes'][$theme_id])) {
                        $all_stat[$started_at_key]['themes'][$theme_id] = array(
                            'correct' => 0,
                            'all'     => 0,
                        );
                    }

                    if ($answer['correct']) {
                        $themes_stat[$theme_id]['correct']++;
                        $all_stat[$started_at_key]['themes'][$theme_id]['correct']++;
                    }
                    $themes_stat[$theme_id]['all']++;
                    $all_stat[$started_at_key]['themes'][$theme_id]['all']++;
                }
            }

            foreach ($all_stat[$started_at_key]['themes'] as $key => $value) {
                $all_stat[$started_at_key]['themes'][$key]['proc'] = round($value['correct'] / $value['all'] * 100);
            }
        }
    }
}
