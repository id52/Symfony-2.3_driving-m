<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\SupportMessage as SupportMessageModel;

class SupportMessage extends SupportMessageModel
{
    protected $user_read = false;
}
