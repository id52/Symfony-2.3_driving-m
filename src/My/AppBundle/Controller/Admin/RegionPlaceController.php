<?php

namespace My\AppBundle\Controller\Admin;

class RegionPlaceController extends AbstractEntityController
{
    protected $listFields = array('name', 'region');
    protected $orderBy = array('name' => 'ASC');
}
