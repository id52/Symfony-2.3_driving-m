<?php

namespace My\PaymentBundle\Controller;

use My\PaymentBundle\Entity\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PsbController extends AbstractController
{
    public function queryAction($id, $uid, $trtype = 1)
    {
        if ($trtype == 14) {
            $cntxt = $this->get('security.context');
            if (!$cntxt->isGranted('ROLE_MOD_ACCOUNTANT')) {
                throw new AccessDeniedHttpException();
            }
        }

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        /** @var $log \My\PaymentBundle\Entity\Log */
        $log = $em->getRepository('PaymentBundle:Log')->createQueryBuilder('l')
            ->andWhere('l.id = :id')->setParameter('id', $id)
            ->andWhere('l.user = :user')->setParameter('user', $uid)
            ->andWhere('l.s_type = :s_type')->setParameter('s_type', 'psb')
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
        if (!$log) {
            throw $this->createNotFoundException();
        }

        $hmac_params = $this->generateHmacParameters($log, $trtype);

        $psign = $this->generateHmac($hmac_params['parameters'], $hmac_params['key']);

        if (!array_key_exists('MERCHANT', $hmac_params['parameters'])) {
            $hmac_params['parameters']['MERCHANT'] = $hmac_params['merchant'];
        }

        return $this->render('PaymentBundle:Psb:query.html.twig', array(
            'url'         => $hmac_params['url'],
            'hmac_params' => $hmac_params['parameters'],
            'psign'       => $psign,
        ));
    }

    public function resultAction(Request $request)
    {
        $path = $this->get('kernel')->getRootDir().'/logs/psb.log';
        $data = var_export($_GET, true).PHP_EOL.var_export($_POST, true).PHP_EOL.PHP_EOL;
        file_put_contents($path, $data, FILE_APPEND);

        $response = '';

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $log = $em->find('PaymentBundle:Log', intval($request->get('ORDER')));
        if ($log) {
            $params = array(
                'key'            => $this->container->getParameter('psb_key'),
                'terminal_id'    => $this->container->getParameter('psb_terminal_id'),
                'merchant_id'    => $this->container->getParameter('psb_merchant_id'),
                'merchant_name'  => $this->container->getParameter('psb_merchant_name'),
                'merchant_email' => $this->container->getParameter('psb_merchant_email'),
                'url'            => $this->container->getParameter('psb_url'),
            );

            $trtype = intval($request->get('TRTYPE'));
            switch ($trtype) {
                case 1: // Оплата
                    $hmac_params = array(
                        $request->get('AMOUNT'),
                        $request->get('CURRENCY'),
                        $request->get('ORDER'),
                        $request->get('MERCH_NAME'),
                        $request->get('MERCHANT'),
                        $request->get('TERMINAL'),
                        $request->get('EMAIL'),
                        $request->get('TRTYPE'),
                        $request->get('TIMESTAMP'),
                        $request->get('NONCE'),
                        $request->get('BACKREF'),
                        $request->get('RESULT'),
                        $request->get('RC'),
                        $request->get('RCTEXT'),
                        $request->get('AUTHCODE'),
                        $request->get('RRN'),
                        $request->get('INT_REF'),
                    );
                    break;
                case 14: // Возврат
                    $hmac_params = array(
                        $request->get('ORDER'),
                        $request->get('AMOUNT'),
                        $request->get('CURRENCY'),
                        $request->get('ORG_AMOUNT'),
                        $request->get('RRN'),
                        $request->get('INT_REF'),
                        $request->get('TRTYPE'),
                        $request->get('TERMINAL'),
                        $request->get('BACKREF'),
                        $request->get('EMAIL'),
                        $request->get('TIMESTAMP'),
                        $request->get('NONCE'),
                        $request->get('RESULT'),
                        $request->get('RC'),
                        $request->get('RCTEXT'),
                    );
                    break;
                default:
                    $hmac_params = array();
            }

            $psign = $this->generateHmac($hmac_params, $params['key']);

            if ($psign == strtolower($request->get('P_SIGN'))
                && $request->get('CURRENCY') == 'RUB'
                && $log && $request->get('AMOUNT') == $log->getSum()
            ) {
                $result = intval($request->get('RESULT'));
                switch ($trtype) {
                    case 1: // Оплата
                        if ($result == 0) {
                            $log->setPaid(true);
                            $log->setSId(trim($request->get('RRN')));
                            $log->setIntRef(trim($request->get('INT_REF')));

                            $comment = json_decode($log->getComment(), true);
                            $comment['authcode'] = trim($request->get('AUTHCODE'));
                            $comment['name'] = trim($request->get('NAME'));
                            $comment['card'] = trim($request->get('CARD'));
                            $log->setComment(json_encode($comment));

                            $em->persist($log);
                            $em->flush();

                            $this->afterSuccessPayment($log);

                            $response = 'OK';
                        } else {
                            $this->afterFail($log, $result);
                        }
                        break;
                    case 14: // Возврат
                        $revert = $em->getRepository('PaymentBundle:RevertLog')->createQueryBuilder('rl')
                            ->andWhere('rl.paid = 0 AND rl.info = :info')->setParameter('info', 'a:0:{}')
                            ->addOrderBy('rl.created_at', 'DESC')
                            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
                        if ($result == 0) {
                            $revert->setPaid(true);
                            $em->persist($revert);
                            $em->flush();

                            $this->afterSuccessRevert($log);

                            $response = 'OK';
                        } else {
                            $this->afterFailRevert($revert, $result);
                        }
                        break;
                }
            }
        }

        return new Response($response);
    }

    protected function generateHmac(array $params, $key)
    {
        $str = '';
        foreach ($params as $k => $v) {
            if ($k !== 'DESC') {
                $str .= strlen($v).$v;
            }
        }
        return hash_hmac('sha1', $str, pack('H*', $key));
    }

    /**
     * @param $log \My\PaymentBundle\Entity\Log
     * @param $trtype integer
     * @return array
     */
    protected function generateHmacParameters($log, $trtype)
    {
        $params = array(
            'key'            => $this->container->getParameter('psb_key'),
            'terminal_id'    => $this->container->getParameter('psb_terminal_id'),
            'merchant_id'    => $this->container->getParameter('psb_merchant_id'),
            'merchant_name'  => $this->container->getParameter('psb_merchant_name'),
            'merchant_email' => $this->container->getParameter('psb_merchant_email'),
            'url'            => $this->container->getParameter('psb_url'),
        );

        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $timestamp = $date->format('YmdHis');

        switch ($trtype) {
            case 1:
                $comments = json_decode($log->getComment(), true);
                $hmac_params = array(
                    'AMOUNT'     => $log->getSum(),
                    'CURRENCY'   => 'RUB',
                    'ORDER'      => sprintf('%06s', $log->getId()),
                    'DESC'       => isset($comments['desc']) ? $comments['desc'] : '-',
                    'MERCH_NAME' => $params['merchant_name'],
                    'MERCHANT'   => $params['merchant_id'],
                    'TERMINAL'   => $params['terminal_id'],
                    'EMAIL'      => $params['merchant_email'],
                    'TRTYPE'     => 1,
                    'TIMESTAMP'  => $timestamp,
                    'NONCE'      => md5(rand(100000, 9000000)),
                    'BACKREF'    => $this->generateUrl('psb_info', array(), true),
                );
                break;
            case 14:
                $backurl = $this->generateUrl('psb_info_revert', array('uid' => $log->getUser()->getId()), true);
                $hmac_params = array(
                    'ORDER'      => sprintf('%06s', $log->getId()),
                    'AMOUNT'     => $log->getSum(),
                    'CURRENCY'   => 'RUB',
                    'ORG_AMOUNT' => $log->getSum(),
                    'RRN'        => $log->getSId(),
                    'INT_REF'    => $log->getIntRef(),
                    'TRTYPE'     => 14,
                    'TERMINAL'   => $params['terminal_id'],
                    'BACKREF'    => $backurl,
                    'EMAIL'      => $params['merchant_email'],
                    'TIMESTAMP'  => $timestamp,
                    'NONCE'      => md5(rand(100000, 9000000)),
                );
                break;
            default:
                throw $this->createNotFoundException();
        }

        return array(
            'parameters' => $hmac_params,
            'key'        => $params['key'],
            'url'        => $params['url'],
            'merchant'   => $params['merchant_id'],
        );
    }

    public function infoAction()
    {
        return $this->render('PaymentBundle:Psb:info.html.twig');
    }

    public function getStatusAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $status = 'not_paid';

        if ($this->getUser()) {
            $log = $em->getRepository('PaymentBundle:Log')->findOneBy([
                'user'   => $this->getUser(),
                's_type' => 'psb',
            ], ['updated_at' => 'DESC']);
            if ($log) {
                if ($log->getPaid()) {
                    $status = 'paid';
                } else {
                    $comment = json_decode($log->getComment(), true);
                    if (isset($comment['fail'])) {
                        $status = 'fail';
                    }
                }
            }
        }

        return new JsonResponse($status);
    }

    public function infoRevertAction($uid)
    {
        $this->get('session')->getFlashBag()->add('info', 'success_revert_money');

        $url = $this->generateUrl('admin_revert_money_user_card', array('id' => $uid));
        return $this->redirect($url);
    }
}
