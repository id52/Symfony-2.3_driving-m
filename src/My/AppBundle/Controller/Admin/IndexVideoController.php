<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class IndexVideoController extends Controller
{
    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;
    /** @var $user \My\AppBundle\Entity\User */
    public $user;
    public $settings = array();

    public function init()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_MOD_CONTENT')) {
            throw $this->createNotFoundException();
        }
    }

    public function itemAction(Request $request)
    {
        $form_factory = $this->container->get('form.factory');
        /** @var $fb \Symfony\Component\Form\FormBuilder */
        $fb = $form_factory->createNamedBuilder('index_video', 'form', null, array(
            'translation_domain' => 'index_video',
        ));
        $fb->add('image', 'file', array(
            'required'     => false,
            'constraints'  => array(new Assert\Image(array('mimeTypes' => array('image/png')))),
        ));
        $fb->add('mp4', 'file', array(
            'required'    => false,
            'constraints' => array(new Assert\File(array('mimeTypes' => 'video/mp4'))),
        ));
        $fb->add('ogv', 'file', array(
            'required'    => false,
            'constraints' => array(new Assert\File(array('mimeTypes' => array('video/ogg', 'application/ogg')))),
        ));
        $fb->add('webm', 'file', array(
            'required'    => false,
            'constraints' => array(new Assert\File(array('mimeTypes' => 'video/webm'))),
        ));
        $fb->add('submit', 'submit', array('label' => 'admin.buttons.save'));
        $form = $fb->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $web_dir = $this->get('kernel')->getRootDir().'/../web/uploads/wysiwyg/';

            $image = $form->get('image')->getData();
            if ($image) {
                $file = $web_dir.'index.png';
                if (file_exists($file)) {
                    unlink($file);
                }
                $image->move($web_dir, 'index.png');
            }

            $mp4 = $form->get('mp4')->getData();
            if ($mp4) {
                $file = $web_dir.'index.mp4';
                if (file_exists($file)) {
                    unlink($file);
                }
                $mp4->move($web_dir, 'index.mp4');
            }

            $ogv = $form->get('ogv')->getData();
            if ($ogv) {
                $file = $web_dir.'index.ogv';
                if (file_exists($file)) {
                    unlink($file);
                }
                $ogv->move($web_dir, 'index.ogv');
            }

            $webm = $form->get('webm')->getData();
            if ($webm) {
                $file = $web_dir.'index.webm';
                if (file_exists($file)) {
                    unlink($file);
                }
                $webm->move($web_dir, 'index.webm');
            }

            $this->get('session')->getFlashBag()->add('info', 'Обновлено!');

            $this->redirect($this->generateUrl('admin_index_video'));
        }

        return $this->render('AppBundle:Admin:IndexVideo/item.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
