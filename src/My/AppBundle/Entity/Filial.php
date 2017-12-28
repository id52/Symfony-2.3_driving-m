<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Filial as FilialModel;

class Filial extends FilialModel
{
    protected $active = true;
    protected $active_auth = true;
    protected $_show = true;
    protected $show_auth = true;

    public function getActivePhones()
    {
        $active_phones = array();

        $phones = $this->getPhones();
        foreach ($phones as $phone => $active) {
            if ($active) {
                $active_phones[] = $phone;
            }
        }

        return $active_phones;
    }

    public function getActiveEmails()
    {
        $active_emails = array();

        $emails = $this->getEmails();
        foreach ($emails as $email => $active) {
            if ($active) {
                $active_emails[] = $email;
            }
        }

        return $active_emails;
    }

    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }
}
