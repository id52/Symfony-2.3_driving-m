<?php

/**
 * Added in crontab
 * 0 20 * * *
 */

namespace My\AppBundle\Command;

use My\AppBundle\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendWarningDiscountCommand extends ContainerAwareCommand
{
    /** @var $em \Doctrine\ORM\EntityManager */
    protected $em;
    /** @var  $smsUslugi \My\SmsUslugiRuBundle\Service\SmsUslugiRu */
    protected $smsUslugi;
    /** @var  $notify \My\AppBundle\Service\Notify */
    protected $notify;
    /** @var $settings \My\AppBundle\Repository\SettingRepository */
    protected $settings;
    /** @var $trans \Symfony\Bundle\FrameworkBundle\Translation\Translator */
    protected $trans;

    protected $cnt = 0;

    protected function configure()
    {
        $this
            ->setName('app:send-warning-discount')
            ->addOption('cron', 'c', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->smsUslugi = $this->getContainer()->get('sms_uslugi_ru');
        $this->notify = $this->getContainer()->get('app.notify');
        $this->settings = $this->em->getRepository('AppBundle:Setting');
        $this->trans = $this->getContainer()->get('translator');

        /** @var $regionRepo \Doctrine\ORM\EntityRepository */
        $regionRepo = $this->em->getRepository('AppBundle:Region');
        $regions = $regionRepo->findAll();
        foreach ($regions as $region) { /** @var $region \My\AppBundle\Entity\Region */
            if ($region->isDiscount2FirstEnabled()) {
                $days = $region->getDiscount2FirstDays();
                $this->send($region, $days, 'first');
            }

            if ($region->isDiscount2SecondEnabled()) {
                $days = $region->getDiscount2FirstDays();
                $days += $region->getDiscount2BetweenPeriodDays();
                $days += $region->getDiscount2SecondDays();
                $this->send($region, $days, 'second');
            }
        }

        if ($this->cnt) {
            $c = $input->getOption('cron') ? date('Y-m-d H:i:s').' | ' : '';
            $output->writeln($c.'Sended <info>'.$this->cnt.'</info> warning messages.');
        }
    }

    protected function send(Region $region, $days, $type)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P'.$days.'D'));
        $users = $this->em->getRepository('AppBundle:User')->createQueryBuilder('u')
            ->andWhere('u.region = :region')->setParameter(':region', $region)
            ->andWhere('u.payment_1_paid = :date')->setParameter(':date', $date->format('Y-m-d'))
            ->andWhere('u.payment_2_paid IS NULL')
            ->andWhere('u.not_all_mailing = :not_mailing')->setParameter(':not_mailing', false)
            ->getQuery()->getResult();

        $now = new \DateTime();
        $tomorrow = new \DateTime('tomorrow');
        $timeout = $now->diff($tomorrow)->format('%h ч. %i мин.');
        $end_time = $now->format('Y.m.d 23:59:59');
        $discount = call_user_func(array($region, 'getDiscount2'.ucfirst($type).'Amount'));
        $price = 0;
        foreach ($region->getServicesPrices() as $service_price) {
            /** @var $service_price \My\AppBundle\Entity\ServicePrice */

            if ($service_price->getActive() && $service_price->getService()->getType() == 'training') {
                $price += $service_price->getPrice();
            }
        }
        $new_price = max($price - $discount, 0);

        $sms = $this->settings->get('discount_2_warning_sms_'.$type);
        $email_subject = $this->settings->get('discount_2_warning_'.$type.'_email_title');
        $email_message = $this->settings->get('discount_2_warning_'.$type.'_email_text');

        foreach ($users as $user) { /** @var $user \My\AppBundle\Entity\User */
            if ('confirmed' == $user->getPhoneMobileStatus()) {
                $message = $sms;
                $message = str_replace('{{ timeout }}', $timeout, $message);
                $this->smsUslugi->query('+7'.$user->getPhoneMobile(), $message);
            }

            $message = $email_message;
            $message = str_replace('{{ end_time }}', $end_time, $message);
            $message = str_replace('{{ discount }}', $discount, $message);
            $message = str_replace('{{ price }}', $price, $message);
            $message = str_replace('{{ new_price }}', $new_price, $message);
            $this->notify->sendEmail($user, $email_subject, $message, 'text/html');

            $this->cnt ++;
        }
    }
}
