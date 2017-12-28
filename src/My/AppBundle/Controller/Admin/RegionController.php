<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\CategoryPrice;
use My\AppBundle\Entity\ServicePrice;
use Symfony\Component\HttpFoundation\Request;

class RegionController extends AbstractEntityController
{
    protected $tmplItem = 'Region/item.html.twig';

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

        $categories = $this->em->getRepository('AppBundle:Category')->findAll();
        $services = $this->em->getRepository('AppBundle:Service')->findAll();

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();

            $this->em->getRepository('AppBundle:CategoryPrice')->createQueryBuilder('cp')
                ->delete()
                ->where('cp.region = :region')->setParameter(':region', $entity)
                ->getQuery()->execute();

            $category_prices_edu = $request->get('category_prices_edu');
            $category_prices_drv = $request->get('category_prices_drv');
            $category_prices_drv_at = $request->get('category_prices_drv_at');
            $category_prices_active = $request->get('category_prices_active');
            foreach ($categories as $category) { /** @var $category \My\AppBundle\Entity\Category */
                $price = new CategoryPrice();
                $c_id = $category->getId();
                $price->setPriceEdu(isset($category_prices_edu[$c_id]) ? intval($category_prices_edu[$c_id]) : 0);
                $price->setPriceDrv(isset($category_prices_drv[$c_id]) ? intval($category_prices_drv[$c_id]) : 0);
                if ($category->getWithAt() && isset($category_prices_drv_at[$c_id])) {
                    $price->setPriceDrvAt(intval($category_prices_drv_at[$c_id]));
                } else {
                    $price->setPriceDrvAt(0);
                }
                $price->setActive(isset($category_prices_active[$c_id]));
                $price->setRegion($entity);
                $price->setCategory($category);
                $this->em->persist($price);
                $this->em->flush();
            }

            $this->em->getRepository('AppBundle:ServicePrice')->createQueryBuilder('sp')
                ->delete()
                ->where('sp.region = :region')->setParameter(':region', $entity)
                ->getQuery()->execute();

            $service_prices = $request->get('service_prices');
            $service_prices_active = $request->get('service_prices_active');
            foreach ($services as $service) { /** @var $service \My\AppBundle\Entity\Service */
                $price = new ServicePrice();
                $s_id = $service->getId();
                $price->setPrice(isset($service_prices[$s_id]) ? intval($service_prices[$s_id]) : 0);
                if ($service->getType() == null) {
                    $price->setActive(isset($service_prices_active[$s_id]));
                } else {
                    $price->setActive(true);
                }
                $price->setRegion($entity);
                $price->setService($service);
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

        $category_prices_edu = array();
        $category_prices_drv = array();
        $category_prices_drv_at = array();
        $category_prices_active = array();
        $prices = $entity->getCategoriesPrices();
        foreach ($prices as $price) { /** @var $price \My\AppBundle\Entity\CategoryPrice */
            $category_prices_edu[$price->getCategory()->getId()] = $price->getPriceEdu();
            $category_prices_drv[$price->getCategory()->getId()] = $price->getPriceDrv();
            $category_prices_drv_at[$price->getCategory()->getId()] = $price->getPriceDrvAt();
            if ($price->getActive()) {
                $category_prices_active[$price->getCategory()->getId()] = true;
            }
        }

        $service_prices = array();
        $service_prices_active = array();
        $prices = $entity->getServicesPrices();
        foreach ($prices as $price) { /** @var $price \My\AppBundle\Entity\ServicePrice */
            $service_prices[$price->getService()->getId()] = $price->getPrice();
            if ($price->getActive()) {
                $service_prices_active[$price->getService()->getId()] = true;
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'                   => $form->createView(),
            'entity'                 => $entity,
            'categories'             => $categories,
            'services'               => $services,
            'category_prices_edu'    => $category_prices_edu,
            'category_prices_drv'    => $category_prices_drv,
            'category_prices_drv_at' => $category_prices_drv_at,
            'category_prices_active' => $category_prices_active,
            'service_prices'         => $service_prices,
            'service_prices_active'  => $service_prices_active,
        ));
    }
}
