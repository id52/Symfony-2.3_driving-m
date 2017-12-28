<?php

namespace My\AppBundle\Model;

/**
 * Image
 */
abstract class Image
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \My\AppBundle\Entity\Question
     */
    protected $question;

    /**
     * @var \My\AppBundle\Entity\Category
     */
    protected $category;

    /**
     * @var \My\AppBundle\Entity\Subject
     */
    protected $subject;

    /**
     * @var \My\AppBundle\Entity\Offer
     */
    protected $offer;

    /**
     * @var \My\AppBundle\Entity\OfferStudent
     */
    protected $offer_student;

    /**
     * @var \My\AppBundle\Entity\Filial
     */
    protected $filial;

    /**
     * @var \My\AppBundle\Entity\Site
     */
    protected $site;

    /**
     * @var \My\AppBundle\Entity\FlashBlockItem
     */
    protected $flash_block_item;

    /**
     * @var \My\AppBundle\Entity\Review
     */
    protected $review;

    /**
     * @var \My\AppBundle\Entity\PassFilial
     */
    protected $pass_filial;

    /**
     * @var \My\AppBundle\Entity\HowWork
     */
    protected $how_work;


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
     * Set file
     *
     * @param string $file
     * @return Image
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Image
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
     * Set question
     *
     * @param \My\AppBundle\Entity\Question $question
     * @return Image
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

    /**
     * Set category
     *
     * @param \My\AppBundle\Entity\Category $category
     * @return Image
     */
    public function setCategory(\My\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \My\AppBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set subject
     *
     * @param \My\AppBundle\Entity\Subject $subject
     * @return Image
     */
    public function setSubject(\My\AppBundle\Entity\Subject $subject = null)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return \My\AppBundle\Entity\Subject 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set offer
     *
     * @param \My\AppBundle\Entity\Offer $offer
     * @return Image
     */
    public function setOffer(\My\AppBundle\Entity\Offer $offer = null)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return \My\AppBundle\Entity\Offer 
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Set offer_student
     *
     * @param \My\AppBundle\Entity\OfferStudent $offerStudent
     * @return Image
     */
    public function setOfferStudent(\My\AppBundle\Entity\OfferStudent $offerStudent = null)
    {
        $this->offer_student = $offerStudent;

        return $this;
    }

    /**
     * Get offer_student
     *
     * @return \My\AppBundle\Entity\OfferStudent 
     */
    public function getOfferStudent()
    {
        return $this->offer_student;
    }

    /**
     * Set filial
     *
     * @param \My\AppBundle\Entity\Filial $filial
     * @return Image
     */
    public function setFilial(\My\AppBundle\Entity\Filial $filial = null)
    {
        $this->filial = $filial;

        return $this;
    }

    /**
     * Get filial
     *
     * @return \My\AppBundle\Entity\Filial 
     */
    public function getFilial()
    {
        return $this->filial;
    }

    /**
     * Set site
     *
     * @param \My\AppBundle\Entity\Site $site
     * @return Image
     */
    public function setSite(\My\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \My\AppBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set flash_block_item
     *
     * @param \My\AppBundle\Entity\FlashBlockItem $flashBlockItem
     * @return Image
     */
    public function setFlashBlockItem(\My\AppBundle\Entity\FlashBlockItem $flashBlockItem = null)
    {
        $this->flash_block_item = $flashBlockItem;

        return $this;
    }

    /**
     * Get flash_block_item
     *
     * @return \My\AppBundle\Entity\FlashBlockItem 
     */
    public function getFlashBlockItem()
    {
        return $this->flash_block_item;
    }

    /**
     * Set review
     *
     * @param \My\AppBundle\Entity\Review $review
     * @return Image
     */
    public function setReview(\My\AppBundle\Entity\Review $review = null)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return \My\AppBundle\Entity\Review 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set pass_filial
     *
     * @param \My\AppBundle\Entity\PassFilial $passFilial
     * @return Image
     */
    public function setPassFilial(\My\AppBundle\Entity\PassFilial $passFilial = null)
    {
        $this->pass_filial = $passFilial;

        return $this;
    }

    /**
     * Get pass_filial
     *
     * @return \My\AppBundle\Entity\PassFilial 
     */
    public function getPassFilial()
    {
        return $this->pass_filial;
    }

    /**
     * Set how_work
     *
     * @param \My\AppBundle\Entity\HowWork $howWork
     * @return Image
     */
    public function setHowWork(\My\AppBundle\Entity\HowWork $howWork = null)
    {
        $this->how_work = $howWork;

        return $this;
    }

    /**
     * Get how_work
     *
     * @return \My\AppBundle\Entity\HowWork 
     */
    public function getHowWork()
    {
        return $this->how_work;
    }
    /**
     * ORM\prePersist
     */
    public function preUpload()
    {
        // Add your code here
    }

    /**
     * ORM\postPersist
     */
    public function upload()
    {
        // Add your code here
    }

    /**
     * ORM\postUpdate
     */
    public function removeUploadCache()
    {
        // Add your code here
    }

    /**
     * ORM\postRemove
     */
    public function removeUpload()
    {
        // Add your code here
    }
}
