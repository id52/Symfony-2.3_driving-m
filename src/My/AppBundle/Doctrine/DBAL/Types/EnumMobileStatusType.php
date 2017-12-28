<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

class EnumMobileStatusType extends EnumType
{
    protected $name = 'enummobilestatus';
    protected $values = array('sended', 'confirmed');
}
