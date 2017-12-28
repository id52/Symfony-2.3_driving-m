<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\SupportDialog as SupportDialogModel;

class SupportDialog extends SupportDialogModel
{
    protected $answered = false;
    protected $userRead = false;
}
