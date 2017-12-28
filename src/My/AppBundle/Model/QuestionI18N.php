<?php

namespace My\AppBundle\Model;

/**
 * QuestionI18N
 */
abstract class QuestionI18N
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
    protected $description;

    /**
     * @var array
     */
    protected $answers;

    /**
     * @var \My\AppBundle\Entity\Locale
     */
    protected $locale;

    /**
     * @var \My\AppBundle\Entity\Question
     */
    protected $question;


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
     * @return QuestionI18N
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
     * @return QuestionI18N
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
     * @return QuestionI18N
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
     * Set locale
     *
     * @param \My\AppBundle\Entity\Locale $locale
     * @return QuestionI18N
     */
    public function setLocale(\My\AppBundle\Entity\Locale $locale = null)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return \My\AppBundle\Entity\Locale 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set question
     *
     * @param \My\AppBundle\Entity\Question $question
     * @return QuestionI18N
     */
    public function setQuestion(\My\AppBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \My\AppBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
