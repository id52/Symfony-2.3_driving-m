<?php

namespace My\AppBundle\Doctrine\DBAL\Types;

//Poromo can have 1 key with restriction by users that registered with it and have a lot of key which are one-off
class EnumPromoRestrictedType extends EnumType
{
    protected $name = 'enumpromorestricted';
    protected $values = array('users', 'keys');
}
