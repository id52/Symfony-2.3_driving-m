<?php

/**
 * Added in crontab
 * 0 3 * * *
 */

namespace My\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendMailingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:send-mailing')
            ->addOption('cron', 'c', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router_cntxt = $this->getContainer()->get('router')->getContext();
        $router_cntxt->setHost($this->getContainer()->getParameter('host'));

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $notify = $this->getContainer()->get('app.notify');
        $translator = $this->getContainer()->get('translator');

        $cnt = 0;
        $test_users = array();
        $test_emails = $em->getRepository('AppBundle:TestEmail')->findAll();
        foreach ($test_emails as $test_email) { /** @var $test_email \My\AppBundle\Entity\TestEmail */
            $test_users[] = array(
                'email'      => $test_email->getEmail(),
                'first_name' => $translator->trans('test_email_first_name', array(), 'test_email'),
                'last_name'  => $translator->trans('test_email_last_name', array(), 'test_email'),
            );
        }

        $mailing = $em->getRepository('AppBundle:Mailing')->createQueryBuilder('m')
            ->andWhere('m.date = :date')->setParameter(':date', new \DateTime(date('Y-m-d')))
            ->getQuery()->execute();
        if ($mailing) {
            foreach ($mailing as $mail) { /** @var $mail \My\AppBundle\Entity\Mailing */
                $users_ids = $mail->getUsers();
                if (count($users_ids) > 0) {
                    $qb = $em->getRepository('AppBundle:User')->createQueryBuilder('u')
                        ->andWhere('u.id IN (:ids)')->setParameter(':ids', $users_ids)
                        ->andWhere('u.not_all_mailing = :not_mailing')->setParameter(':not_mailing', false);
                    ;

                    // Отправка писем даже отписавшимся
                    if (!$mail->getForceSending()) {
                        $qb->andWhere('u.mailing = :mailing')->setParameter(':mailing', true);
                    }

                    $users = $qb->getQuery()->getArrayResult();
                    foreach ($users as $user) {
                        $notify->sendMailing($user, $mail->getTitle(), $mail->getMessage());
                        $cnt++;
                    }

                    //Отправка контрольных email
                    foreach ($test_users as $user) {
                        $notify->sendMailing($user, $mail->getTitle(), $mail->getMessage());
                    }
                }
            }
        }

        if ($cnt) {
            $c = $input->getOption('cron') ? date('Y-m-d H:i:s').' | ' : '';
            $output->writeln($c.'Sended <info>'.$cnt.'</info> e-mails.');
        }
    }
}
