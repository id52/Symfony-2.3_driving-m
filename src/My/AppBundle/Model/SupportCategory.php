<?php

namespace My\AppBundle\Model;

/**
 * SupportCategory
 */
abstract class SupportCategory
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     \* @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $t_versions;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $dialogs;

    /**
     * @var \My\AppBundle\Model\SupportCategory
     */
    protected $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $moderators;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dialogs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->moderators = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return SupportCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     \* @param string $type
     * @return SupportCategory
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     \* @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set t_versions
     *
     * @param string $tVersions
     * @return SupportCategory
     */
    public function setTVersions($tVersions)
    {
        $this->t_versions = $tVersions;

        return $this;
    }

    /**
     * Get t_versions
     *
     * @return string 
     */
    public function getTVersions()
    {
        return $this->t_versions;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return SupportCategory
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
     * Add children
     *
     * @param \My\AppBundle\Model\SupportCategory $children
     * @return SupportCategory
     */
    public function addChild(\My\AppBundle\Model\SupportCategory $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \My\AppBundle\Model\SupportCategory $children
     */
    public function removeChild(\My\AppBundle\Model\SupportCategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $dialogs
     * @return SupportCategory
     */
    public function addDialog(\My\AppBundle\Entity\SupportDialog $dialogs)
    {
        $this->dialogs[] = $dialogs;

        return $this;
    }

    /**
     * Remove dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $dialogs
     */
    public function removeDialog(\My\AppBundle\Entity\SupportDialog $dialogs)
    {
        $this->dialogs->removeElement($dialogs);
    }

    /**
     * Get dialogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDialogs()
    {
        return $this->dialogs;
    }

    /**
     * Set parent
     *
     * @param \My\AppBundle\Model\SupportCategory $parent
     * @return SupportCategory
     */
    public function setParent(\My\AppBundle\Model\SupportCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \My\AppBundle\Model\SupportCategory 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add moderators
     *
     * @param \My\AppBundle\Entity\User $moderators
     * @return SupportCategory
     */
    public function addModerator(\My\AppBundle\Entity\User $moderators)
    {
        $this->moderators[] = $moderators;

        return $this;
    }

    /**
     * Remove moderators
     *
     * @param \My\AppBundle\Entity\User $moderators
     */
    public function removeModerator(\My\AppBundle\Entity\User $moderators)
    {
        $this->moderators->removeElement($moderators);
    }

    /**
     * Get moderators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModerators()
    {
        return $this->moderators;
    }
}
