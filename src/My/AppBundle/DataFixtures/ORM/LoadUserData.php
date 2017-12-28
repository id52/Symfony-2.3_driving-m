<?php

namespace My\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use My\AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setLastName('');
        $user->setFirstName('Admin');
        $user->setPatronymic('');
        $user->setEmail('admin@example.com');
        $user->setPlainPassword('admin');
        $user->addRole('ROLE_ADMIN');
        $user->setEnabled(true);
        $user->setPaidNotifiedAt(new \DateTime());
        $user->setPayment1Paid(new \DateTime());
        $user->setPayment2Paid(new \DateTime());
        $manager->persist($user);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
