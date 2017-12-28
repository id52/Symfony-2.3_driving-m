<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class IndexController extends AbstractSettingsController
{
    protected $routerSettings = 'admin_index_settings';

    /**
     * @param $fb FormBuilderInterface
     * @return FormBuilderInterface
     */
    protected function addSettingsFb(FormBuilderInterface $fb)
    {
        $fb->add('landing_title', 'text');
        $fb->add('landing_keywords', 'text');
        $fb->add('landing_description', 'text');

        return $fb;
    }
}
