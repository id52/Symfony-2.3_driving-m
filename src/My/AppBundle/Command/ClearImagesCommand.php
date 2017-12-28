<?php

/**
 * Added in crontab
 * 0 5 * * 1
 */

namespace My\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearImagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:clear-images')
            ->addOption('cron', 'c', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $cnt = 0;
        $images = $em->getRepository('AppBundle:Image')->createQueryBuilder('i')
            ->andWhere('i.question IS NULL')
            ->andWhere('i.category IS NULL')
            ->andWhere('i.subject IS NULL')
            ->andWhere('i.offer IS NULL')
            ->andWhere('i.offer_student IS NULL')
            ->andWhere('i.filial IS NULL')
            ->andWhere('i.site IS NULL')
            ->andWhere('i.flash_block_item IS NULL')
            ->andWhere('i.review IS NULL')
            ->andWhere('i.pass_filial IS NULL')
            ->andWhere('i.how_work IS NULL')
            ->getQuery()->execute();
        foreach ($images as $image) {
            $em->remove($image);
            $em->flush();

            $cnt++;
        }

        if ($cnt) {
            $c = $input->getOption('cron') ? date('Y-m-d H:i:s').' | ' : '';
            $output->writeln($c.'Removed <info>'.$cnt.'</info> images.');
        }
    }
}
