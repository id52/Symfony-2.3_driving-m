<?php

namespace My\AppBundle\Model;

/**
 * Office
 */
abstract class Office
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
    protected $address;

    /**
     * @var string
     */
    protected $station;

    /**
     * @var string
     */
    protected $address_desc;

    /**
     * @var string
     */
    protected $work_time;

    /**
     * @var array
     */
    protected $phones;

    /**
     * @var array
     */
    protected $emails;

    /**
     * @var string
     */
    protected $address_geo;

    /**
     * @var string
     */
    protected $map_code;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;


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
     * @return Office
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
     * Set address
     *
     * @param string $address
     * @return Office
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set station
     *
     * @param string $station
     * @return Office
     */
    public function setStation($station)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get station
     *
     * @return string 
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set address_desc
     *
     * @param string $addressDesc
     * @return Office
     */
    public function setAddressDesc($addressDesc)
    {
        $this->address_desc = $addressDesc;

        return $this;
    }

    /**
     * Get address_desc
     *
     * @return string 
     */
    public function getAddressDesc()
    {
        return $this->address_desc;
    }

    /**
     * Set work_time
     *
     * @param string $workTime
     * @return Office
     */
    public function setWorkTime($workTime)
    {
        $this->work_time = $workTime;

        return $this;
    }

    /**
     * Get work_time
     *
     * @return string 
     */
    public function getWorkTime()
    {
        return $this->work_time;
    }

    /**
     * Set phones
     *
     * @param array $phones
     * @return Office
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return array 
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set emails
     *
     * @param array $emails
     * @return Office
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return array 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set address_geo
     *
     * @param string $addressGeo
     * @return Office
     */
    public function setAddressGeo($addressGeo)
    {
        $this->address_geo = $addressGeo;

        return $this;
    }

    /**
     * Get address_geo
     *
     * @return string 
     */
    public function getAddressGeo()
    {
        return $this->address_geo;
    }

    /**
     * Set map_code
     *
     * @param string $mapCode
     * @return Office
     */
    public function setMapCode($mapCode)
    {
        $this->map_code = $mapCode;

        return $this;
    }

    /**
     * Get map_code
     *
     * @return string 
     */
    public function getMapCode()
    {
        return $this->map_code;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Office
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Office
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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Office
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
     * @return Office
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
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return Office
     */
    public function setRegion(\My\AppBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \My\AppBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }
}
