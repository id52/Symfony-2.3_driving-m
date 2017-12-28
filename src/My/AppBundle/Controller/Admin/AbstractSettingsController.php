<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Repository;

abstract class AbstractSettingsController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;

    public $settings = array();

    protected $perms = array('ROLE_ADMIN');
    protected $permsSettings = array('ROLE_ADMIN');
    protected $routerSettings;
    protected $tmplSettings = '_settings.html.twig';

    public function init()
    {
        if (!$this->routerSettings) {
            throw $this->createNotFoundException('Variable $routerSettings is not defined.');
        }
        if (false === $this->get('security.context')->isGranted($this->perms)) {
            throw $this->createNotFoundException('Not permissions');
        }
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
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        return $fb;
    }

    /**
     * @param $data array
     * @param $form FormInterface
     */
    protected function checkData(&$data, FormInterface &$form)
    {
    }
}
