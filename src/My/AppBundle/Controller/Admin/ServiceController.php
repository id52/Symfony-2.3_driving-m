<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\ServicePrice;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractEntityController
{
    protected $tmplItem = 'Service/item.html.twig';

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

        $regions = $this->em->getRepository('AppBundle:Region')->findAll();

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();

            $this->em->getRepository('AppBundle:ServicePrice')->createQueryBuilder('sp')
                ->delete()
                ->where('sp.service = :service')->setParameter(':service', $entity)
                ->getQuery()->execute();
            $prices = $request->get('prices');
            $prices_active = $request->get('prices_active');
            foreach ($regions as $region) { /** @var $region \My\AppBundle\Entity\Region */
                $price = new ServicePrice();
                $price->setPrice(isset($prices[$region->getId()]) ? intval($prices[$region->getId()]) : 0);
                if ($entity->getType() == null) {
                    $price->setActive(isset($prices_active[$region->getId()]));
                } else {
                    $price->setActive(true);
                }
                $price->setRegion($region);
                $price->setService($entity);
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

        $prices = array();
        $prices_active = array();
        $regions_prices = $entity->getRegionsPrices();
        foreach ($regions_prices as $price) { /** @var $price \My\AppBundle\Entity\ServicePrice */
            $prices[$price->getRegion()->getId()] = $price->getPrice();
            if ($price->getActive()) {
                $prices_active[$price->getRegion()->getId()] = true;
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'          => $form->createView(),
            'entity'        => $entity,
            'regions'       => $regions,
            'prices'        => $prices,
            'prices_active' => $prices_active,
        ));
    }
}
