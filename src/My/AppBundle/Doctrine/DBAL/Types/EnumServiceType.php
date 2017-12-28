<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

class EnumServiceType extends EnumType
{
    protected $name = 'enumservice';
    protected $values = array();
}
