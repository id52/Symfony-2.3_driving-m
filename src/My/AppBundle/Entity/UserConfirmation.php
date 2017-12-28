<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\UserConfirmation as UserConfirmationModel;

class UserConfirmation extends UserConfirmationModel
{
    protected $activated = false;

    public function __construct()
    {
        $this->setHash(sha1(uniqid(mt_rand(), true)));
        $this->setLastSent(new \DateTime());
    }
}
