<?php

namespace My\PaymentBundle\Entity;

use My\PaymentBundle\Model\Log as LogModel;

class Log extends LogModel
{
    protected $paid = false;
    public $categories = array();
    public $services = array();
    public $moderate_name = '';
}
