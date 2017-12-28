<?php

namespace My\AppBundle\Controller\Admin;

use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;

class ReviewController extends AbstractEntityController
{
    protected $orderBy = array('created_at' => 'DESC');
    protected $tmplList = 'Review/list.html.twig';

    public function listAction()
    {
        $qb = $this->repo->createQueryBuilder('e');
        foreach ($this->orderBy as $field => $order) {
            $qb->addOrderBy('e.'.$field, $order);
        }

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('subject', 'form', array(), array('csrf_protection' => false))
            ->add('status', 'choice', array(
                'label'   => 'Статус',
                'choices' => array(
                    'not_checked' => 'Не проверенные',
                    'moderated'   => 'Утверждённые',
                    'rejected'    => 'Отклонённые',
                    'all'         => 'Все',
                ),
                'data' => 'not_checked',
            ))
        ;

        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($this->getRequest());

        $data = null;
        if ($data = $filter_form->get('status')->getData()) {
            if ($data != 'all') {
                switch ($data) {
                    case 'moderated':
                        $qb->andWhere('e.moderated = 1');
                        break;
                    case 'rejected':
                        $qb->andWhere('e.moderated = 0');
                        break;
                    default:
                        $qb->andWhere('e.moderated IS NULL');
                }
            }
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));

        return $this->render('AppBundle:Admin:'.$this->tmplList, array(
            'pagerfanta'  => $pagerfanta,
            'list_fields' => $this->listFields,
            'filter_form' => $filter_form->createView(),
        ));
    }

    public function checkAction(Request $request, $id, $status)
    {
        $entity = $this->repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException($this->entityName.' for id "'.$id.'" not found.');
        }
        /** @var $entity \My\AppBundle\Entity\Review */

        $entity->setModerated(intval($status));
        $this->em->persist($entity);
        $this->em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
