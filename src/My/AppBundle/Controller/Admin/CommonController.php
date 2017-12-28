<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class CommonController extends AbstractSettingsController
{
    protected $routerSettings = 'admin_settings_common';
    protected $tmplSettings = 'Common/settings.html.twig';

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
        $fb->add('counters_yandex', 'textarea', array('required' => false));

        $fb->add('contacts_email', 'text');
        $fb->add('contacts_phone1_prefix', 'text');
        $fb->add('contacts_phone1', 'text');
        $fb->add('contacts_phone2_prefix', 'text');
        $fb->add('contacts_phone2', 'text');
        $fb->add('coupons_phone1_prefix', 'text');
        $fb->add('coupons_phone1', 'text');
        $fb->add('coupons_phone2_prefix', 'text');
        $fb->add('coupons_phone2', 'text');

        $fb->add('socials_vk', 'text');
        $fb->add('socials_facebook', 'text');
        $fb->add('socials_twitter', 'text');

        $fb->add('profile_final_exam', 'textarea');

        $fb->add('training_test_timeout', 'textarea');
        $fb->add('training_test_complete', 'textarea');
        $fb->add('training_test_long_time', 'textarea');
        $fb->add('training_test_max_errors', 'textarea');

        $fb->add('training_test_knowledge_timeout', 'textarea');
        $fb->add('training_test_knowledge_complete', 'textarea');
        $fb->add('training_test_knowledge_long_time', 'textarea');
        $fb->add('training_test_knowledge_max_errors', 'textarea');

        $fb->add('training_test_statistics_empty', 'textarea');

        $fb->add('trainings_help_btn', 'textarea');
        $subjects = $this->em->getRepository('AppBundle:Subject')->findAll();
        foreach ($subjects as $subject) { /** @var $subject \My\AppBundle\Entity\Subject */
            $fb->add('training_'.$subject->getId().'_help_btn', 'textarea');
        }
        $fb->add('theme_help_btn', 'textarea');
        $fb->add('question_help_btn', 'textarea');

        $fb->add('birthday_greeting_title', 'text');
        $fb->add('birthday_greeting_text', 'textarea');

        $fb->add('lock_user_enabled', 'checkbox', array('required' => false));
        $fb->add('lock_user_title', 'text');
        $fb->add('lock_user_text', 'textarea');

        $fb->add('unlock_user_enabled', 'checkbox', array('required' => false));
        $fb->add('unlock_user_title', 'text');
        $fb->add('unlock_user_text', 'textarea');
                
        $fb->add('error_account_locked', 'text');

        $fb->add('close_final_exam_title', 'text');
        $fb->add('close_final_exam_text', 'textarea');

        $fb->add('closed_subject_title', 'text');
        $fb->add('closed_subject_text', 'textarea');

        for ($i = 1; $i <= 5; $i ++) {
            $fb->add('sign_'.$i, 'text');
        }

        return $fb;
    }
}
