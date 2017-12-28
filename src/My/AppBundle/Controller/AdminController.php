<?php

namespace My\AppBundle\Controller;

use Doctrine\ORM\Query\Expr;
use My\AppBundle\Entity\Image;
use My\AppBundle\Entity\Category;
use My\AppBundle\Entity\Region;
use My\AppBundle\Form\Type\ImageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Assert;

class AdminController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;
    public $settings = array();

    public function init()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_MOD')) {
            throw $this->createNotFoundException();
        }
    }

    public function indexAction()
    {
        $route = null;

        /** @var $cntxt \Symfony\Component\Security\Core\SecurityContext */
        $cntxt = $this->get('security.context');

        if ($cntxt->isGranted(array('ROLE_TRANSLATOR_TJ'))) {
            $route = 'admin_question_translations_tj';
        }

        if ($cntxt->isGranted(array('ROLE_TRANSLATOR_UZ'))) {
            $route = 'admin_question_translations_uz';
        }

        if ($cntxt->isGranted(array('ROLE_TRANSLATOR_KG'))) {
            $route = 'admin_question_translations_kg';
        }

        if ($cntxt->isGranted('ROLE_MOD_PROMO')) {
            $route = 'admin_promos';
        }
        if ($cntxt->isGranted('ROLE_MOD_MAILING')) {
            $route = 'admin_mailing';
        }
        if ($cntxt->isGranted(array('ROLE_MOD_SUPPORT', 'ROLE_MOD_TEACHER'))) {
            $route = 'admin_support_dialogs';
        }
        if ($cntxt->isGranted('ROLE_MOD_PARADOX_USERS')) {
            $route = 'admin_paradox_users';
        }
        if ($cntxt->isGranted('ROLE_MOD_PRECHECK_USERS')) {
            $route = 'admin_precheck_users';
        }
        if ($cntxt->isGranted('ROLE_MOD_ADD_USER')) {
            $route = 'admin_users_add_simple';
        }
        if ($cntxt->isGranted('ROLE_MOD_CONTENT')) {
            $route = 'admin_index_video';
        }
        if ($cntxt->isGranted('ROLE_MOD_ACCOUNTANT')) {
            $route = 'admin_revert_money_user_list';
        }
        if ($cntxt->isGranted('ROLE_ADMIN')) {
            $route = 'admin_settings_common';
        }

        if (!$route) {
            throw new AccessDeniedException();
        }

        return $this->redirect($this->generateUrl($route));
    }

    public function imageAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $result = array();

        if ($request->isMethod('post')) {
            $image = $this->em->getRepository('AppBundle:Image')->find($request->get('image_id'));
            if (!$image) {
                $image = new Image();
            }
            $form = $this->createForm(new ImageFormType(), $image);
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var $image \My\AppBundle\Entity\Image */
                $image = $form->getData();
                $image->setUpdatedAt(new \DateTime());
                $this->em->persist($image);
                $this->em->flush();

                $result['image_id'] = $image->getId();
                $result['image_view'] = $this->renderView('AppBundle:Admin:image_view.html.twig', array(
                    'image'  => $image,
                    'filter' => $request->get('filter', 'image_small'),
                ));
            } else {
                foreach ($form->getErrors() as $error) {
                    $result['errors'][] = $error->getMessage();
                }
            }
        } else {
            $result['errors'][] = $this->get('translator')->trans('errors.not_post');
        }

        return new JsonResponse($result);
    }

    public function imageViewAjaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $trans = $this->get('translator');
        $result = array();

        if ($request->isMethod('post')) {
            $image_id = $request->get('image_id');
            $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
            if ($image) {
                $result['image_view'] = $this->renderView('AppBundle:Admin:image_view.html.twig', array(
                    'image'  => $image,
                    'filter' => $request->get('filter', 'image_small'),
                ));
            } else {
                $result['errors'][] = $trans->trans('errors.image_not_found', array('%image_id%' => $image_id));
            }
        } else {
            $result['errors'][] = $trans->trans('errors.not_post');
        }

        return new JsonResponse($result);
    }

    public function calcPayAmountAjaxAction(Request $request)
    {
        $region_id = $request->get('region_id');
        $category_id = $request->get('category_id');
        if (!$category_id || !$region_id) {
            throw $this->createNotFoundException();
        }
        $region = $this->em->getRepository('AppBundle:Region')->find($region_id);
        if (!$region) {
            throw $this->createNotFoundException('Region not found');
        }
        $category = $this->em->getRepository('AppBundle:Category')->find($category_id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $paid = $request->get('paid');
        $price = $category->getPriceByRegion($region);
        $with_at = ($category->getWithAt() && intval($request->get('with_at')));

        $sum = 0;
        switch ($paid) {
            case 'paid_2':
                $sum = $price->getSum2();
                break;
            case 'paid_3':
                $sum = $price->getSum1($with_at);
                break;
            case 'paid_3_f':
                $sum = $price->getSum($with_at);
                break;
            default:
                $offer = $this->em->find('AppBundle:Offer', $paid);
                if (!$offer) {
                    $offer = $this->em->find('AppBundle:OfferStudent', $paid);
                }
                if ($offer) {
                    $sum = $offer->getPrice($with_at, $region->getId(), $category->getId());
                }
        }
        return new JsonResponse(array('sum' => $sum));
    }
}
