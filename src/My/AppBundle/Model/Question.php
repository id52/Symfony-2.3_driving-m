<?php

namespace My\AppBundle\Model;

/**
 * Question
 */
abstract class Question
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $is_pdd;

    /**
     * @var string
     */
    protected $num;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $answers;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $questions_i18n;

    /**
     * @var \My\AppBundle\Entity\Theme
     */
    protected $theme;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $versions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions_i18n = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set is_pdd
     *
     * @param boolean $isPdd
     * @return Question
     */
    public function setIsPdd($isPdd)
    {
        $this->is_pdd = $isPdd;

        return $this;
    }

    /**
     * Get is_pdd
     *
     * @return boolean 
     */
    public function getIsPdd()
    {
        return $this->is_pdd;
    }

    /**
     * Set num
     *
     * @param string $num
     * @return Question
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Question
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
     * Set description
     *
     * @param string $description
     * @return Question
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set answers
     *
     * @param array $answers
     * @return Question
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
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return Question
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
     * Add questions_i18n
     *
     * @param \My\AppBundle\Model\QuestionI18N $questionsI18n
     * @return Question
     */
    public function addQuestionsI18n(\My\AppBundle\Model\QuestionI18N $questionsI18n)
    {
        $this->questions_i18n[] = $questionsI18n;

        return $this;
    }

    /**
     * Remove questions_i18n
     *
     * @param \My\AppBundle\Model\QuestionI18N $questionsI18n
     */
    public function removeQuestionsI18n(\My\AppBundle\Model\QuestionI18N $questionsI18n)
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

    /**
     * Set theme
     *
     * @param \My\AppBundle\Entity\Theme $theme
     * @return Question
     */
    public function setTheme(\My\AppBundle\Entity\Theme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \My\AppBundle\Entity\Theme 
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Add versions
     *
     * @param \My\AppBundle\Entity\TrainingVersion $versions
     * @return Question
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
