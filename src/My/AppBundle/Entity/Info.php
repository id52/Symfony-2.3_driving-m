<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Info as InfoModel;

class Info extends InfoModel
{
    public function isReaded(User $user)
    {
        $readers = $this->getReaders();
        return in_array($user, $readers->toArray());
    }
}
