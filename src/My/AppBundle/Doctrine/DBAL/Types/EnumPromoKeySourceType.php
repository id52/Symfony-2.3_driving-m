<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

class EnumPromoKeySourceType extends EnumType
{
    protected $name = 'enumpromokeysource';
    protected $values = array('campaign', 'auto_overdue');
}
