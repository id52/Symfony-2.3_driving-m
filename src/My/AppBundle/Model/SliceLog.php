<?php

namespace My\AppBundle\Model;

/**
 * SliceLog
 */
abstract class SliceLog
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
     * @var \My\AppBundle\Entity\Slice
     */
    protected $slice;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user;


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
     * @return SliceLog
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
     * @return SliceLog
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
     * @return SliceLog
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
     * @return SliceLog
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
     * @return SliceLog
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
     * Set slice
     *
     * @param \My\AppBundle\Entity\Slice $slice
     * @return SliceLog
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
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return SliceLog
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
}
