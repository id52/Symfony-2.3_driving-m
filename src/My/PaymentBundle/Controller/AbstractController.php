<?php

namespace My\PaymentBundle\Controller;

use My\PaymentBundle\Entity\Log;
use My\PaymentBundle\Entity\RevertLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    protected function afterSuccessPayment(Log $log)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $user = $log->getUser();

        /** @var $notify \My\AppBundle\Service\Notify */
        $notify = $this->get('app.notify');

        $comment = json_decode($log->getComment(), true);

        if (isset($comment['promo_key'])) {
            $this->get('promo')->activateKey($comment['promo_key'], 'first', $user->getId());
        }

        if (!empty($comment['categories'])) {
            $paids = explode(',', $comment['paid']);
            if (!$user->hasRole('ROLE_USER_PAID')) {
                $user->addRole('ROLE_USER_PAID');
                $user->setPayment1Paid(new \DateTime());
                $notify->sendAfterFirstPayment($user);
            } else {
                if (in_array(2, $paids)) {
                    $notify->sendAfterSecondPayment($user);
                }
                if (in_array(3, $paids)) {
                    $notify->sendAfterThirdPayment($user);
                }
            }
            if (in_array(2, $paids) && !$user->hasRole('ROLE_USER_PAID2')) {
                $user->addRole('ROLE_USER_PAID2');
                $user->setPayment2Paid(new \DateTime());
            }
            if (in_array(3, $paids) && !$user->hasRole('ROLE_USER_PAID3')) {
                $user->addRole('ROLE_USER_PAID3');
                $user->setPayment3Paid(new \DateTime());
            }
        }

        if (!empty($comment['services'])) {
            $notify->sendAfterPayment($user);
        }

        $em->persist($user);
        $em->flush();
    }

    protected function afterSuccessRevert(Log $log)
    {
        $user = $log->getUser();

        $comment = json_decode($log->getComment(), true);
        if ($comment != null && isset($comment['paid'])) {
            $paids = $comment['paid'];
            if (('3' == $paids || '1,2,3' == $paids) && in_array('ROLE_USER_PAID3', $user->getRoles())) {
                $user->removeRole('ROLE_USER_PAID3');
            }
            if (('1,2' == $paids || '1,2,3' == $paids) && !in_array('ROLE_USER_PAID3', $user->getRoles())) {
                $user->removeRole('ROLE_USER_PAID');
                $user->removeRole('ROLE_USER_PAID2');
            }

            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
    }

    protected function afterFail(Log $log, $code)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $comment = json_decode($log->getComment(), true);
        $comment['fail'] = $code;
        $log->setComment(json_encode($comment));
        $em->persist($log);
        $em->flush();
    }

    protected function afterFailRevert(RevertLog $log, $code)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $log->setInfo(array_merge($log->getInfo(), array('fail' => $code)));
        $em->persist($log);
        $em->flush();
    }
}
