<?php

namespace My\AppBundle\Model;

/**
 * Subject
 */
abstract class Subject
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $brief_description;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $themes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $exams_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $versions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $closed_users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->themes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->exams_logs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->closed_users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Subject
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set brief_description
     *
     * @param string $briefDescription
     * @return Subject
     */
    public function setBriefDescription($briefDescription)
    {
        $this->brief_description = $briefDescription;

        return $this;
    }

    /**
     * Get brief_description
     *
     * @return string 
     */
    public function getBriefDescription()
    {
        return $this->brief_description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Subject
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return Subject
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
     * Add themes
     *
     * @param \My\AppBundle\Entity\Theme $themes
     * @return Subject
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
     * Add exams_logs
     *
     * @param \My\AppBundle\Entity\ExamLog $examsLogs
     * @return Subject
     */
    public function addExamsLog(\My\AppBundle\Entity\ExamLog $examsLogs)
    {
        $this->exams_logs[] = $examsLogs;

        return $this;
    }

    /**
     * Remove exams_logs
     *
     * @param \My\AppBundle\Entity\ExamLog $examsLogs
     */
    public function removeExamsLog(\My\AppBundle\Entity\ExamLog $examsLogs)
    {
        $this->exams_logs->removeElement($examsLogs);
    }

    /**
     * Get exams_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExamsLogs()
    {
        return $this->exams_logs;
    }

    /**
     * Add versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $versions
     * @return Subject
     */
    public function addVersion(\My\AppBundle\Entity\TrainingVersion $versions)
    {
        $this->versions[] = $versions;

        return $this;
    }

    /**
     * Remove versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $versions
     */
    public function removeVersion(\My\AppBundle\Entity\TrainingVersion $versions)
    {
        $this->versions->removeElement($versions);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Add closed_users
     *
     * @param \My\AppBundle\Entity\User $closedUsers
     * @return Subject
     */
    public function addClosedUser(\My\AppBundle\Entity\User $closedUsers)
    {
        $this->closed_users[] = $closedUsers;

        return $this;
    }

    /**
     * Remove closed_users
     *
     * @param \My\AppBundle\Entity\User $closedUsers
     */
    public function removeClosedUser(\My\AppBundle\Entity\User $closedUsers)
    {
        $this->closed_users->removeElement($closedUsers);
    }

    /**
     * Get closed_users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClosedUsers()
    {
        return $this->closed_users;
    }
}
