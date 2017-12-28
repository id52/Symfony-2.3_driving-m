<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\TestLog as TestLogModel;

class TestLog extends TestLogModel
{
    protected $passed = false;
}
