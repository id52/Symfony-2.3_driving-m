<?php

namespace My\AppBundle\Controller\Admin;

class NewsController extends AbstractEntityController
{
    protected $orderBy = array('created_at' => 'DESC');
    protected $perms = array('ROLE_MOD_CONTENT');
    protected $routerList = 'admin_news';
    protected $tmplList = 'News/list.html.twig';
}
