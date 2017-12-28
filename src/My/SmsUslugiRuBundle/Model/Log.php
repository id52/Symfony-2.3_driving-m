<?php

namespace My\SmsUslugiRuBundle\Model;

/**
 * Log
 */
abstract class Log
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $s_id;

    /**
     * @var array
     */
    protected $s_answer;

    /**
     * @var \DateTime
     */
    protected $created_at;


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
     * Set text
     *
     * @param string $text
     * @return Log
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
     * Set number
     *
     * @param string $number
     * @return Log
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set s_id
     *
     * @param string $sId
     * @return Log
     */
    public function setSId($sId)
    {
        $this->s_id = $sId;

        return $this;
    }

    /**
     * Get s_id
     *
     * @return string 
     */
    public function getSId()
    {
        return $this->s_id;
    }

    /**
     * Set s_answer
     *
     * @param array $sAnswer
     * @return Log
     */
    public function setSAnswer($sAnswer)
    {
        $this->s_answer = $sAnswer;

        return $this;
    }

    /**
     * Get s_answer
     *
     * @return array 
     */
    public function getSAnswer()
    {
        return $this->s_answer;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Log
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
}
