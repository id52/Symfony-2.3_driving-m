<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\CategoryPrice;
use My\AppBundle\Entity\Image;
use My\AppBundle\Form\Type\ImageFormType;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractEntityController
{
    protected $routerList = 'admin_categories';
    protected $tmplItem = 'Category/item.html.twig';

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
        /** @var $entity \My\AppBundle\Entity\Category */

        $regions = $this->em->getRepository('AppBundle:Region')->findAll();

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($entity);

            if ($entity->getImage()) {
                $entity->getImage()->setCategory(null);
            }
            $image_id = intval($form->get('image_id')->getData());
            $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
            if ($image) {
                $image->setCategory($entity);
            }

            $this->em->flush();

            $this->em->getRepository('AppBundle:CategoryPrice')->createQueryBuilder('cp')
                ->delete()
                ->where('cp.category = :category')->setParameter(':category', $entity)
                ->getQuery()->execute();
            $prices_edu = $request->get('prices_edu');
            $prices_drv = $request->get('prices_drv');
            $prices_drv_at = $request->get('prices_drv_at');
            $prices_active = $request->get('prices_active');
            foreach ($regions as $region) { /** @var $region \My\AppBundle\Entity\Region */
                $price = new CategoryPrice();
                $price->setPriceEdu(isset($prices_edu[$region->getId()]) ? intval($prices_edu[$region->getId()]) : 0);
                $price->setPriceDrv(isset($prices_drv[$region->getId()]) ? intval($prices_drv[$region->getId()]) : 0);
                if ($entity->getWithAt() && isset($prices_drv_at[$region->getId()])) {
                    $price->setPriceDrvAt(intval($prices_drv_at[$region->getId()]));
                } else {
                    $price->setPriceDrvAt(0);
                }
                $price->setActive(isset($prices_active[$region->getId()]));
                $price->setRegion($region);
                $price->setCategory($entity);
                $this->em->persist($price);
                $this->em->flush();
            }

            if ($id) {
                $this->get('session')->getFlashBag()->add('success', 'success_edited');
                return $this->redirect($this->generateUrl($this->routerList));
            } else {
                $this->get('session')->getFlashBag()->add('success', 'success_added');
                return $this->redirect($this->generateUrl($this->routerItemAdd));
            }
        }

        $prices_edu = array();
        $prices_drv = array();
        $prices_drv_at = array();
        $prices_active = array();
        $regions_prices = $entity->getCategoriesPrices();
        foreach ($regions_prices as $price) { /** @var $price \My\AppBundle\Entity\CategoryPrice */
            $prices_edu[$price->getRegion()->getId()] = $price->getPriceEdu();
            $prices_drv[$price->getRegion()->getId()] = $price->getPriceDrv();
            $prices_drv_at[$price->getRegion()->getId()] = $price->getPriceDrvAt();
            if ($price->getActive()) {
                $prices_active[$price->getRegion()->getId()] = true;
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'          => $form->createView(),
            'entity'        => $entity,
            'imageForm'     => $this->createForm(new ImageFormType(), new Image())->createView(),
            'regions'       => $regions,
            'prices_edu'    => $prices_edu,
            'prices_drv'    => $prices_drv,
            'prices_drv_at' => $prices_drv_at,
            'prices_active' => $prices_active,
        ));
    }

    public function deleteAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        if (count($entity->getUsers())) {
            $this->get('session')->getFlashBag()->add('error', 'errors.category_cant_delete');
        } else {
            $this->em->remove($entity);
            $this->em->flush();
            $this->get('session')->getFlashBag()->add('success', 'success_deleted');
        }

        return $this->redirect($this->generateUrl($this->routerList));
    }
}
