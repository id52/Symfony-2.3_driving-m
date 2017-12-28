<?php

namespace My\AppBundle\Model;

/**
 * Slice
 */
abstract class Slice
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \My\AppBundle\Entity\Theme
     */
    protected $after_theme;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $versions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set after_theme
     *
     * @param \My\AppBundle\Entity\Theme $afterTheme
     * @return Slice
     */
    public function setAfterTheme(\My\AppBundle\Entity\Theme $afterTheme = null)
    {
        $this->after_theme = $afterTheme;

        return $this;
    }

    /**
     * Get after_theme
     *
     * @return \My\AppBundle\Entity\Theme 
     */
    public function getAfterTheme()
    {
        return $this->after_theme;
    }

    /**
     * Add logs
     *
     * @param \My\AppBundle\Model\SliceLog $logs
     * @return Slice
     */
    public function addLog(\My\AppBundle\Model\SliceLog $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param \My\AppBundle\Model\SliceLog $logs
     */
    public function removeLog(\My\AppBundle\Model\SliceLog $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $versions
     * @return Slice
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
