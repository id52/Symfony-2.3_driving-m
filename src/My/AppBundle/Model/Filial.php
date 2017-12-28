<?php

namespace My\AppBundle\Model;

/**
 * Filial
 */
abstract class Filial
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
    protected $url;

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
     * @var array
     */
    protected $coords;

    /**
     * @var string
     */
    protected $address_geo;

    /**
     * @var string
     */
    protected $map_code;

    /**
     * @var string
     */
    protected $counters_code;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var boolean
     */
    protected $active_auth;

    /**
     * @var boolean
     */
    protected $_show;

    /**
     * @var boolean
     */
    protected $show_auth;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;

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
     * @return Filial
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
     * Set url
     *
     * @param string $url
     * @return Filial
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Filial
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
     * @return Filial
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
     * Set work_time
     *
     * @param string $workTime
     * @return Filial
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
     * @return Filial
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
     * @return Filial
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
     * Set coords
     *
     * @param array $coords
     * @return Filial
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get coords
     *
     * @return array 
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set address_geo
     *
     * @param string $addressGeo
     * @return Filial
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
     * @return Filial
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
     * Set counters_code
     *
     * @param string $countersCode
     * @return Filial
     */
    public function setCountersCode($countersCode)
    {
        $this->counters_code = $countersCode;

        return $this;
    }

    /**
     * Get counters_code
     *
     * @return string 
     */
    public function getCountersCode()
    {
        return $this->counters_code;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Filial
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
     * Set active_auth
     *
     * @param boolean $activeAuth
     * @return Filial
     */
    public function setActiveAuth($activeAuth)
    {
        $this->active_auth = $activeAuth;

        return $this;
    }

    /**
     * Get active_auth
     *
     * @return boolean 
     */
    public function getActiveAuth()
    {
        return $this->active_auth;
    }

    /**
     * Set _show
     *
     * @param boolean $show
     * @return Filial
     */
    public function setShow($show)
    {
        $this->_show = $show;

        return $this;
    }

    /**
     * Get _show
     *
     * @return boolean 
     */
    public function getShow()
    {
        return $this->_show;
    }

    /**
     * Set show_auth
     *
     * @param boolean $showAuth
     * @return Filial
     */
    public function setShowAuth($showAuth)
    {
        $this->show_auth = $showAuth;

        return $this;
    }

    /**
     * Get show_auth
     *
     * @return boolean 
     */
    public function getShowAuth()
    {
        return $this->show_auth;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Filial
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
     * @return Filial
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
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return Filial
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
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return Filial
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
