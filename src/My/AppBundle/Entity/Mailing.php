<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Mailing as MailingModel;

class Mailing extends MailingModel
{
    public function getUsers()
    {
        return explode(',', $this->users);
    }

    public function setUsers($users)
    {
        $this->users = implode(',', is_array($users) ? $users : array());
        return $this;
    }
}
