<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

class EnumSexType extends EnumType
{
    protected $name = 'enumsex';
    protected $values = array('male', 'female');
}
