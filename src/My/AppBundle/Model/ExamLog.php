<?php

namespace My\AppBundle\Model;

/**
 * ExamLog
 */
abstract class ExamLog
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $started_at;

    /**
     * @var \DateTime
     */
    protected $ended_at;

    /**
     * @var array
     */
    protected $questions;

    /**
     * @var array
     */
    protected $answers;

    /**
     * @var boolean
     */
    protected $passed;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user;

    /**
     * @var \My\AppBundle\Entity\Subject
     */
    protected $subject;


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
     * Set started_at
     *
     * @param \DateTime $startedAt
     * @return ExamLog
     */
    public function setStartedAt($startedAt)
    {
        $this->started_at = $startedAt;

        return $this;
    }

    /**
     * Get started_at
     *
     * @return \DateTime 
     */
    public function getStartedAt()
    {
        return $this->started_at;
    }

    /**
     * Set ended_at
     *
     * @param \DateTime $endedAt
     * @return ExamLog
     */
    public function setEndedAt($endedAt)
    {
        $this->ended_at = $endedAt;

        return $this;
    }

    /**
     * Get ended_at
     *
     * @return \DateTime 
     */
    public function getEndedAt()
    {
        return $this->ended_at;
    }

    /**
     * Set questions
     *
     * @param array $questions
     * @return ExamLog
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions
     *
     * @return array 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set answers
     *
     * @param array $answers
     * @return ExamLog
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get answers
     *
     * @return array 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set passed
     *
     * @param boolean $passed
     * @return ExamLog
     */
    public function setPassed($passed)
    {
        $this->passed = $passed;

        return $this;
    }

    /**
     * Get passed
     *
     * @return boolean 
     */
    public function getPassed()
    {
        return $this->passed;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return ExamLog
     */
    public function setUser(\My\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \My\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set subject
     *
     * @param \My\AppBundle\Entity\Subject $subject
     * @return ExamLog
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
}
