<?php

namespace My\AppBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Image;
use My\AppBundle\Form\Type\ImageFormType;
use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends AbstractEntityController
{
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplItem = 'Question/item.html.twig';
    protected $tmplList = 'Question/list.html.twig';

    public function listAction()
    {
        $qb = $this->repo->createQueryBuilder('e')
            ->leftJoin('e.theme', 't')
        ;

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('question', 'form', array(), array(
            'csrf_protection'    => false,
            'translation_domain' => $this->entityNameS,
        ))
            ->add('is_pdd', 'checkbox', array('required' => false))
            ->add('subject', 'entity', array(
                'class'       => 'AppBundle:Subject',
                'required'    => false,
                'empty_value' => 'choose_option',
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
            ->add('theme', 'entity', array(
                'class'       => 'AppBundle:Theme',
                'required'    => false,
                'empty_value' => 'choose_option',
                'property'    => 'title',
            ))
        ;

        $fb->setMethod('get');
        $filter_form = $fb->getForm();
        $filter_form->handleRequest($this->getRequest());

        $data = null;
        if (($data = $filter_form->get('is_pdd')->getData()) && $data) {
            $qb
                ->andWhere('e.is_pdd = :is_pdd')->setParameter(':is_pdd', true)
                ->addOrderBy('e.num')
                ->addOrderBy('t.subject')
                ->addOrderBy('t.position')
            ;
        } else {
            $qb
                ->addOrderBy('t.subject')
                ->addOrderBy('t.position')
                ->addOrderBy('e.num')
            ;
        }
        if ($data = $filter_form->get('subject')->getData()) {
            $qb->andWhere('t.subject = :subject')->setParameter(':subject', $data);

        }
        if ($data = $filter_form->get('version')->getData()) {
            $qb
                ->leftJoin('e.versions', 'v')
                ->andWhere('v.id = :version')->setParameter(':version', $data)
            ;
        }
        if ($data = $filter_form->get('theme')->getData()) {
            $qb->andWhere('e.theme = :theme')->setParameter(':theme', $data)
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
        /** @var $entity \My\AppBundle\Entity\Question */

        $answers = $entity->getAnswers();

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        if ($request->isMethod('post')) {
            $answers = array();
            if ($answers_req = $request->get('answers')) {
                $correct_answer = intval($request->get('correct_answer'));
                foreach ($answers_req as $key => $answer) {
                    $answers[$key] = array(
                        'title'   => $answer,
                        'correct' => ($key == $correct_answer),
                    );
                }
            }

            $form->handleRequest($request);

            if ($form->get('is_pdd')->getData()) {
                $num = trim($form->get('num')->getData());
                if (!$num) {
                    $form->get('num')->addError(new FormError('Номер не может быть пустым!'));
                } else {
                    $qb = $this->repo->createQueryBuilder('e');
                    $qb->andWhere('e.num = :num')->setParameter(':num', $entity->getNum());
                    if ($entity->getId()) {
                        $qb->andWhere('e.id != :id')->setParameter(':id', $entity->getId());
                    }
                    $qb->leftJoin('e.versions', 'v');
                    $qb->andWhere('v.id IN (:versions)')->setParameter(':versions', $entity->getVersionsIds());
                    $nums = $qb->getQuery()->getArrayResult();
                    if ($nums) {
                        $form->get('num')->addError(new FormError('Такой номер уже используется в такой же версии!'));
                    }
                }
            } else {
                $entity->setNum(null);
            }


            if ($form->isValid()) {
                $entity->setAnswers($answers);

                $allow_versions = array();
                $versions = $this->em->getRepository('AppBundle:TrainingVersion')->createQueryBuilder('v')
                    ->leftJoin('v.themes', 't')
                    ->andWhere('t.id = :theme')->setParameter(':theme', $entity->getTheme())
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

                if ($entity->getImage()) {
                    $entity->getImage()->setQuestion(null);
                }
                $image_id = intval($form->get('image_id')->getData());
                $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
                if ($image) {
                    $image->setQuestion($entity);
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

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'      => $form->createView(),
            'entity'    => $entity,
            'imageForm' => $this->createForm(new ImageFormType(), new Image())->createView(),
            'answers'   => $answers,
        ));
    }
}
