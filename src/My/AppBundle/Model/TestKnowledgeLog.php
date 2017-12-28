<?php

namespace My\AppBundle\Model;

/**
 * TestKnowledgeLog
 */
abstract class TestKnowledgeLog
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
     * @return TestKnowledgeLog
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
     * @return TestKnowledgeLog
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
     * @return TestKnowledgeLog
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
     * @return TestKnowledgeLog
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
     * @return TestKnowledgeLog
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
     * @return TestKnowledgeLog
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
