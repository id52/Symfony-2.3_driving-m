<?php

namespace My\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SupportDialogsMailingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:sd-mailing')
            ->addOption('cron', 'c', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router = $this->getContainer()->get('router');
        /** @var $router_cntxt \Symfony\Component\Routing\RequestContext */
        $router_cntxt = $router->getContext();
        $host = $this->getContainer()->getParameter('host');
        $router_cntxt->setHost($host);
        $notifier = $this->getContainer()->get('app.notify');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $cnt = 0;

        //outdate dialogs
        $dialogs = $em->getRepository('AppBundle:SupportDialog')->getOutdatedDialogs();

        if ($dialogs) {
            //list of user to inform (admins or moderators, e.g.)
            $recievers = $em->getRepository('AppBundle:User')->findByRole('ROLE_ADMIN');

            //generate Email subject and message
            $subject = 'В системе обратной связи на '.$host.' есть неотвеченные в срок диалоги ('.count($dialogs).')';
            $message = '<p>Оповещаем, что в системе обратной связи на '.$host;
            $message .= ' есть неотвеченные в срок диалоги ('.count($dialogs).').</p>';
            $message .= '<ul>';
            foreach ($dialogs as $dialog) {
                $link = $router->generate('admin_support_dialog_show', array('id' => $dialog['id']), true);
                $message .= '<li><a href="'.$link.'">Диалог №'.$dialog['id'];
                $message .= ', категория '.$dialog['parentCategoryName'].':'.$dialog['categoryName'].'</a></li>';
            }
            $message .= '</ul>';

            foreach ($recievers as $user) { /** @var $user \My\AppBundle\Entity\User */
                $notifier->sendEmail($user->getEmail(), $subject, $message, 'text/html');
            }
        }

        if ($cnt) {
            $c = $input->getOption('cron') ? date('Y-m-d H:i:s').' | ' : '';
            $output->writeln($c.'Sended <info>'.$cnt.'</info> e-mails.');
        }
    }
}
