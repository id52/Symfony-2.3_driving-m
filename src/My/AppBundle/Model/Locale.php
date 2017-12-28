<?php

namespace My\AppBundle\Model;

/**
 * Locale
 */
abstract class Locale
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $questions_i18n;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions_i18n = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set code
     *
     * @param string $code
     * @return Locale
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add questions_i18n
     *
     * @param \My\AppBundle\Entity\QuestionI18N $questionsI18n
     * @return Locale
     */
    public function addQuestionsI18n(\My\AppBundle\Entity\QuestionI18N $questionsI18n)
    {
        $this->questions_i18n[] = $questionsI18n;

        return $this;
    }

    /**
     * Remove questions_i18n
     *
     * @param \My\AppBundle\Entity\QuestionI18N $questionsI18n
     */
    public function removeQuestionsI18n(\My\AppBundle\Entity\QuestionI18N $questionsI18n)
    {
        $this->questions_i18n->removeElement($questionsI18n);
    }

    /**
     * Get questions_i18n
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionsI18n()
    {
        return $this->questions_i18n;
    }
}
