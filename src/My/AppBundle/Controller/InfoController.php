<?php

namespace My\AppBundle\Controller;

use Doctrine\ORM\Query;
use My\AppBundle\Entity\SupportDialog;
use My\AppBundle\Entity\SupportMessage;
use My\AppBundle\Entity\TestKnowledgeLog;
use My\AppBundle\Entity\TestLog;
use My\AppBundle\Entity\UserOldMobilePhone;
use My\AppBundle\Form\Type\PhotoFormType;
use My\AppBundle\Form\Type\ProfileFormType;
use My\AppBundle\Form\Type\SupportMessageFormType;
use My\PaymentBundle\Entity\Log as PaymentLog;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints as Assert;

class InfoController extends MyAbstract
{
    public function listAction()
    {
        $infos = $this->em->getRepository('AppBundle:Info')->createQueryBuilder('i')
            ->andWhere('i.release_time <= :time')->setParameter('time', new \DateTime())
            ->getQuery()->getResult();

        $infoArr = [];

        foreach ($infos as $info) { /** @var $info \My\AppBundle\Entity\Info */
            $i = [];
            $i['title']        = $info->getTitle();
            $i['text']         = $info->getText();
            $i['release_time'] = $info->getReleaseTime();
            $i['id']           = $info->getId();
            $i['is_readed']    = false;

            if ($info->isReaded($this->getUser())) {
                $i['is_readed'] = true;
            };
            $infoArr[] = $i;
        }

        return $this->render('AppBundle:Info:list.html.twig', ['infos' => $infoArr]);
    }

    public function itemAction($id)
    {
        $info = $this->em->getRepository('AppBundle:Info')->find($id);
        if (!$info) {
            throw $this->createNotFoundException('Info for id "'.$id.'" not found.');
        }

        if (!$info->isReaded($this->getUser())) {
            $info->addReader($this->getUser());
            $this->em->persist($info);
            $this->em->flush();
        }

        return $this->render('AppBundle:Info:item.html.twig', ['info' => $info]);
    }
}
