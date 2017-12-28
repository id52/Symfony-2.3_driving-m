<?php

namespace My\PaymentBundle\Entity;

use My\PaymentBundle\Model\RevertLog as RevertLogModel;

class RevertLog extends RevertLogModel
{
    protected $paid = false;
    protected $info = array();
}
