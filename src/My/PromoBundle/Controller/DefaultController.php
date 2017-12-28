<?php

namespace My\PromoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function checkAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $response = array();

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $response['discount'] = $this->get('promo')->getDiscountByKey($request->get('key'), $request->get('type'));
        }

        return new JsonResponse($response);
    }

    public function triedsWriteAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $id = $this->getUser()->getId();
        $response = array();

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $key = $request->get('key');
            $type = $request->get('type');
            $response['promoKey'] = $this->get('promo')->writeTriedsInKey($key, $type, $id);
        }

        return new JsonResponse($response);
    }

    public function triedsRemoveAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $id = $this->getUser()->getId();
        $response = array();

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $key = $request->get('key');
            $type= $request->get('type');
            $response['promoKey'] = $this->get('promo')->removeTriedsInKey($key, $type, $id);
        }

        return new JsonResponse($response);
    }
}
