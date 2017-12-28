<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

class EnumPromoStatusType extends EnumType
{
    protected $name = 'enumpromostatus';
    protected $values = array('open', 'closed');
}
