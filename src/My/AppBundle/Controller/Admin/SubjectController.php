<?php

namespace My\AppBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Image;
use My\AppBundle\Form\Type\ImageFormType;
use My\AppBundle\Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class SubjectController extends AbstractEntityController
{
    protected $listFields = array('title', 'briefDescription');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplItem = 'Subject/item.html.twig';
    protected $tmplList = 'Subject/list.html.twig';
    protected $tmplSettings = 'Subject/settings.html.twig';

    public function listAction()
    {
        $qb = $this->repo->createQueryBuilder('e');
        foreach ($this->orderBy as $field => $order) {
            $qb->addOrderBy('e.'.$field, $order);
        }

        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('subject', 'form', array(), array(
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

        $form = $this->createForm(new $this->formClassName(), $entity, array(
            'translation_domain' => $this->entityNameS,
        ));
        if ($request->isMethod('post')) {
            $orig_versions = $entity->getVersionsIds();

            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->em->persist($entity);

                if ($entity->getImage()) {
                    $entity->getImage()->setSubject(null);
                }
                $image_id = intval($form->get('image_id')->getData());
                $image = $this->em->getRepository('AppBundle:Image')->find($image_id);
                if ($image) {
                    $image->setSubject($entity);
                }

                $cur_versions = $entity->getVersionsIds();

                foreach (array_diff($orig_versions, $cur_versions) as $id) {
                    $version = $this->em->find('AppBundle:TrainingVersion', $id);

                    $themes = $entity->getThemes();
                    foreach ($themes as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
                        $theme->removeVersion($version);
                        $this->em->persist($theme);

                        $slice = $theme->getSlice();
                        if ($slice) {
                            $slice->removeVersion($version);
                            $this->em->persist($slice);
                        }

                        $questions = $theme->getQuestions();
                        foreach ($questions as $question) {
                            $question->removeVersion($version);
                            $this->em->persist($question);
                        }
                    }
                }

                foreach (array_diff($cur_versions, $orig_versions) as $id) {
                    $version = $this->em->find('AppBundle:TrainingVersion', $id);

                    $themes = $entity->getThemes();
                    foreach ($themes as $theme) { /** @var $theme \My\AppBundle\Entity\Theme */
                        $theme->addVersion($version);
                        $this->em->persist($theme);

                        $slice = $theme->getSlice();
                        if ($slice) {
                            $slice->addVersion($version);
                            $this->em->persist($slice);
                        }

                        $questions = $theme->getQuestions();
                        foreach ($questions as $question) {
                            $question->addVersion($version);
                            $this->em->persist($question);
                        }
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
            'form'      => $form->createView(),
            'entity'    => $entity,
            'imageForm' => $this->createForm(new ImageFormType(), new Image())->createView(),
        ));
    }

    public function settingsAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted($this->permsSettings)) {
            throw $this->createNotFoundException('Not permissions for settings');
        }

        $settings_repository = $this->em->getRepository('AppBundle:Setting');
        $settings = $settings_repository->getAllData();

        $form_factory = $this->get('form.factory');
        $fb = $form_factory->createNamedBuilder('settings', 'form', null, array(
            'translation_domain' => 'settings',
        ));
        $fb = $this->addSettingsFb($fb);
        $fb->setData(array_intersect_key($settings, $fb->all()));

        $form = $fb->getForm();
        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            $data = $form->getData();
            foreach ($data as $key => $value) {
                if (is_null($data[$key]) && $form->has($key)) {
                    $type = $form->get($key)->getConfig()->getType()->getName();
                    switch ($type) {
                        case 'text':
                        case 'textarea':
                            $data[$key] = '';
                            break;
                        case 'integer':
                            $data[$key] = 0;
                            break;
                    }
                }
            }
            $this->checkData($data, $form);
            if ($form->isValid()) {
                $settings_repository->setData($data);
                $this->get('session')->getFlashBag()->add('success', 'success_updated');
                return $this->redirect($this->generateUrl($this->routerSettings));
            }
        }

        return $this->render('AppBundle:Admin:'.$this->tmplSettings, array(
            'form'     => $form->createView(),
            'subjects' => $this->em->getRepository('AppBundle:Subject')->findAll(),
        ));
    }

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('exam_shuffle', 'checkbox', array('required' => false));
        $fb->add('exam_tickets', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('exam_questions_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('exam_not_repeat_questions_in_tickets', 'checkbox', array('required' => false));
        $fb->add('exam_max_errors_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('exam_ticket_time', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('exam_shuffle_answers', 'checkbox', array('required' => false));
        $fb->add('exam_retake_time', 'integer', array('attr' => array('class' => 'span1')));

        $fb->add('training_exam_timeout', 'textarea');
        $fb->add('training_exam_complete', 'textarea');
        $fb->add('training_exam_long_time', 'textarea');
        $fb->add('training_exam_max_errors', 'textarea');
        $fb->add('training_exam_retake', 'textarea');

        $subjects = $this->em->getRepository('AppBundle:Subject')->findAll();
        foreach ($subjects as $subject) { /** @var $subject \My\AppBundle\Entity\Subject */
            $fb->add('after_all_slices_'.$subject->getId().'_enabled', 'checkbox', array('required' => false));
            $fb->add('after_all_slices_'.$subject->getId().'_title', 'text');
            $fb->add('after_all_slices_'.$subject->getId().'_text', 'textarea');

            $fb->add('after_all_slices_'.$subject->getId().'_email_enabled', 'checkbox', array('required' => false));
            $fb->add('after_all_slices_'.$subject->getId().'_email_title', 'text');
            $fb->add('after_all_slices_'.$subject->getId().'_email_text', 'textarea');

            $fb->add('after_fail_exam_'.$subject->getId().'_enabled', 'checkbox', array('required' => false));
            $fb->add('after_fail_exam_'.$subject->getId().'_title', 'text');
            $fb->add('after_fail_exam_'.$subject->getId().'_text', 'textarea');

            $fb->add('after_fail_exam_'.$subject->getId().'_email_enabled', 'checkbox', array('required' => false));
            $fb->add('after_fail_exam_'.$subject->getId().'_email_title', 'text');
            $fb->add('after_fail_exam_'.$subject->getId().'_email_text', 'textarea');

            $fb->add('after_exam_'.$subject->getId().'_enabled', 'checkbox', array('required' => false));
            $fb->add('after_exam_'.$subject->getId().'_title', 'text');
            $fb->add('after_exam_'.$subject->getId().'_text', 'textarea');

            $fb->add('after_exam_'.$subject->getId().'_email_enabled', 'checkbox', array('required' => false));
            $fb->add('after_exam_'.$subject->getId().'_email_title', 'text');
            $fb->add('after_exam_'.$subject->getId().'_email_text', 'textarea');
        }

        return $fb;
    }
}
