<?php

namespace My\AppBundle\Model;

/**
 * Setting
 */
abstract class Setting
{
    /**
     * @var string
     */
    protected $_key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $type;


    /**
     * Set _key
     *
     * @param string $key
     * @return Setting
     */
    public function setKey($key)
    {
        $this->_key = $key;

        return $this;
    }

    /**
     * Get _key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Setting
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
