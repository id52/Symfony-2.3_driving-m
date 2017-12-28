<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\Image;
use My\AppBundle\Form\Type\ImageFormType;
use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class FlashBlockItemController extends AbstractEntityController
{
    protected $listFields = array('title');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplItem = 'FlashBlockItem/item.html.twig';
    protected $tmplList = 'FlashBlockItem/list.html.twig';

    /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
    protected $repo;

    /** @var \My\AppBundle\Entity\FlashBlock */
    protected $block;

    protected $categories = array(
        '1_who'       => 'Кто',
        '2_what'      => 'Что',
        '3_where'     => 'Где',
        '4_action'    => 'Действие',
        '5_condition' => 'Условие',
    );

    public function init()
    {
        parent::init();

        $key = $this->getRequest()->get('key');
        $this->block = $this->em->getRepository('AppBundle:FlashBlock')->findOneBy(array('_key' => $key));
        if (!$this->block) {
            throw $this->createNotFoundException('FlashBlock for key "'.$key.'" not found.');
        }

        if ($this->block->getIsSimple()) {
            $this->formClassName = 'My\AppBundle\Form\Type\\'.$this->entityName.'SimpleFormType';
            $this->tmplItem = 'FlashBlockItem/item_simple.html.twig';
            $this->tmplList = 'FlashBlockItem/list_simple.html.twig';
        }
    }

    public function listAction()
    {
        $qb = $this->repo->createQueryBuilder('e');
        $qb->andWhere('e.block = :block')->setParameter(':block', $this->block);
        if ($this->block->getIsSimple()) {
            $qb->orderBy('e.position');
        } else {
            $qb->orderBy('e.category, e.lft');
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));

        return $this->render('AppBundle:Admin:'.$this->tmplList, array(
            'pagerfanta'  => $pagerfanta,
            'list_fields' => $this->listFields,
            'block'       => $this->block,
            'categories'  => $this->categories,
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
        /** @var $entity \My\AppBundle\Entity\FlashBlockItem */

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setBlock($this->block);
            if ($entity->getParent()) {
                $entity->setCategory($entity->getParent()->getCategory());
            }
            if ($entity->getChildren()->count()) {
                foreach ($entity->getChildren() as $child) { /** @var $child \My\AppBundle\Entity\FlashBlockItem */
                    $child->setCategory($entity->getCategory());
                }
            }
            $this->em->persist($entity);

            if ($this->block->getIsSimple()) {
                if ($entity->getImage()) {
                    $entity->getImage()->setFlashBlockItem(null);
                }
                $image_id = intval($form->get('image_id')->getData());
                $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
                if ($image) {
                    $image->setFlashBlockItem($entity);
                }
            }

            $this->em->flush();

            if ($id) {
                $this->get('session')->getFlashBag()->add('success', 'success_edited');
                return $this->redirect($this->generateUrl($this->routerList, array(
                    'key' => $this->block->getKey(),
                )));
            } else {
                $this->get('session')->getFlashBag()->add('success', 'success_added');
                return $this->redirect($this->generateUrl($this->routerItemAdd, array(
                    'key' => $this->block->getKey(),
                )));
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'      => $form->createView(),
            'entity'    => $entity,
            'block'     => $this->block,
            'imageForm' => $this->createForm(new ImageFormType(), new Image())->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $this->em->remove($entity);
        $this->em->flush();

        $this->get('session')->getFlashBag()->add('success', 'success_deleted');
        return $this->redirect($this->generateUrl($this->routerList, array('key' => $this->block->getKey())));
    }

    public function upAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $this->repo->moveUp($entity);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($this->generateUrl($this->routerList, array('key' => $this->block->getKey())));
    }

    public function downAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $this->repo->moveDown($entity);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($this->generateUrl($this->routerList, array('key' => $this->block->getKey())));
    }

    public function simpleUpAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $entity->setPosition($entity->getPosition() - 1);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($this->generateUrl($this->routerList, array('key' => $this->block->getKey())));
    }

    public function simpleDownAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $entity->setPosition($entity->getPosition() + 1);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($this->generateUrl($this->routerList, array('key' => $this->block->getKey())));
    }

    public function getParentListAjaxAction(Request $request)
    {
        $result = array();

        if ($request->isXmlHttpRequest()) {
            /** @var $entity \My\AppBundle\Entity\FlashBlockItem */
            $entity = $this->repo->find($request->get('id'));
            if (!$entity || !$entity->getChildren()->count()) {
                $category = $request->get('category');
                if (array_key_exists($category, $this->categories)) {
                    $qb = $this->repo->createQueryBuilder('e')
                        ->andWhere('e.category = :category')->setParameter(':category', $category)
                        ->andWhere('e.parent IS NULL')
                        ->orderBy('e.category, e.lft')
                    ;
                    if ($entity) {
                        $qb->andWhere('e.id != :id')->setParameter(':id', $entity->getId());
                    }
                    $elements = $qb->getQuery()->execute();
                    foreach ($elements as $element) { /** @var $element \My\AppBundle\Entity\FlashBlockItem */
                        $result[$element->getId()] = $element->getTitle();
                    }
                }
            }
        }

        return new JsonResponse($result);
    }
}
