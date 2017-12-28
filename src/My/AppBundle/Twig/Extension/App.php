<?php

namespace My\AppBundle\Twig\Extension;

use My\AppBundle\Entity\CategoryPrice;

class App extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sum_format', array($this, 'sumFormatFilter')),
        );
    }

    public static function sumFormatFilter($sum)
    {
        return CategoryPrice::getSumView($sum);
    }

    public function getName()
    {
        return 'app_extension';
    }
}
