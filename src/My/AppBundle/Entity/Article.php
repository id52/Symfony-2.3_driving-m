<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Article as ArticleModel;

class Article extends ArticleModel
{
    protected $top_menu = false;
    protected $bottom_menu = false;
    protected $private = false;
}
