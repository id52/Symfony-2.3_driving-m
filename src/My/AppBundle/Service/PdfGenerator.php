<?php

namespace My\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use My\AppBundle\Entity\User;

function pdfff_escape($str)
{
    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');

    return $str;
}

class PdfGenerator
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;
    /** @var \AppKernel */
    protected $kernel;

    public function __construct(EntityManager $em, \AppKernel $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;
    }

    public function generate($source, $dest, $values)
    {
        //paths
        $scriptPath = $this->kernel->getRootDir().'/PdfFormFiller/PdfFormFiller.jar';
        $fontPath = $this->kernel->getRootDir().'/../src/My/AppBundle/Resources/public_source/fonts/arial.ttf';

        //make pipe, set values etc
        $f = proc_open(
            'java -jar '.$scriptPath.' '.$source.' -font "'.$fontPath.'" -flatten '.$dest.' 2>&1',
            array(
                0 => array("pipe", "r"), // stdin is a pipe that the child will read from
                1 => array("pipe", "w"), // stdout is a pipe that the child will write to
                // 2 => array("file", "error-output.txt", "a") // stderr is a file to write to
                2 => array("pipe", "w")  // Actually, stderr is sent to stdin " 2>&1"
                // in $cmd above, as select() in php is not reliable.
            ),
            $pipes,
            null, // On FreeBSD open_proc() is not run in a shell and
            // thus a PATH with 'usr/local/bin' and CWD are not set.
            // $env['PATH'] = '/usr/bin:/bin:/usr/local/bin';
            // $path = '/var/www/linux.org/site/htroot/doc';
            array('LANG' => 'ru_RU.UTF-8')
        );

        //values
        foreach ($values as $name => $value) {
            fwrite($pipes[0], $name.' '.pdfff_escape($value).PHP_EOL);
        }
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($f); //must be 0
    }

    public function generateCertificate(User $user)
    {
        $certWebPath = '/certificates/';
        $pdfSourceName = $this->kernel->getRootDir().'/../src/My/AppBundle/Resources/pdf/certificate.pdf';
        $pdfName = md5(time().$user->getId());
        $pdfSitePath = $certWebPath.$pdfName.'.pdf';
        $pdfServerName = $this->kernel->getRootDir().'/../web'.$pdfSitePath;
        $date = new \DateTime();
        $months = array(
            1 => 'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря'
        );
        $values = array(
            'number'                 => '№ '.$user->getId(),
            'Surname'                => $user->getLastName(),
            'First_name_Second_name' => $user->getFirstName().' '.$user->getPatronymic(),
            'City'                   => $user->getRegion()->getName(),
            'Date'                   => $date->format('d').' '.$months[$date->format('n')].' '.$date->format('Y').' г.'
        );
        $this->generate($pdfSourceName, $pdfServerName, $values);
        $user->setCertificate($pdfName);
        $this->em->persist($user);
        $this->em->flush();

        return $pdfSitePath;
    }
}
