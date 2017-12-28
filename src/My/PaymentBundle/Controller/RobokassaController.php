<?php

namespace My\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RobokassaController extends AbstractController
{
    public function queryAction(Request $request)
    {
        $params = array(
            'login' => $this->container->getParameter('robokassa_login'),
            'pass1' => $this->container->getParameter('robokassa_pass1'),
            'pass2' => $this->container->getParameter('robokassa_pass2'),
            'url'   => $this->container->getParameter('robokassa_url'),
        );

        $id = $request->get('id');
        $uid = $request->get('uid');
        $sum = $request->get('sum');

        $url = $params['url'].'?'.http_build_query(array(
            'MrchLogin'      => $params['login'],
            'OutSum'         => $sum,
            'Desc'           => '',
            'SignatureValue' => md5(implode(':', array(
                $params['login'], $sum, '',
                $params['pass1'],
                'shp_id='.$id, 'shp_uid='.$uid,
            ))),
            'shp_id' => $id,
            'shp_uid' => $uid,
        ));
        return $this->redirect($url);
    }

    public function resultAction(Request $request)
    {
        $path = $this->get('kernel')->getRootDir().'/logs/robokassa.log';
        $data = var_export($_GET, true).PHP_EOL.var_export($_POST, true).PHP_EOL.PHP_EOL;
        file_put_contents($path, $data, FILE_APPEND);

        $params = array(
            'login' => $this->container->getParameter('robokassa_login'),
            'pass1' => $this->container->getParameter('robokassa_pass1'),
            'pass2' => $this->container->getParameter('robokassa_pass2'),
            'url'   => $this->container->getParameter('robokassa_url'),
        );

        $response = '';
        $sid = $request->get('InvId');
        $id = $request->get('shp_id');
        $uid = $request->get('shp_uid');
        $sum = $request->get('OutSum');
        $hash = md5(implode(':', array(
            $sum, $sid,
            $params['pass2'],
            'shp_id='.$id, 'shp_uid='.$uid,
        )));
        if ($hash == strtolower($request->get('SignatureValue'))) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            /** @var $log \My\PaymentBundle\Entity\Log */
            $log = $em->getRepository('PaymentBundle:Log')->find($id);
            if ($log && $sum == $log->getSum() && $uid == $log->getUser()->getId()) {
                $log->setPaid(true);
                $log->setSId($sid);
                $em->persist($log);
                $em->flush();

                $this->afterSuccessPayment($log);

                $response = 'OK'.$sid;
            }
        }
        return new Response($response);
    }

    public function successAction()
    {
        return $this->render('PaymentBundle:Robokassa:success.html.twig');
    }

    public function failAction()
    {
        return $this->render('PaymentBundle:Robokassa:fail.html.twig');
    }

    public function isPaidAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $logRepo = $em->getRepository('PaymentBundle:Log');
        if ($user) {
            $payment = $logRepo->findLastPayment($user);
            $isPaid = $payment->getPaid();
        } else {
            $isPaid = false;
        }

        return new JsonResponse(array(
            'is_paid'      => $isPaid,
            'redirect_url' => $this->generateUrl('homepage')
        ));
    }
}
