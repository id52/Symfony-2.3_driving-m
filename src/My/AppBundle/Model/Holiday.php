<?php

namespace My\AppBundle\Model;

/**
 * Holiday
 */
abstract class Holiday
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $entry_value;

    /**
     * @var boolean
     */
    protected $exception;


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
     * Set entry_value
     *
     * @param \DateTime $entryValue
     * @return Holiday
     */
    public function setEntryValue($entryValue)
    {
        $this->entry_value = $entryValue;

        return $this;
    }

    /**
     * Get entry_value
     *
     * @return \DateTime 
     */
    public function getEntryValue()
    {
        return $this->entry_value;
    }

    /**
     * Set exception
     *
     * @param boolean $exception
     * @return Holiday
     */
    public function setException($exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * Get exception
     *
     * @return boolean 
     */
    public function getException()
    {
        return $this->exception;
    }
}
