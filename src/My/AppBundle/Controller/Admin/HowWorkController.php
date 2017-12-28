<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\Image;
use My\AppBundle\Form\Type\ImageFormType;
use Symfony\Component\HttpFoundation\Request;

class HowWorkController extends AbstractEntityController
{
    protected $listFields = array('title');
    protected $orderBy = array('position' => 'ASC');
    protected $routerList = 'admin_how_work_list';
    protected $tmplItem = 'HowWork/item.html.twig';
    protected $tmplList = 'HowWork/list.html.twig';

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
        /** @var $entity \My\AppBundle\Entity\HowWork */

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($entity);

                if ($entity->getImage()) {
                    $entity->getImage()->setHowWork(null);
                }
                $image_id = intval($form->get('image_id')->getData());
                $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
                if ($image) {
                    $image->setHowWork($entity);
                }

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
            'form'      => $form->createView(),
            'entity'    => $entity,
            'imageForm' => $this->createForm(new ImageFormType(), new Image())->createView(),
        ));
    }

    public function upAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $entity->setPosition($entity->getPosition() - 1);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($this->generateUrl($this->routerList));
    }

    public function downAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $entity->setPosition($entity->getPosition() + 1);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($this->generateUrl($this->routerList));
    }
}
