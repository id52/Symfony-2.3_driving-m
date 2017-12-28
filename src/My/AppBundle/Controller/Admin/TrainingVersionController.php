<?php

namespace My\AppBundle\Controller\Admin;

class TrainingVersionController extends AbstractEntityController
{
    protected $entityName = 'TrainingVersion';
    protected $listFields = array('category', 'startDate');
    protected $orderBy = array('category' => 'ASC', 'start_date' => 'ASC');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplList = 'TrainingVersion/list.html.twig';
}
