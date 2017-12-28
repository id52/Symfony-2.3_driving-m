<?php

namespace My\AppBundle\Controller\Admin;

class InfoController extends AbstractEntityController
{
    protected $perms    = array('ROLE_MOD_CONTENT');
    protected $tmplList = 'Info/list.html.twig';
    protected $tmplItem = 'Info/item.html.twig';

    public function deleteAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $this->em->remove($entity);
        $this->em->flush();

        $this->get('session')->getFlashBag()->add('success', 'success_deleted');
        return $this->redirect($this->generateUrl($this->routerList));
    }
}
