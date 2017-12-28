<?php

namespace My\PaymentBundle\Doctrine\DBAL\Types;

class EnumPaymentType extends EnumType
{
    protected $name = 'enumpayment';
    protected $values = array('robokassa', 'psb');
}
