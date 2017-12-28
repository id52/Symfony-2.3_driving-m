<?php

namespace My\AppBundle\Controller\Admin;

class TestEmailController extends AbstractEntityController
{
    protected $perms = array('ROLE_MOD_MAILING');
    protected $listFields = array('email');
}
