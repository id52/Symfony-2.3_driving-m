<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TrainingController extends AbstractSettingsController
{
    protected $routerSettings = 'admin_training_settings';
    protected $tmplSettings = 'Training/settings.html.twig';

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('ticket_test_old_style', 'checkbox', array('required' => false));

        $fb->add('final_exam_1_shuffle', 'checkbox', array('required' => false));
        $fb->add('final_exam_1_tickets', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_1_max_errors_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_1_ticket_time', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_1_shuffle_answers', 'checkbox', array('required' => false));
        $fb->add('final_exam_1_extra_time', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_1_max_errors_in_ticket', 'integer', array('attr' => array('class' => 'span1')));

        $fb->add('final_exam_2_tickets', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_2_questions_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_2_not_repeat_questions_in_tickets', 'checkbox', array('required' => false));
        $fb->add('final_exam_2_max_errors_in_ticket', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_2_ticket_time', 'integer', array('attr' => array('class' => 'span1')));
        $fb->add('final_exam_2_shuffle_answers', 'checkbox', array('required' => false));

        $fb->add('training_final_exam_description', 'textarea');
        $fb->add('training_final_exam_timeout', 'textarea');
        $fb->add('training_final_exam_complete', 'textarea');
        $fb->add('training_final_exam_long_time', 'textarea');
        $fb->add('training_final_exam_max_errors', 'textarea');
        $fb->add('training_final_exam_retake', 'textarea');
        $fb->add('training_final_exam_denied', 'textarea');
        $fb->add('training_final_exam_passed', 'textarea');

        $fb->add('after_all_exams_enabled', 'checkbox', array('required' => false));
        $fb->add('after_all_exams_title', 'text');
        $fb->add('after_all_exams_text', 'textarea');

        $fb->add('after_all_exams_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_all_exams_email_title', 'text');
        $fb->add('after_all_exams_email_text', 'textarea');

        $fb->add('after_final_exam_enabled', 'checkbox', array('required' => false));
        $fb->add('after_final_exam_title', 'text');
        $fb->add('after_final_exam_text', 'textarea');

        $fb->add('after_final_exam_email_enabled', 'checkbox', array('required' => false));
        $fb->add('after_final_exam_email_title', 'text');
        $fb->add('after_final_exam_email_text', 'textarea');

        $fb->add('max_errors_questions_text', 'textarea');
        $fb->add('max_errors_questions_block_text', 'textarea');
        $fb->add('max_errors_additional_questions_text', 'textarea');
        $fb->add('max_errors_ticket_text', 'textarea');

        return $fb;
    }
}
