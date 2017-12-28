<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Form\FormBuilderInterface;

class ArticleController extends AbstractEntityController
{
    protected $listFields = array('title', 'url');
    protected $orderBy = array('position' => 'ASC');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplList = 'Article/list.html.twig';

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

    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('article_styles', 'textarea', array('required' => false));
        return $fb;
    }
}
