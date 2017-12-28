<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OfficeController extends AbstractEntityController
{
    protected $listFields = array('region', 'title');
    protected $orderBy = array('region' => 'ASC', 'position' => 'ASC');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplItem = 'Office/item.html.twig';
    protected $tmplList = 'Office/list.html.twig';

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

        $phones = (array)$request->get('office_phones');
        if ($phones) {
            $entity->setPhones(array());
        }
        $phones_active = $request->get('office_phones_active');
        foreach ($phones as $key => $value) {
            $value = trim($value);
            if ($value) {
                $entity->setPhones((array)$entity->getPhones() + array($value => isset($phones_active[$key])));
            }
        }

        $emails = (array)$request->get('office_emails');
        if ($emails) {
            $entity->setEmails(array());
        }
        $emails_active = $request->get('office_emails_active');
        foreach ($emails as $key => $value) {
            $value = trim($value);
            if ($value) {
                $entity->setEmails((array)$entity->getEmails() + array($value => isset($emails_active[$key])));
            }
        }

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($request->isMethod('post')) {
            if ($form->isValid()) {
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
            'form'      => $form->createView(),
            'entity'    => $entity,
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
