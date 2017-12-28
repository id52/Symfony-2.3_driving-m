<?php

namespace My\SmsUslugiRuBundle\Service;

use Doctrine\ORM\EntityManager;
use My\SmsUslugiRuBundle\Entity\Log;

class SmsUslugiRu
{
    /** @var EntityManager */
    protected $em;
    /** @var array */
    protected $params;

    public function __construct(EntityManager $em, $sms_uslugi_login, $sms_uslugi_pass, $sms_uslugi_url)
    {
        $this->em = $em;
        $this->params = array(
            'login' => $sms_uslugi_login,
            'pass'  => $sms_uslugi_pass,
            'url'   => $sms_uslugi_url,
        );
    }

    /**
     * Query to SmsUslugiRu
     *
     * @param $number string
     * @param $text string
     *
     * @return boolean
     */
    public function query($number, $text)
    {
        $login = $this->params['login'];
        $pass = $this->params['pass'];

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<data>
    <login>{$login}</login>
    <password>{$pass}</password>
    <text>{$text}</text>
    <to number="{$number}"></to>
    <source>MGTU</source>
</data>
XML;

        $address = $this->params['url'];
        $ch = curl_init($address);
        curl_setopt($ch, CURLOPT_URL, $address);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $s_answer = (array)simplexml_load_string($result);

        $log = new Log();
        $log->setText($text);
        $log->setNumber($number);
        if (isset($s_answer['smsid'])) {
            $log->setSId($s_answer['smsid']);
        }
        $log->setSAnswer($s_answer);
        $this->em->persist($log);
        $this->em->flush();

        return ($s_answer['code'] == 1);
    }
}
