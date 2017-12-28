<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints as Assert;

class PromoController extends AbstractEntityController
{
    protected $entityBundleName = 'PromoBundle';
    protected $entityName = 'Campaign';
    protected $entityNameS = 'promo';
    protected $formClassName = 'My\AppBundle\Form\Type\PromoFormType';
    protected $perms = array('ROLE_MOD_PROMO');
    protected $tmplItem = 'Promo/item.html.twig';
    protected $tmplList = 'Promo/list.html.twig';

    public function listAction()
    {
        $request = $this->getRequest();

        $fb = $this->createFormBuilder(array(), array('csrf_protection' => false))
            ->add('active', 'choice', array(
                'label'       => 'Активна?',
                'choices'     => array(
                    2 => 'Да',
                    1 => 'Нет',
                ),
            ))
        ;
        $fb->setMethod('get');
        $form = $fb->getForm();
        $form->handleRequest($request);

        $qb = $this->repo->createQueryBuilder('e');
        foreach ($this->orderBy as $field => $order) {
            $qb->addOrderBy('e.'.$field, $order);
        }

        if (($active = $form->get('active')->getData()) && $active == 1) {
            $qb
                ->andWhere('e.active = :active')->setParameter('active', 0)
                ->orWhere('e.used_from > :now OR e.used_to < :now')->setParameter('now', new \DateTime())
            ;
        } else {
            $qb
                ->andWhere('e.active = :active')->setParameter('active', 1)
                ->andWhere('e.used_from <= :now AND e.used_to >= :now')->setParameter('now', new \DateTime())
            ;
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));
        $results = $pagerfanta->getCurrentPageResults();

        $keysCountArray = array();
        $triedsArray = array();
        $rezervArray = array();
        $activeArray = array();
        $delArray = array();
        foreach ($results as $promo) {
            /** @var $promo \My\PromoBundle\Entity\Campaign */

            $keys = $promo->getKeys();
            $del = 1;
            foreach ($keys as $key) {
                if (!isset($keysCountArray[$promo->getId()])) {
                    $keysCountArray[$promo->getId()] = 0;
                }
                if (!isset($triedsArray[$promo->getId()])) {
                    $triedsArray[$promo->getId()] = 0;
                }
                if (!isset($rezervArray[$promo->getId()])) {
                    $rezervArray[$promo->getId()] = 0;
                }
                if (!isset($activeArray[$promo->getId()])) {
                    $activeArray[$promo->getId()] = 0;
                }

                $keysCountArray[$promo->getId()]++;

                $trieds = count($key->getTriedEnters());
                $rezerv = count($key->getRezerv());
                $active = count($key->getActiveUser());

                if ($trieds || $rezerv || $active) {
                    $del = 0;
                }

                $triedsArray[$promo->getId()] += $trieds;
                $rezervArray[$promo->getId()] += $rezerv;
                $activeArray[$promo->getId()] += $active;
                $delArray[$promo->getId()] = $del;
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplList, array(
            'pagerfanta'    => $pagerfanta,
            'list_fields'   => $this->listFields,
            'count_keys'    => $keysCountArray,
            'trieds'        => $triedsArray,
            'rezerv'        => $rezervArray,
            'active'        => $activeArray,
            'del'        => $delArray,
            'filter_form'    => $form->createView(),
        ));
    }

    public function itemAction(Request $request)
    {

        if ($id = $request->get('id')) {
            $entity = $this->repo->find($id);
            if (!$entity) {
                throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
            }
        } else {
            $entity = new $this->entityClassName();
        }

        /** @var $entity \My\PromoBundle\Entity\Campaign */
        $keys = $entity->getKeys();
        $del = 1;
        foreach ($keys as $key) {
            $trieds = count($key->getTriedEnters());
            $rezerv = count($key->getRezerv());
            $active = count($key->getActiveUser());

            if ($trieds || $rezerv || $active) {
                $del = 0;
            }
        }

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setPaymentType('first');
            $this->em->persist($entity);
            $this->em->flush();

            if (!$id) {
                $keys = $entity->getType() == 'keys' ? $form->get('keys')->getData() : 1;
                $this->get('promo')->addKeys($entity, $keys);
            }

            if ($id) {
                $this->get('session')->getFlashBag()->add('success', 'success_edited');
                return $this->redirect($this->generateUrl($this->routerList));
            } else {
                $this->get('session')->getFlashBag()->add('success', 'success_added');
                return $this->redirect($this->generateUrl($this->routerItemAdd));
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'   => $form->createView(),
            'entity' => $entity,
            'del'    => $del,
        ));
    }

    public function activationsAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }
        /** @var $entity \My\PromoBundle\Entity\Campaign */

        $promo_keys = array();
        $params = array();
        switch ($entity->getType()) {
            case 'keys':
                $form_factory = $this->container->get('form.factory');
                /** @var $fb \Symfony\Component\Form\FormBuilder */
                $fb = $form_factory->createNamedBuilder('promo_key', 'form', array(), array(
                    'csrf_protection'    => false,
                    'translation_domain' => $this->entityNameS,
                ));
                $fb->add('actived', 'choice', array(
                    'required'    => false,
                    'empty_value' => 'choose_option',
                    'choices'     => array(
                        'true'  => 'actived.true',
                        'false' => 'actived.false',
                    ),
                ));
                $fb->setMethod('get');
                $filter_form = $fb->getForm();
                $filter_form->handleRequest($this->getRequest());

                $qb = $this->em->getRepository('PromoBundle:Key')->createQueryBuilder('pk')
                    ->andWhere('pk.campaign = :campaign')->setParameter(':campaign', $entity)
                    ->addOrderBy('pk.activations_info', 'DESC')
                ;

                $data = null;
                if ($data = $filter_form->get('actived')->getData()) {
                    if ($data == 'true') {
                        $qb->andWhere('pk.activations_info != :array')->setParameter(':array', serialize(array()));
                    } else {
                        $qb->andWhere('pk.activations_info = :array')->setParameter(':array', serialize(array()));
                    }
                }

                $keys = $qb->getQuery()->execute();
                foreach ($keys as $key) { /** @var $key \My\PromoBundle\Entity\Key */
                    $promo_key = array(
                        'key'  => $key->getKey(),
                        'user' => null,
                    );

                    $info = $key->getActivationsInfo();
                    if (reset($info)) {
                        $keys_info = array_keys($info);
                        if ($user_id = reset($keys_info)) {
                            $user = $this->em->find('AppBundle:User', $user_id);
                            if ($user) {
                                $promo_key['user'] = array(
                                    'id'        => $user->getId(),
                                    'full_name' => $user->getFullName(),
                                );
                            }
                        }
                    }

                    $promo_keys[] = $promo_key;
                }

                $params = array(
                    'entity'      => $entity,
                    'keys'        => $promo_keys,
                    'filter_form' => $filter_form->createView(),
                );
                break;
            case 'users':
                $keys = $entity->getKeys();
                if (isset($keys[0])) {
                    $key = $keys[0];
                    $info = $key->getActivationsInfo();
                    foreach ($info as $user_id => $date) {
                        $user = $this->em->find('AppBundle:User', $user_id);
                        $promo_keys[] = array(
                            'key'  => $key->getKey(),
                            'user' => $user ? array(
                                'id'        => $user->getId(),
                                'full_name' => $user->getFullName(),
                            ) : null,
                        );
                    }

                    $more = $entity->getMaxActivations() - count($info);
                    for ($i = 0; $i < $more; $i ++) {
                        $promo_keys[] = array(
                            'key'  => $key->getKey(),
                            'user' => null,
                        );
                    }

                    $params = array(
                        'entity' => $entity,
                        'keys'   => $promo_keys,
                    );
                }
                break;
            default:
                throw $this->createNotFoundException();
        }
        return $this->render('AppBundle:Admin/Promo:activations_keys.html.twig', $params);
    }

    public function allKeysAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        /** @var $entity \My\PromoBundle\Entity\Campaign */

        $promo_keys = array();
        $params = array();
        switch ($entity->getType()) {
            case 'keys':
                $form_factory = $this->container->get('form.factory');
                /** @var $fb \Symfony\Component\Form\FormBuilder */
                $fb = $form_factory->createNamedBuilder('promo_key', 'form', array(), array(
                    'csrf_protection'    => false,
                    'translation_domain' => $this->entityNameS,
                ));
                $fb->add('actived', 'choice', array(
                    'required'    => false,
                    'empty_value' => 'choose_option',
                    'choices'     => array(
                        'true'  => 'actived.true',
                        'false' => 'actived.false',
                    ),
                ))
                    ->add('createdFrom', 'date', array('required' => false))
                    ->add('createdTo', 'date', array('required' => false))
                    ->add('activatedFrom', 'date', array('required' => false))
                    ->add('activatedTo', 'date', array('required' => false))
                ;
                $fb->setMethod('get');
                $filter_form = $fb->getForm();
                $filter_form->handleRequest($this->getRequest());

                $qb = $this->em->getRepository('PromoBundle:Key')->createQueryBuilder('pk')
                    ->andWhere('pk.campaign = :campaign')->setParameter(':campaign', $entity)
                    ->addOrderBy('pk.activations_info', 'DESC');

                $data = null;
                if ($data = $filter_form->get('actived')->getData()) {
                    if ($data == 'true') {
                        $qb->andWhere('pk.activations_info != :array')->setParameter(':array', serialize(array()));
                    } else {
                        $qb->andWhere('pk.activations_info = :array')->setParameter(':array', serialize(array()));
                    }
                }
                if ($createdFrom = $filter_form->get('createdFrom')->getData()) {
                    $qb->andWhere('pk.created >= :createdFrom')->setParameter(':createdFrom', $createdFrom);
                }
                if ($createdTo = $filter_form->get('createdTo')->getData()) {
                    $qb->andWhere('pk.created <= :createdTo')->setParameter(':createdTo', $createdTo);
                }
                if ($activatedFrom = $filter_form->get('activatedFrom')->getData()) {
                    $qb->andWhere('pk.activated >= :activatedFrom')->setParameter(':activatedFrom', $activatedFrom);
                }
                if ($activatedTo = $filter_form->get('activatedTo')->getData()) {
                    $qb->andWhere('pk.activated <= :activatedTo')->setParameter(':activatedTo', $activatedTo);
                }

                $keys = $qb->getQuery()->execute();
                foreach ($keys as $key) { /** @var $key \My\PromoBundle\Entity\Key */
                    $promo_key = array(
                        'key'  => $key->getKey(),
                        'id'    => $key->getId(),
                        'user' => null,
                        'perv' => $key->getTriedEnters(),
                        'rezerv' => $key->getRezerv(),
                        'use' => $key->getActiveUser(),
                        'info' => $key->getActivationsInfo(),
                        'dateCreate' => $key->getCreated(),
                        'dateActivate' => $key->getActivated(),

                    );
                    $promo_keys[] = $promo_key;
                }

                $params = array(
                    'entity'      => $entity,
                    'keys'        => $promo_keys,
                    'filter_form' => $filter_form->createView(),
                );
                break;
            case 'users':
                $keys = $entity->getKeys();

                if (isset($keys[0])) {
                    /** @var $key \My\PromoBundle\Entity\Key */
                    $key = $keys[0];
                    $promo_key = array(
                        'key'  => $key->getKey(),
                        'id'    => $key->getId(),
                        'user' => null,
                        'perv' => $key->getTriedEnters(),
                        'rezerv' => $key->getRezerv(),
                        'use' => $key->getActiveUser(),
                        'info' => $key->getActivationsInfo(),
                        'dateCreate' => $key->getCreated(),
                        'dateActivate' => $key->getActivated(),
                    );
                    $promo_keys[] = $promo_key;
                    $params = array(
                        'entity' => $entity,
                        'keys'   => $promo_keys,
                    );
                }
                break;
            default:
                throw $this->createNotFoundException();
        }
        return $this->render('AppBundle:Admin/Promo:show_all_keys.html.twig', $params);
    }

    public function promoKeysAction($type, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        /** @var $key \My\PromoBundle\Entity\Key */
        $key = $this->em->find('PromoBundle:Key', $id);
        if (!$key) {
            throw $this->createNotFoundException('Promo key for id "'.$id.'" not found.');
        }

        $usersId = 0;
        if ($type == 'first') {
            $usersId = $key->getTriedEnters();
        } elseif ($type == 'rezerv') {
            $usersId = $key->getRezerv();
        } elseif ($type == 'use') {
            $usersId = $key->getActiveUser();
        }

        $qb = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->andWhere('u.id IN (:id)')->setParameter('id', $usersId);

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));
        $pagerfanta->setMaxPerPage(30);

        $pagerfanta->getCurrentPageResults();

        return $this->render('AppBundle:Admin/Promo:promo_keys_show.html.twig', array(
            'pagerfanta' => $pagerfanta,
            'key' => $key,
            'type' => $type,
            'company' => $key->getCampaign(),
        ));
    }

    public function addKeysAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity || $entity->getType() != 'keys') {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }
        /** @var $entity \My\PromoBundle\Entity\Campaign */

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('promo_key', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => $this->entityNameS,
        ));
        $fb->add('count_keys', 'integer', array(
            'attr'        => array('class' => 'span1'),
            'constraints' => array(new GreaterThan(0)),
        ));
        $form = $fb->getForm();

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $this->get('promo')->addKeys($entity, $form->get('count_keys')->getData());
            return $this->redirect($this->generateUrl('admin_promo_activations', array('id' => $id)));
        }

        return $this->render('AppBundle:Admin:Promo/add_key.html.twig', array(
            'form'   => $form->createView(),
            'entity' => $entity,
        ));
    }

    public function deleteAction($id)
    {
        /** @var $entity \My\PromoBundle\Entity\Campaign */
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName . ' for id "' . $id . '" not found.');
        }

        $keys = $entity->getKeys();

        foreach ($keys as $key) { /** @var $key \My\PromoBundle\Entity\Key */
            $tried = $key->getTriedEnters();
            $rezerv = $key->getRezerv();
            $activ = $key->getActiveUser();

            if ($tried || $rezerv || $activ) {
                throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
            }
        }

        $this->em->remove($entity);
        $this->em->flush();

        $this->get('session')->getFlashBag()->add('success', 'success_deleted');

        return $this->redirect($this->generateUrl($this->routerList));
    }

    public function addActivationsAction($id)
    {
        $entity = $this->repo->find($id);
        if (!$entity || $entity->getType() != 'users') {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }
        /** @var $entity \My\PromoBundle\Entity\Campaign */

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('promo_key', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => $this->entityNameS,
        ));
        $fb->add('count_activations', 'integer', array(
            'attr'        => array('class' => 'span1'),
            'constraints' => array(new GreaterThan(0)),
        ));
        $form = $fb->getForm();

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $entity->setMaxActivations($entity->getMaxActivations() + $form->get('count_activations')->getData());
            $this->em->persist($entity);
            $this->em->flush();

            return $this->redirect($this->generateUrl('admin_promo_activations', array('id' => $id)));
        }

        return $this->render('AppBundle:Admin:Promo/add_activations.html.twig', array(
            'form'   => $form->createView(),
            'entity' => $entity,
        ));
    }

    public function searchKeyAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $fb = $this->createFormBuilder()
            ->add('key', 'text', array(
                'label' => 'Ключ',
                'constraints' => new Assert\NotBlank(),
            ))
        ;
        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $key = $this->em->getRepository('PromoBundle:Key')->findOneBy(array(
                '_key' => $form->get('key')->getData(),
            ));
            if ($key) {
                return $this->redirect($this->generateUrl('admin_promo_keys_activated', array('id' => $key->getId())));
            }
            $form->get('key')->addError(new FormError('К сожалению, такой ключ не найден.'));
        }

        return $this->render('AppBundle:Admin/Promo:promo_search_key.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function promoKeysActivatedAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $key = $this->em->getRepository('PromoBundle:Key')->find($id);
        if (!$key) {
            throw $this->createNotFoundException('Promo key for id "'.$id.'" not found.');
        }


        return $this->render('AppBundle:Admin/Promo:promo_keys_activated.html.twig', array(
            'key' => $key,
        ));
    }

    public function deleteKeyAction($id)
    {
        $key = $this->em->getRepository('PromoBundle:Key')->find($id);

        if (!$key) {
            throw $this->createNotFoundException();
        }

        /** @var $key \My\PromoBundle\Entity\Key */
        $tried = $key->getTriedEnters();
        $rezerv = $key->getRezerv();
        $activ = $key->getActiveUser();

        if ($tried || $rezerv || $activ) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }

        $this->em->remove($key);
        $this->em->flush();

        $flash = $this->get('session')->getFlashBag();
        $flash->add('success', 'success_deleted');

        return $this->redirect($this->generateUrl('admin_promo_all_keys', array('id' => $key->getCampaign()->getId())));
    }
}
