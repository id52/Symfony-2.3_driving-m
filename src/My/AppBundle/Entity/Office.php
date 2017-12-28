<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Office as OfficeModel;

class Office extends OfficeModel
{
    protected $active = true;

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
}
