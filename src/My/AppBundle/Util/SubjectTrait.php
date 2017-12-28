<?php

namespace My\AppBundle\Util;

trait SubjectTrait
{
    protected function getValidSubjects()
    {
        $regions           = $this->em->getRepository('AppBundle:Region')->findAll();
        $categories        = $this->em->getRepository('AppBundle:Category')->findAll();
        $validSubjects     = [];
        $versionCategories = [];

        foreach ($categories as $category) { /** @var $category \My\AppBundle\Entity\Category */
            $version = $this->em->getRepository('AppBundle:TrainingVersion')->createQueryBuilder('v')
                ->andWhere('v.category = :category')->setParameter('category', $category)
                ->andWhere('v.start_date <= :start_date')->setParameter('start_date', new \DateTime())
                ->addOrderBy('v.start_date', 'DESC')
                ->setMaxResults(1)->getQuery()->getOneOrNullResult();

            if ($version) { /** @var $version \My\AppBundle\Entity\TrainingVersion */
                $versionCategories[$category->getId()] = [];
                foreach ($version->getSubjects() as $subject) {  /** @var $subject \My\AppBundle\Entity\Subject */
                    $versionCategories[$category->getId()][$subject->getId()] = $subject->getId();
                }
            }
        }

        foreach ($regions as $region) { /** @var $region \My\AppBundle\Entity\Region */
            $categories = $this->em->getRepository('AppBundle:Category')->createQueryBuilder('c')
                ->leftJoin('c.categories_prices', 'cp')
                ->andWhere('cp.active  = :active')->setParameter('active', true)
                ->andWhere('cp.region  = :region')->setParameter('region', $region)
                ->getQuery()->execute();
            foreach ($categories as $category) {
                if (isset($versionCategories[$category->getId()])) {
                    $validSubjects[$region->getId()][$category->getId()] = $versionCategories[$category->getId()];
                }
            }
        }
        return $validSubjects;
    }

    private function getRegionCategories()
    {
        $region_categories = array();
        $region_categories_source = $this->em->getRepository('AppBundle:Region')->createQueryBuilder('r')
            ->leftJoin('r.categories_prices', 'cp')
            ->leftJoin('cp.category', 'c')
            ->getQuery()->getResult();
        foreach ($region_categories_source as $region) { /** @var $region \My\AppBundle\Entity\Region */
            if (!isset($region_categories[$region->getId()])) {
                $region_categories[$region->getId()] = array();
            }
            foreach ($region->getCategoriesPrices() as $cp) { /** @var $cp \My\AppBundle\Entity\CategoryPrice */
                if ($cp->getActive()) {
                    $category = $cp->getCategory();
                    $region_categories[$region->getId()][$category->getId()] = array(
                        'name'    => $category->getName(),
                        'with_at' => $category->getWithAt(),
                    );
                }
            }
        }
        return $region_categories;
    }
}
