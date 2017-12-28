<?php

namespace My\AppBundle\Controller\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class FlashBlockController extends AbstractEntityController
{
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $tmplList = 'FlashBlock/list.html.twig';
}
