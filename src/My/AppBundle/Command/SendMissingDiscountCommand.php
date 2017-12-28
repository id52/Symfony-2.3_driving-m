<?php

/**
 * Added in crontab
 * 5 9 * * *
 */

namespace My\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendMissingDiscountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:send-missing-discount')
            ->addOption('cron', 'c', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $sms_uslugi = $this->getContainer()->get('sms_uslugi_ru');
        $notify = $this->getContainer()->get('app.notify');
        $settings_repository = $em->getRepository('AppBundle:Setting');

        $sms = $settings_repository->get('discount_2_missing_sms');
        $email_subject = $settings_repository->get('discount_2_missing_email_title');
        $email_message = $settings_repository->get('discount_2_missing_email_text');

        $cnt = 0;
        $regions = $em->getRepository('AppBundle:Region')->findAll();
        foreach ($regions as $region) { /** @var $region \My\AppBundle\Entity\Region */
            if ($region->isDiscount2SecondEnabled()) {
                $date_paid = new \DateTime('today');
                $days = $region->getDiscount2FirstDays() + $region->getDiscount2BetweenPeriodDays() + 1;
                $date_paid->sub(new \DateInterval('P'.$days.'D'));

                $now = new \DateTime();
                $date = new \DateTime('today');
                $days = $region->getDiscount2SecondDays();
                $date->add(new \DateInterval('P'.$days.'D'));
                $timeout = $now->diff($date)->format('%d д. %h ч. %i мин.');
                $date->sub(new \DateInterval('P1D'));
                $end_time = $date->format('Y.m.d 23:59:59');
                $discount = $region->getDiscount2SecondAmount();
                $price = 0;
                foreach ($region->getServicesPrices() as $service_price) {
                    /** @var $service_price \My\AppBundle\Entity\ServicePrice */

                    if ($service_price->getActive() && $service_price->getService()->getType() == 'training') {
                        $price += $service_price->getPrice();
                    }
                }
                $new_price = max($price - $discount, 0);

                $users = $em->getRepository('AppBundle:User')->createQueryBuilder('u')
                    ->andWhere('u.region = :region')->setParameter(':region', $region)
                    ->andWhere('u.payment_1_paid = :date')->setParameter(':date', $date_paid->format('Y-m-d'))
                    ->andWhere('u.payment_2_paid IS NULL')
                    ->andWhere('u.not_all_mailing = :not_mailing')->setParameter(':not_mailing', false)
                    ->getQuery()->getResult();
                foreach ($users as $user) { /** @var $user \My\AppBundle\Entity\User */
                    if ('confirmed' == $user->getPhoneMobileStatus()) {
                        $message = $sms;
                        $message = str_replace('{{ timeout }}', $timeout, $message);
                        $sms_uslugi->query('+7'.$user->getPhoneMobile(), $message);
                    }

                    $message = $email_message;
                    $message = str_replace('{{ end_time }}', $end_time, $message);
                    $message = str_replace('{{ discount }}', $discount, $message);
                    $message = str_replace('{{ price }}', $price, $message);
                    $message = str_replace('{{ new_price }}', $new_price, $message);
                    $notify->sendEmail($user, $email_subject, $message, 'text/html');

                    $cnt ++;
                }
            }
        }

        if ($cnt) {
            $c = $input->getOption('cron') ? date('Y-m-d H:i:s').' | ' : '';
            $output->writeln($c.'Sended <info>'.$cnt.'</info> missing messages.');
        }
    }
}
