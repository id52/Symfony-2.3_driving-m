<?php

namespace My\AppBundle\Model;

/**
 * Category
 */
abstract class Category
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $with_at;

    /**
     * @var integer
     */
    protected $theory;

    /**
     * @var integer
     */
    protected $practice;

    /**
     * @var string
     */
    protected $training;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $categories_prices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $offers_prices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $offers_prices_students;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $reviews;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $training_versions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories_prices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offers_prices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offers_prices_students = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->training_versions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set with_at
     *
     * @param boolean $withAt
     * @return Category
     */
    public function setWithAt($withAt)
    {
        $this->with_at = $withAt;

        return $this;
    }

    /**
     * Get with_at
     *
     * @return boolean 
     */
    public function getWithAt()
    {
        return $this->with_at;
    }

    /**
     * Set theory
     *
     * @param integer $theory
     * @return Category
     */
    public function setTheory($theory)
    {
        $this->theory = $theory;

        return $this;
    }

    /**
     * Get theory
     *
     * @return integer 
     */
    public function getTheory()
    {
        return $this->theory;
    }

    /**
     * Set practice
     *
     * @param integer $practice
     * @return Category
     */
    public function setPractice($practice)
    {
        $this->practice = $practice;

        return $this;
    }

    /**
     * Get practice
     *
     * @return integer 
     */
    public function getPractice()
    {
        return $this->practice;
    }

    /**
     * Set training
     *
     * @param string $training
     * @return Category
     */
    public function setTraining($training)
    {
        $this->training = $training;

        return $this;
    }

    /**
     * Get training
     *
     * @return string 
     */
    public function getTraining()
    {
        return $this->training;
    }

    /**
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return Category
     */
    public function setImage(\My\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \My\AppBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add categories_prices
     *
     * @param \My\AppBundle\Model\CategoryPrice $categoriesPrices
     * @return Category
     */
    public function addCategoriesPrice(\My\AppBundle\Model\CategoryPrice $categoriesPrices)
    {
        $this->categories_prices[] = $categoriesPrices;

        return $this;
    }

    /**
     * Remove categories_prices
     *
     * @param \My\AppBundle\Model\CategoryPrice $categoriesPrices
     */
    public function removeCategoriesPrice(\My\AppBundle\Model\CategoryPrice $categoriesPrices)
    {
        $this->categories_prices->removeElement($categoriesPrices);
    }

    /**
     * Get categories_prices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategoriesPrices()
    {
        return $this->categories_prices;
    }

    /**
     * Add offers_prices
     *
     * @param \My\AppBundle\Entity\OfferPrice $offersPrices
     * @return Category
     */
    public function addOffersPrice(\My\AppBundle\Entity\OfferPrice $offersPrices)
    {
        $this->offers_prices[] = $offersPrices;

        return $this;
    }

    /**
     * Remove offers_prices
     *
     * @param \My\AppBundle\Entity\OfferPrice $offersPrices
     */
    public function removeOffersPrice(\My\AppBundle\Entity\OfferPrice $offersPrices)
    {
        $this->offers_prices->removeElement($offersPrices);
    }

    /**
     * Get offers_prices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffersPrices()
    {
        return $this->offers_prices;
    }

    /**
     * Add offers_prices_students
     *
     * @param \My\AppBundle\Entity\OfferPriceStudent $offersPricesStudents
     * @return Category
     */
    public function addOffersPricesStudent(\My\AppBundle\Entity\OfferPriceStudent $offersPricesStudents)
    {
        $this->offers_prices_students[] = $offersPricesStudents;

        return $this;
    }

    /**
     * Remove offers_prices_students
     *
     * @param \My\AppBundle\Entity\OfferPriceStudent $offersPricesStudents
     */
    public function removeOffersPricesStudent(\My\AppBundle\Entity\OfferPriceStudent $offersPricesStudents)
    {
        $this->offers_prices_students->removeElement($offersPricesStudents);
    }

    /**
     * Get offers_prices_students
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffersPricesStudents()
    {
        return $this->offers_prices_students;
    }

    /**
     * Add users
     *
     * @param \My\AppBundle\Entity\User $users
     * @return Category
     */
    public function addUser(\My\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \My\AppBundle\Entity\User $users
     */
    public function removeUser(\My\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add reviews
     *
     * @param \My\AppBundle\Entity\Review $reviews
     * @return Category
     */
    public function addReview(\My\AppBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \My\AppBundle\Entity\Review $reviews
     */
    public function removeReview(\My\AppBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add training_versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $trainingVersions
     * @return Category
     */
    public function addTrainingVersion(\My\AppBundle\Entity\TrainingVersion $trainingVersions)
    {
        $this->training_versions[] = $trainingVersions;

        return $this;
    }

    /**
     * Remove training_versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $trainingVersions
     */
    public function removeTrainingVersion(\My\AppBundle\Entity\TrainingVersion $trainingVersions)
    {
        $this->training_versions->removeElement($trainingVersions);
    }

    /**
     * Get training_versions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrainingVersions()
    {
        return $this->training_versions;
    }
}
