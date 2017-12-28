<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SupportCategoryController extends AbstractEntityController
{
    /** @var $repo \My\AppBundle\Repository\SupportCategoryRepository */
    protected $repo;

    protected $routerList = 'admin_support_categories';
    protected $tmplList = 'SupportCategory/list.html.twig';

    public function listAction()
    {
        $categories = array();
        $sc = $this->repo->getOnlyCategories();
        foreach ($sc as $category) { /** @var $category \My\AppBundle\Entity\SupportCategory */
            if (!isset($categories[$category->getType()])) {
                $categories[$category->getType()] = array();
            }
            if (!$category->getParent()) {
                $categories[$category->getType()][$category->getId()] = $category;
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplList, array(
            'categories' => $categories,
        ));
    }

    public function itemAction(Request $request)
    {
        $id = null;
        if ($id = $request->get('id')) {
            $entity = $this->repo->find($id);
            if (!$entity) {
                throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
            }
        } else {
            $entity = new $this->entityClassName();
        }
        /** @var $entity \My\AppBundle\Entity\SupportCategory */

        $form = $this->createForm(new $this->formClassName(), $entity);
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($entity == $entity->getParent()) {
                $form->get('parent')->addError(new FormError('Категория не может быть сама себе родительской!'));
            }

            if ($form->isValid()) {
                $entity->setType('category');
                $this->em->persist($entity);
                $this->em->flush();

                if ($id) {
                    $this->get('session')->getFlashBag()->add('success', 'success_edited');
                    return $this->redirect($this->generateUrl($this->routerList));
                } else {
                    $this->get('session')->getFlashBag()->add('success', 'success_added');
                    return $this->redirect($this->generateUrl($this->routerItemAdd));
                }
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'   => $form->createView(),
            'entity' => $entity,
        ));
    }

    protected function checkPermissions()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }
    }
}
