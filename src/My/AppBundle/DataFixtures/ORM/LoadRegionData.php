<?php

namespace My\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use My\AppBundle\Entity\Category;
use My\AppBundle\Entity\CategoryPrice;
use My\AppBundle\Entity\Region;

class LoadRegionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $region1 = new Region();
        $region1->setName('Москва');
        $manager->persist($region1);

        $region2 = new Region();
        $region2->setName('Питер');
        $manager->persist($region2);

        $category1 = new Category();
        $category1->setName('A');
        $category1->setTheory(10);
        $category1->setPractice(20);
        $category1->setTraining(2.1);
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('B');
        $category2->setTheory(15);
        $category2->setPractice(30);
        $category2->setTraining(2.3);
        $category2->setWithAt(true);
        $manager->persist($category2);

        $manager->flush();

        $price = new CategoryPrice();
        $price->setActive(true);
        $price->setPriceEdu(3000);
        $price->setPriceDrv(10000);
        $price->setPriceDrvAt(0);
        $price->setRegion($region1);
        $price->setCategory($category1);
        $manager->persist($price);

        $price = new CategoryPrice();
        $price->setActive(true);
        $price->setPriceEdu(3500);
        $price->setPriceDrv(0);
        $price->setPriceDrvAt(15000);
        $price->setRegion($region1);
        $price->setCategory($category2);
        $manager->persist($price);

        $price = new CategoryPrice();
        $price->setActive(true);
        $price->setPriceEdu(2000);
        $price->setPriceDrv(8000);
        $price->setPriceDrvAt(0);
        $price->setRegion($region2);
        $price->setCategory($category1);
        $manager->persist($price);

        $price = new CategoryPrice();
        $price->setActive(false);
        $price->setPriceEdu(2500);
        $price->setPriceDrv(0);
        $price->setPriceDrvAt(12000);
        $price->setRegion($region2);
        $price->setCategory($category2);
        $manager->persist($price);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
