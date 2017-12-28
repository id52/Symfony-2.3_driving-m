<?php

namespace My\AppBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends AbstractEntityController
{
    protected $orderBy = array('subject' => 'ASC', 'position' => 'ASC');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplList = 'Theme/list.html.twig';
    protected $tmplSettings = 'Theme/settings.html.twig';

    public function listAction()
    {
        $qb = $this->repo->createQueryBuilder('e');
        foreach ($this->orderBy as $field => $order) {
            $qb->addOrderBy('e.'.$field, $order);
        }

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('theme', 'form', array(), array(
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
        /** @var $entity \My\AppBundle\Entity\Theme */

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        if ($request->isMethod('post')) {
            $orig_versions = $entity->getVersionsIds();

            $form->handleRequest($request);
            if ($form->isValid()) {
                $allow_versions = array();
                $versions = $this->em->getRepository('AppBundle:TrainingVersion')->createQueryBuilder('v')
                    ->leftJoin('v.subjects', 's')->andWhere('s.id = :subject')
                    ->setParameter(':subject', $entity->getSubject())->getQuery()
                    ->getArrayResult();
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

                $cur_versions = $entity->getVersionsIds();

                foreach (array_diff($orig_versions, $cur_versions) as $id) {
                    $version = $this->em->find('AppBundle:TrainingVersion', $id);

                    $slice = $entity->getSlice();
                    if ($slice) {
                        $slice->removeVersion($version);
                        $this->em->persist($slice);
                    }

                    $questions = $entity->getQuestions();
                    foreach ($questions as $question) {
                        $question->removeVersion($version);
                        $this->em->persist($question);
                    }
                }

                foreach (array_diff($cur_versions, $orig_versions) as $id) {
                    $version = $this->em->find('AppBundle:TrainingVersion', $id);

                    $slice = $entity->getSlice();
                    if ($slice) {
                        $slice->addVersion($version);
                        $this->em->persist($slice);
                    }

                    $questions = $entity->getQuestions();
                    foreach ($questions as $question) {
                        $question->addVersion($version);
                        $this->em->persist($question);
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

        return $this->render('AppBundle:Admin:'.$this->tmplItem, array(
            'form'   => $form->createView(),
            'entity' => $entity,
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

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('theme_test_correct_answers', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('theme_test_correct_answers_in_row', 'checkbox', array('required' => false));
        $fb->add('theme_test_questions_method', 'choice', array(
            'expanded' => true,
            'choices'  => array(
                'same_order' => 'settings_theme_test_questions_method_same_order',
                'shuffle'    => 'settings_theme_test_questions_method_shuffle',
            ),
            'attr'     => array('class' => 'inline'),
        ));
        $fb->add('theme_test_time', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('theme_test_shuffle_answers', 'checkbox', array('required' => false));

        $fb->add('training_theme_test_timeout', 'textarea');
        $fb->add('training_theme_test_complete_next', 'textarea');
        $fb->add('training_theme_test_complete_list', 'textarea');
        $fb->add('training_theme_test_long_time', 'textarea');

        return $fb;
    }
}
