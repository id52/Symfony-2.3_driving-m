<?php

namespace My\AppBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class SliceController extends AbstractEntityController
{
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplList = 'Slice/list.html.twig';
    protected $tmplSettings = 'Slice/settings.html.twig';

    public function listAction()
    {
        $qb = $this->repo->createQueryBuilder('e');
        $qb->leftJoin('e.after_theme', 't');
        $qb->addOrderBy('t.subject');
        $qb->addOrderBy('t.position');

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('slice', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => $this->entityNameS,
        ))
            ->add('version', 'entity', array(
                'class'         => 'AppBundle:TrainingVersion',
                'required'      => false,
                'empty_value'   => 'choose_option',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->addOrderBy('v.category')
                        ->addOrderBy('v.start_date')
                    ;
                },
            ))
        ;

        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($this->getRequest());

        $data = null;
        if ($data = $filter_form->get('version')->getData()) {
            $qb
                ->leftJoin('e.versions', 'v')
                ->andWhere('v.id = :version')->setParameter(':version', $data)
            ;
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pagerfanta->setCurrentPage($this->getRequest()->get('page', 1));

        return $this->render('AppBundle:Admin:'.$this->tmplList, array(
            'pagerfanta'  => $pagerfanta,
            'list_fields' => $this->listFields,
            'filter_form' => $filter_form->createView(),
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
        /** @var $entity \My\AppBundle\Entity\Slice */

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $allow_versions = array();
            $versions = $this->em->getRepository('AppBundle:TrainingVersion')->createQueryBuilder('v')
                ->leftJoin('v.themes', 't')
                ->andWhere('t.id = :theme')->setParameter(':theme', $entity->getAfterTheme())
                ->getQuery()->getArrayResult();
            foreach ($versions as $version) {
                $allow_versions[] = $version['id'];
            }

            $versions = $entity->getVersions();
            foreach ($versions as $version) {
                if (!in_array($version->getId(), $allow_versions)) {
                    $entity->removeVersion($version);
                }
            }

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

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'   => $form->createView(),
            'entity' => $entity,
        ));
    }

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('slice_tickets', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('slice_questions_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('slice_not_repeat_questions_in_tickets', 'checkbox', array('required' => false));
        $fb->add('slice_max_errors_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('slice_ticket_time', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('slice_shuffle_answers', 'checkbox', array('required' => false));

        $fb->add('training_slice_timeout', 'textarea');
        $fb->add('training_slice_complete', 'textarea');
        $fb->add('training_slice_long_time', 'textarea');
        $fb->add('training_slice_max_errors', 'textarea');

        return $fb;
    }
}
