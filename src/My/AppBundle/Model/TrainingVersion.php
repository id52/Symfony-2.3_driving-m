<?php

namespace My\AppBundle\Model;

/**
 * TrainingVersion
 */
abstract class TrainingVersion
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $start_date;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \My\AppBundle\Entity\Category
     */
    protected $category;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $subjects;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $slices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $themes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $questions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subjects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->slices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->themes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return TrainingVersion
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return TrainingVersion
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return TrainingVersion
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set category
     *
     * @param \My\AppBundle\Entity\Category $category
     * @return TrainingVersion
     */
    public function setCategory(\My\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \My\AppBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add subjects
     *
     * @param \My\AppBundle\Entity\Subject $subjects
     * @return TrainingVersion
     */
    public function addSubject(\My\AppBundle\Entity\Subject $subjects)
    {
        $this->subjects[] = $subjects;

        return $this;
    }

    /**
     * Remove subjects
     *
     * @param \My\AppBundle\Entity\Subject $subjects
     */
    public function removeSubject(\My\AppBundle\Entity\Subject $subjects)
    {
        $this->subjects->removeElement($subjects);
    }

    /**
     * Get subjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Add slices
     *
     * @param \My\AppBundle\Entity\Slice $slices
     * @return TrainingVersion
     */
    public function addSlice(\My\AppBundle\Entity\Slice $slices)
    {
        $this->slices[] = $slices;

        return $this;
    }

    /**
     * Remove slices
     *
     * @param \My\AppBundle\Entity\Slice $slices
     */
    public function removeSlice(\My\AppBundle\Entity\Slice $slices)
    {
        $this->slices->removeElement($slices);
    }

    /**
     * Get slices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSlices()
    {
        return $this->slices;
    }

    /**
     * Add themes
     *
     * @param \My\AppBundle\Entity\Theme $themes
     * @return TrainingVersion
     */
    public function addTheme(\My\AppBundle\Entity\Theme $themes)
    {
        $this->themes[] = $themes;

        return $this;
    }

    /**
     * Remove themes
     *
     * @param \My\AppBundle\Entity\Theme $themes
     */
    public function removeTheme(\My\AppBundle\Entity\Theme $themes)
    {
        $this->themes->removeElement($themes);
    }

    /**
     * Get themes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Add questions
     *
     * @param \My\AppBundle\Entity\Question $questions
     * @return TrainingVersion
     */
    public function addQuestion(\My\AppBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;

        return $this;
    }

    /**
     * Remove questions
     *
     * @param \My\AppBundle\Entity\Question $questions
     */
    public function removeQuestion(\My\AppBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
