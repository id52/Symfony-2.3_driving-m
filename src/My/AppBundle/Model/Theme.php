<?php

namespace My\AppBundle\Model;

/**
 * Theme
 */
abstract class Theme
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
    protected $text;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var \My\AppBundle\Entity\Slice
     */
    protected $slice;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $questions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $tests_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $support_dialogs;

    /**
     * @var \My\AppBundle\Entity\Subject
     */
    protected $subject;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $readers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $versions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tests_logs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->support_dialogs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->readers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Theme
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
     * Set text
     *
     * @param string $text
     * @return Theme
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Theme
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set slice
     *
     * @param \My\AppBundle\Entity\Slice $slice
     * @return Theme
     */
    public function setSlice(\My\AppBundle\Entity\Slice $slice = null)
    {
        $this->slice = $slice;

        return $this;
    }

    /**
     * Get slice
     *
     * @return \My\AppBundle\Entity\Slice 
     */
    public function getSlice()
    {
        return $this->slice;
    }

    /**
     * Add questions
     *
     * @param \My\AppBundle\Entity\Question $questions
     * @return Theme
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

    /**
     * Add tests_logs
     *
     * @param \My\AppBundle\Model\ThemeTestLog $testsLogs
     * @return Theme
     */
    public function addTestsLog(\My\AppBundle\Model\ThemeTestLog $testsLogs)
    {
        $this->tests_logs[] = $testsLogs;

        return $this;
    }

    /**
     * Remove tests_logs
     *
     * @param \My\AppBundle\Model\ThemeTestLog $testsLogs
     */
    public function removeTestsLog(\My\AppBundle\Model\ThemeTestLog $testsLogs)
    {
        $this->tests_logs->removeElement($testsLogs);
    }

    /**
     * Get tests_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTestsLogs()
    {
        return $this->tests_logs;
    }

    /**
     * Add support_dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $supportDialogs
     * @return Theme
     */
    public function addSupportDialog(\My\AppBundle\Entity\SupportDialog $supportDialogs)
    {
        $this->support_dialogs[] = $supportDialogs;

        return $this;
    }

    /**
     * Remove support_dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $supportDialogs
     */
    public function removeSupportDialog(\My\AppBundle\Entity\SupportDialog $supportDialogs)
    {
        $this->support_dialogs->removeElement($supportDialogs);
    }

    /**
     * Get support_dialogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSupportDialogs()
    {
        return $this->support_dialogs;
    }

    /**
     * Set subject
     *
     * @param \My\AppBundle\Entity\Subject $subject
     * @return Theme
     */
    public function setSubject(\My\AppBundle\Entity\Subject $subject = null)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return \My\AppBundle\Entity\Subject 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Add readers
     *
     * @param \My\AppBundle\Entity\User $readers
     * @return Theme
     */
    public function addReader(\My\AppBundle\Entity\User $readers)
    {
        $this->readers[] = $readers;

        return $this;
    }

    /**
     * Remove readers
     *
     * @param \My\AppBundle\Entity\User $readers
     */
    public function removeReader(\My\AppBundle\Entity\User $readers)
    {
        $this->readers->removeElement($readers);
    }

    /**
     * Get readers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReaders()
    {
        return $this->readers;
    }

    /**
     * Add versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $versions
     * @return Theme
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
}
