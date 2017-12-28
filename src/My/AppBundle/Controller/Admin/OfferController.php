<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Entity\Image;
use My\AppBundle\Entity\OfferPrice;
use My\AppBundle\Form\Type\ImageFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class OfferController extends AbstractEntityController
{
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplItem = 'Offer/item.html.twig';
    protected $tmplList = 'Offer/list.html.twig';
    protected $orderBy = array('position' => 'ASC');

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
        /** @var $entity \My\AppBundle\Entity\Offer */

        $regions = $this->em->getRepository('AppBundle:Region')->findAll();
        $categories = $this->em->getRepository('AppBundle:Category')->findAll();

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($entity->getStartedAt() && $entity->getEndedAt() && $entity->getEndedAt() < $entity->getStartedAt()) {
                $form->get('ended_at')->addError(new FormError($this->get('translator')->trans('errors.offer_date')));
            }

            if ($form->isValid()) {
                $this->em->persist($entity);

                if ($entity->getImage()) {
                    $entity->getImage()->setOffer(null);
                }
                $image_id = intval($form->get('image_id')->getData());
                $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
                if ($image) {
                    $image->setOffer($entity);
                }

                $this->em->flush();

                $this->em->getRepository('AppBundle:OfferPrice')->createQueryBuilder('op')
                    ->delete()
                    ->where('op.offer = :offer')->setParameter('offer', $entity)
                    ->getQuery()->execute();
                $prices = $request->get('prices');
                foreach ($regions as $region) { /** @var $region \My\AppBundle\Entity\Region */
                    $rid = $region->getId();
                    foreach ($categories as $category) { /** @var $category \My\AppBundle\Entity\Category */
                        $cid = $category->getId();
                        if ($category->getWithAt()) {
                            $price = new OfferPrice();
                            $price->setOffer($entity);
                            $price->setRegion($region);
                            $price->setCategory($category);
                            $price->setPrice(isset($prices[$rid][$cid.'_a']) ? intval($prices[$rid][$cid.'_a']) : 0);
                            $price->setAt(true);
                            $this->em->persist($price);
                        }
                        $price = new OfferPrice();
                        $price->setOffer($entity);
                        $price->setRegion($region);
                        $price->setCategory($category);
                        $price->setPrice(isset($prices[$rid][$cid]) ? intval($prices[$rid][$cid]) : 0);
                        $this->em->persist($price);
                    }
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

        $prices = array();
        $offer_prices = $entity->getPrices();
        foreach ($offer_prices as $offer_price) { /** @var $offer_price \My\AppBundle\Entity\OfferPrice */
            $region_id = $offer_price->getRegion()->getId();
            $category_id = $offer_price->getCategory()->getId();
            $prices[$region_id][$category_id.($offer_price->getAt() ? '_a' : '')] = $offer_price->getPrice();
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'       => $form->createView(),
            'entity'     => $entity,
            'imageForm'  => $this->createForm(new ImageFormType(), new Image())->createView(),
            'regions'    => $regions,
            'categories' => $categories,
            'prices'     => $prices,
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
