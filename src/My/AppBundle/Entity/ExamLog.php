<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\ExamLog as ExamLogModel;

class ExamLog extends ExamLogModel
{
    protected $passed = false;
}
