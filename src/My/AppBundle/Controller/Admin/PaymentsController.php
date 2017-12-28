<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class PaymentsController extends AbstractSettingsController
{
    protected $routerSettings = 'admin_settings_payments';
    protected $tmplSettings = 'Payments/settings.html.twig';

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('payment_success', 'textarea');
        $fb->add('payment_fail', 'textarea');

//        $fb->add('access_time_after_1_payment', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('access_time_after_2_payment', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('access_time_after_3_payment', 'integer', array('attr' => array('class' => 'span1')));

        for ($i = 1; $i <= 10; $i ++) {
            $fb->add('access_time_end_popup_after_2_payment_'.$i, 'integer', array(
                'attr' => array('class' => 'span1'),
            ));
        }

        for ($i = 1; $i <= 16; $i ++) {
/*
            $fb->add('access_time_end_notify_after_1_payment_'.$i, 'integer', array(
                'attr' => array('class' => 'span1'),
            ));
*/
            $fb->add('access_time_end_notify_after_2_payment_'.$i, 'integer', array(
                'attr' => array('class' => 'span1'),
            ));
            $fb->add('access_time_end_notify_after_3_payment_'.$i, 'integer', array(
                'attr' => array('class' => 'span1'),
            ));
        }

        $fb->add('no_payments_enabled', 'checkbox', array('required' => false));
        $fb->add('no_payments_title', 'text');
        $fb->add('no_payments_text', 'textarea');

        $fb->add('training_without_3_payment_title', 'text');
        $fb->add('training_without_3_payment_text', 'textarea');

        $fb->add('first_payment_text', 'textarea');
        $fb->add('first_payment_2_text', 'textarea');

        $fb->add('after_1_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_1_payment_title', 'text');
        $fb->add('after_1_payment_text', 'textarea');

        $fb->add('after_1_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_1_payment_email_title', 'text');
        $fb->add('after_1_payment_email_text', 'textarea');

        $fb->add('after_1_payment_2_enabled', 'checkbox', array('required' => false));
        $fb->add('after_1_payment_2_title', 'text');
        $fb->add('after_1_payment_2_text', 'textarea');

        $fb->add('after_1_payment_2_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_1_payment_2_email_title', 'text');
        $fb->add('after_1_payment_2_email_text', 'textarea');

/*
        $fb->add('after_2_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_2_payment_title', 'text');
        $fb->add('after_2_payment_text', 'textarea');

        $fb->add('after_2_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_2_payment_email_title', 'text');
        $fb->add('after_2_payment_email_text', 'textarea');
*/

        $fb->add('after_3_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_3_payment_title', 'text');
        $fb->add('after_3_payment_text', 'textarea');

        $fb->add('after_3_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_3_payment_email_title', 'text');
        $fb->add('after_3_payment_email_text', 'textarea');

/*
        $fb->add('after_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_payment_title', 'text');
        $fb->add('after_payment_text', 'textarea');

        $fb->add('after_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_payment_email_title', 'text');
        $fb->add('after_payment_email_text', 'textarea');
*/

/*
        $fb->add('before_access_time_end_after_1_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('before_access_time_end_after_1_payment_title', 'text');
        $fb->add('before_access_time_end_after_1_payment_text', 'textarea');

        $fb->add('before_access_time_end_after_1_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('before_access_time_end_after_1_payment_email_title', 'text');
        $fb->add('before_access_time_end_after_1_payment_email_text', 'textarea');

        $fb->add('after_access_time_end_after_1_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_access_time_end_after_1_payment_title', 'text');
        $fb->add('after_access_time_end_after_1_payment_text', 'textarea');
*/

        $fb->add('before_access_time_end_after_2_payment_popup_title', 'text');
        $fb->add('before_access_time_end_after_2_payment_popup_text', 'textarea');

        $fb->add('before_access_time_end_after_2_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('before_access_time_end_after_2_payment_title', 'text');
        $fb->add('before_access_time_end_after_2_payment_text', 'textarea');

        $fb->add('before_access_time_end_after_2_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('before_access_time_end_after_2_payment_email_title', 'text');
        $fb->add('before_access_time_end_after_2_payment_email_text', 'textarea');

        $fb->add('after_access_time_end_after_2_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_access_time_end_after_2_payment_title', 'text');
        $fb->add('after_access_time_end_after_2_payment_text', 'textarea');

        $fb->add('before_access_time_end_after_3_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('before_access_time_end_after_3_payment_title', 'text');
        $fb->add('before_access_time_end_after_3_payment_text', 'textarea');

        $fb->add('before_access_time_end_after_3_payment_email_enabled', 'checkbox', array('required' => false));
        $fb->add('before_access_time_end_after_3_payment_email_title', 'text');
        $fb->add('before_access_time_end_after_3_payment_email_text', 'textarea');

        $fb->add('after_access_time_end_after_3_payment_enabled', 'checkbox', array('required' => false));
        $fb->add('after_access_time_end_after_3_payment_title', 'text');
        $fb->add('after_access_time_end_after_3_payment_text', 'textarea');

        return $fb;
    }

    /**
     * @param $data array
     * @param $form FormInterface
     */
    protected function checkData(&$data, FormInterface &$form)
    {
        $trans = $this->get('translator');
/*
        if ($data['access_time_after_1_payment'] > 0
            && $data['access_time_after_2_payment'] > 0
            && $data['access_time_after_1_payment'] >= $data['access_time_after_2_payment']
        ) {
            $error = new FormError($trans->trans('settings_access_time_after_2_payment_less', array(), 'settings'));
            $form->get('access_time_after_2_payment')->addError($error);
        }
*/
        if ($data['access_time_after_2_payment'] > 0
            && $data['access_time_after_3_payment'] > 0
            && $data['access_time_after_2_payment'] >= $data['access_time_after_3_payment']
        ) {
            $error = new FormError($trans->trans('settings_access_time_after_3_payment_less', array(), 'settings'));
            $form->get('access_time_after_3_payment')->addError($error);
        }
    }
}
