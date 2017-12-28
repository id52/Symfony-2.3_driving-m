<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\TestKnowledgeLog as TestKnowledgeLogModel;

class TestKnowledgeLog extends TestKnowledgeLogModel
{
    protected $passed = false;
}
