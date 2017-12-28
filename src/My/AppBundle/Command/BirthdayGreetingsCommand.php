<?php

namespace My\AppBundle\Command;

use My\AppBundle\AppBundle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BirthdayGreetingsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:birthday')
            ->setDescription('Поздравления с днем рождения')
            ->addOption('cron', 'c', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $notify = $this->getContainer()->get('app.notify');

        $cnt = 0;

        $users = $em->getRepository('AppBundle:User')->findByUserBirthdayToday();
        if ($users) {
            foreach ($users as $user) {
                if (!$user['not_all_mailing']) {
                    $notify->sendCongratulationBirthday($user);
                    $cnt++;
                }
            }
        }

        if ($cnt) {
            $c = $input->getOption('cron') ? date('Y-m-d H:i:s').' | ' : '';
            $output->writeln($c.'Sended <info>'.$cnt.'</info> HB messages.');
        }
    }
}
