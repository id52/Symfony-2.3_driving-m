<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\PassFilial as PassFilialModel;

class PassFilial extends PassFilialModel
{
    protected $active = true;
    protected $phones = array();
    protected $emails = array();
    protected $groups = array();

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

    public function getActiveGroups()
    {
        $active_groups = array();

        $groups = $this->getGroups();
        foreach ($groups as $group) {
            if ($group['active']) {
                $active_groups[] = $group;
            }
        }

        return $active_groups;
    }

    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }
}
