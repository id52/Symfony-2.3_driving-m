<?php

namespace My\AppBundle\Model;

/**
 * User
 */
abstract class User extends \FOS\UserBundle\Entity\User
{
    /**
     * @var string
     */
    protected $certificate;

    /**
     * @var string
     */
    protected $last_name;

    /**
     * @var string
     */
    protected $first_name;

    /**
     * @var string
     */
    protected $patronymic;

    /**
     * @var string
     */
    protected $photo;

    /**
     * @var array
     */
    protected $photo_coords;

    /**
     \* @var string
     */
    protected $sex;

    /**
     * @var \DateTime
     */
    protected $birthday;

    /**
     * @var string
     */
    protected $birth_country;

    /**
     * @var string
     */
    protected $birth_region;

    /**
     * @var string
     */
    protected $birth_city;

    /**
     * @var boolean
     */
    protected $foreign_passport;

    /**
     * @var string
     */
    protected $foreign_passport_number;

    /**
     * @var string
     */
    protected $passport_number;

    /**
     * @var string
     */
    protected $passport_rovd;

    /**
     * @var string
     */
    protected $passport_rovd_number;

    /**
     * @var \DateTime
     */
    protected $passport_rovd_date;

    /**
     * @var boolean
     */
    protected $not_registration;

    /**
     * @var string
     */
    protected $registration_country;

    /**
     * @var string
     */
    protected $registration_region;

    /**
     * @var string
     */
    protected $registration_city;

    /**
     * @var string
     */
    protected $registration_street;

    /**
     * @var string
     */
    protected $registration_house;

    /**
     * @var string
     */
    protected $registration_stroenie;

    /**
     * @var string
     */
    protected $registration_korpus;

    /**
     * @var string
     */
    protected $registration_apartament;

    /**
     * @var string
     */
    protected $place_country;

    /**
     * @var string
     */
    protected $place_region;

    /**
     * @var string
     */
    protected $place_city;

    /**
     * @var string
     */
    protected $place_street;

    /**
     * @var string
     */
    protected $place_house;

    /**
     * @var string
     */
    protected $place_stroenie;

    /**
     * @var string
     */
    protected $place_korpus;

    /**
     * @var string
     */
    protected $place_apartament;

    /**
     * @var string
     */
    protected $work_place;

    /**
     * @var string
     */
    protected $work_position;

    /**
     * @var string
     */
    protected $phone_home;

    /**
     * @var string
     */
    protected $phone_mobile;

    /**
     \* @var string
     */
    protected $phone_mobile_status;

    /**
     * @var string
     */
    protected $phone_mobile_code;

    /**
     * @var integer
     */
    protected $notifies_cnt;

    /**
     * @var \DateTime
     */
    protected $paid_notified_at;

    /**
     * @var \DateTime
     */
    protected $payment_1_paid;

    /**
     * @var boolean
     */
    protected $payment_1_paid_not_notify;

    /**
     * @var \DateTime
     */
    protected $payment_2_paid;

    /**
     * @var boolean
     */
    protected $payment_2_paid_not_notify;

    /**
     * @var \DateTime
     */
    protected $payment_3_paid;

    /**
     * @var boolean
     */
    protected $payment_3_paid_not_notify;

    /**
     * @var boolean
     */
    protected $promo_used;

    /**
     * @var array
     */
    protected $white_ips;

    /**
     * @var boolean
     */
    protected $moderated;

    /**
     * @var integer
     */
    protected $paradox_id;

    /**
     * @var boolean
     */
    protected $discount_2_notify_first;

    /**
     * @var boolean
     */
    protected $discount_2_notify_second;

    /**
     * @var boolean
     */
    protected $mailing;

    /**
     * @var boolean
     */
    protected $not_all_mailing;

    /**
     * @var boolean
     */
    protected $overdue_unsubscribed;

    /**
     * @var boolean
     */
    protected $offline;

    /**
     * @var array
     */
    protected $reg_info;

    /**
     * @var array
     */
    protected $popup_info;

    /**
     * @var array
     */
    protected $pass_info;

    /**
     * @var boolean
     */
    protected $close_final_exam;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \My\AppBundle\Entity\Notify
     */
    protected $required_notify;

    /**
     * @var \My\AppBundle\Entity\SupportCategory
     */
    protected $teacher;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $themes_tests_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $slices_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $exams_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $final_exams_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $notifies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $tests_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $tests_knowledge_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $old_mobile_phones;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $payment_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $payment_revert_logs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $support_dialogs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $last_support_dialogs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $support_messages;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $user_confirmation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $reviews;

    /**
     * @var \My\AppBundle\Entity\Category
     */
    protected $category;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;

    /**
     * @var \My\AppBundle\Entity\RegionPlace
     */
    protected $region_place;

    /**
     * @var \My\AppBundle\Entity\Webgroup
     */
    protected $webgroup;

    /**
     * @var \My\AppBundle\Entity\Offer
     */
    protected $offer;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $closed_subjects;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $read_themes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $moderated_support_categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $read_infos;


    /**
     * Set certificate
     *
     * @param string $certificate
     * @return User
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * Get certificate
     *
     * @return string 
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set patronymic
     *
     * @param string $patronymic
     * @return User
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    /**
     * Get patronymic
     *
     * @return string 
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo_coords
     *
     * @param array $photoCoords
     * @return User
     */
    public function setPhotoCoords($photoCoords)
    {
        $this->photo_coords = $photoCoords;

        return $this;
    }

    /**
     * Get photo_coords
     *
     * @return array 
     */
    public function getPhotoCoords()
    {
        return $this->photo_coords;
    }

    /**
     * Set sex
     *
     \* @param string $sex
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     \* @return string 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set birth_country
     *
     * @param string $birthCountry
     * @return User
     */
    public function setBirthCountry($birthCountry)
    {
        $this->birth_country = $birthCountry;

        return $this;
    }

    /**
     * Get birth_country
     *
     * @return string 
     */
    public function getBirthCountry()
    {
        return $this->birth_country;
    }

    /**
     * Set birth_region
     *
     * @param string $birthRegion
     * @return User
     */
    public function setBirthRegion($birthRegion)
    {
        $this->birth_region = $birthRegion;

        return $this;
    }

    /**
     * Get birth_region
     *
     * @return string 
     */
    public function getBirthRegion()
    {
        return $this->birth_region;
    }

    /**
     * Set birth_city
     *
     * @param string $birthCity
     * @return User
     */
    public function setBirthCity($birthCity)
    {
        $this->birth_city = $birthCity;

        return $this;
    }

    /**
     * Get birth_city
     *
     * @return string 
     */
    public function getBirthCity()
    {
        return $this->birth_city;
    }

    /**
     * Set foreign_passport
     *
     * @param boolean $foreignPassport
     * @return User
     */
    public function setForeignPassport($foreignPassport)
    {
        $this->foreign_passport = $foreignPassport;

        return $this;
    }

    /**
     * Get foreign_passport
     *
     * @return boolean 
     */
    public function getForeignPassport()
    {
        return $this->foreign_passport;
    }

    /**
     * Set foreign_passport_number
     *
     * @param string $foreignPassportNumber
     * @return User
     */
    public function setForeignPassportNumber($foreignPassportNumber)
    {
        $this->foreign_passport_number = $foreignPassportNumber;

        return $this;
    }

    /**
     * Get foreign_passport_number
     *
     * @return string 
     */
    public function getForeignPassportNumber()
    {
        return $this->foreign_passport_number;
    }

    /**
     * Set passport_number
     *
     * @param string $passportNumber
     * @return User
     */
    public function setPassportNumber($passportNumber)
    {
        $this->passport_number = $passportNumber;

        return $this;
    }

    /**
     * Get passport_number
     *
     * @return string 
     */
    public function getPassportNumber()
    {
        return $this->passport_number;
    }

    /**
     * Set passport_rovd
     *
     * @param string $passportRovd
     * @return User
     */
    public function setPassportRovd($passportRovd)
    {
        $this->passport_rovd = $passportRovd;

        return $this;
    }

    /**
     * Get passport_rovd
     *
     * @return string 
     */
    public function getPassportRovd()
    {
        return $this->passport_rovd;
    }

    /**
     * Set passport_rovd_number
     *
     * @param string $passportRovdNumber
     * @return User
     */
    public function setPassportRovdNumber($passportRovdNumber)
    {
        $this->passport_rovd_number = $passportRovdNumber;

        return $this;
    }

    /**
     * Get passport_rovd_number
     *
     * @return string 
     */
    public function getPassportRovdNumber()
    {
        return $this->passport_rovd_number;
    }

    /**
     * Set passport_rovd_date
     *
     * @param \DateTime $passportRovdDate
     * @return User
     */
    public function setPassportRovdDate($passportRovdDate)
    {
        $this->passport_rovd_date = $passportRovdDate;

        return $this;
    }

    /**
     * Get passport_rovd_date
     *
     * @return \DateTime 
     */
    public function getPassportRovdDate()
    {
        return $this->passport_rovd_date;
    }

    /**
     * Set not_registration
     *
     * @param boolean $notRegistration
     * @return User
     */
    public function setNotRegistration($notRegistration)
    {
        $this->not_registration = $notRegistration;

        return $this;
    }

    /**
     * Get not_registration
     *
     * @return boolean 
     */
    public function getNotRegistration()
    {
        return $this->not_registration;
    }

    /**
     * Set registration_country
     *
     * @param string $registrationCountry
     * @return User
     */
    public function setRegistrationCountry($registrationCountry)
    {
        $this->registration_country = $registrationCountry;

        return $this;
    }

    /**
     * Get registration_country
     *
     * @return string 
     */
    public function getRegistrationCountry()
    {
        return $this->registration_country;
    }

    /**
     * Set registration_region
     *
     * @param string $registrationRegion
     * @return User
     */
    public function setRegistrationRegion($registrationRegion)
    {
        $this->registration_region = $registrationRegion;

        return $this;
    }

    /**
     * Get registration_region
     *
     * @return string 
     */
    public function getRegistrationRegion()
    {
        return $this->registration_region;
    }

    /**
     * Set registration_city
     *
     * @param string $registrationCity
     * @return User
     */
    public function setRegistrationCity($registrationCity)
    {
        $this->registration_city = $registrationCity;

        return $this;
    }

    /**
     * Get registration_city
     *
     * @return string 
     */
    public function getRegistrationCity()
    {
        return $this->registration_city;
    }

    /**
     * Set registration_street
     *
     * @param string $registrationStreet
     * @return User
     */
    public function setRegistrationStreet($registrationStreet)
    {
        $this->registration_street = $registrationStreet;

        return $this;
    }

    /**
     * Get registration_street
     *
     * @return string 
     */
    public function getRegistrationStreet()
    {
        return $this->registration_street;
    }

    /**
     * Set registration_house
     *
     * @param string $registrationHouse
     * @return User
     */
    public function setRegistrationHouse($registrationHouse)
    {
        $this->registration_house = $registrationHouse;

        return $this;
    }

    /**
     * Get registration_house
     *
     * @return string 
     */
    public function getRegistrationHouse()
    {
        return $this->registration_house;
    }

    /**
     * Set registration_stroenie
     *
     * @param string $registrationStroenie
     * @return User
     */
    public function setRegistrationStroenie($registrationStroenie)
    {
        $this->registration_stroenie = $registrationStroenie;

        return $this;
    }

    /**
     * Get registration_stroenie
     *
     * @return string 
     */
    public function getRegistrationStroenie()
    {
        return $this->registration_stroenie;
    }

    /**
     * Set registration_korpus
     *
     * @param string $registrationKorpus
     * @return User
     */
    public function setRegistrationKorpus($registrationKorpus)
    {
        $this->registration_korpus = $registrationKorpus;

        return $this;
    }

    /**
     * Get registration_korpus
     *
     * @return string 
     */
    public function getRegistrationKorpus()
    {
        return $this->registration_korpus;
    }

    /**
     * Set registration_apartament
     *
     * @param string $registrationApartament
     * @return User
     */
    public function setRegistrationApartament($registrationApartament)
    {
        $this->registration_apartament = $registrationApartament;

        return $this;
    }

    /**
     * Get registration_apartament
     *
     * @return string 
     */
    public function getRegistrationApartament()
    {
        return $this->registration_apartament;
    }

    /**
     * Set place_country
     *
     * @param string $placeCountry
     * @return User
     */
    public function setPlaceCountry($placeCountry)
    {
        $this->place_country = $placeCountry;

        return $this;
    }

    /**
     * Get place_country
     *
     * @return string 
     */
    public function getPlaceCountry()
    {
        return $this->place_country;
    }

    /**
     * Set place_region
     *
     * @param string $placeRegion
     * @return User
     */
    public function setPlaceRegion($placeRegion)
    {
        $this->place_region = $placeRegion;

        return $this;
    }

    /**
     * Get place_region
     *
     * @return string 
     */
    public function getPlaceRegion()
    {
        return $this->place_region;
    }

    /**
     * Set place_city
     *
     * @param string $placeCity
     * @return User
     */
    public function setPlaceCity($placeCity)
    {
        $this->place_city = $placeCity;

        return $this;
    }

    /**
     * Get place_city
     *
     * @return string 
     */
    public function getPlaceCity()
    {
        return $this->place_city;
    }

    /**
     * Set place_street
     *
     * @param string $placeStreet
     * @return User
     */
    public function setPlaceStreet($placeStreet)
    {
        $this->place_street = $placeStreet;

        return $this;
    }

    /**
     * Get place_street
     *
     * @return string 
     */
    public function getPlaceStreet()
    {
        return $this->place_street;
    }

    /**
     * Set place_house
     *
     * @param string $placeHouse
     * @return User
     */
    public function setPlaceHouse($placeHouse)
    {
        $this->place_house = $placeHouse;

        return $this;
    }

    /**
     * Get place_house
     *
     * @return string 
     */
    public function getPlaceHouse()
    {
        return $this->place_house;
    }

    /**
     * Set place_stroenie
     *
     * @param string $placeStroenie
     * @return User
     */
    public function setPlaceStroenie($placeStroenie)
    {
        $this->place_stroenie = $placeStroenie;

        return $this;
    }

    /**
     * Get place_stroenie
     *
     * @return string 
     */
    public function getPlaceStroenie()
    {
        return $this->place_stroenie;
    }

    /**
     * Set place_korpus
     *
     * @param string $placeKorpus
     * @return User
     */
    public function setPlaceKorpus($placeKorpus)
    {
        $this->place_korpus = $placeKorpus;

        return $this;
    }

    /**
     * Get place_korpus
     *
     * @return string 
     */
    public function getPlaceKorpus()
    {
        return $this->place_korpus;
    }

    /**
     * Set place_apartament
     *
     * @param string $placeApartament
     * @return User
     */
    public function setPlaceApartament($placeApartament)
    {
        $this->place_apartament = $placeApartament;

        return $this;
    }

    /**
     * Get place_apartament
     *
     * @return string 
     */
    public function getPlaceApartament()
    {
        return $this->place_apartament;
    }

    /**
     * Set work_place
     *
     * @param string $workPlace
     * @return User
     */
    public function setWorkPlace($workPlace)
    {
        $this->work_place = $workPlace;

        return $this;
    }

    /**
     * Get work_place
     *
     * @return string 
     */
    public function getWorkPlace()
    {
        return $this->work_place;
    }

    /**
     * Set work_position
     *
     * @param string $workPosition
     * @return User
     */
    public function setWorkPosition($workPosition)
    {
        $this->work_position = $workPosition;

        return $this;
    }

    /**
     * Get work_position
     *
     * @return string 
     */
    public function getWorkPosition()
    {
        return $this->work_position;
    }

    /**
     * Set phone_home
     *
     * @param string $phoneHome
     * @return User
     */
    public function setPhoneHome($phoneHome)
    {
        $this->phone_home = $phoneHome;

        return $this;
    }

    /**
     * Get phone_home
     *
     * @return string 
     */
    public function getPhoneHome()
    {
        return $this->phone_home;
    }

    /**
     * Set phone_mobile
     *
     * @param string $phoneMobile
     * @return User
     */
    public function setPhoneMobile($phoneMobile)
    {
        $this->phone_mobile = $phoneMobile;

        return $this;
    }

    /**
     * Get phone_mobile
     *
     * @return string 
     */
    public function getPhoneMobile()
    {
        return $this->phone_mobile;
    }

    /**
     * Set phone_mobile_status
     *
     \* @param string $phoneMobileStatus
     * @return User
     */
    public function setPhoneMobileStatus($phoneMobileStatus)
    {
        $this->phone_mobile_status = $phoneMobileStatus;

        return $this;
    }

    /**
     * Get phone_mobile_status
     *
     \* @return string 
     */
    public function getPhoneMobileStatus()
    {
        return $this->phone_mobile_status;
    }

    /**
     * Set phone_mobile_code
     *
     * @param string $phoneMobileCode
     * @return User
     */
    public function setPhoneMobileCode($phoneMobileCode)
    {
        $this->phone_mobile_code = $phoneMobileCode;

        return $this;
    }

    /**
     * Get phone_mobile_code
     *
     * @return string 
     */
    public function getPhoneMobileCode()
    {
        return $this->phone_mobile_code;
    }

    /**
     * Set notifies_cnt
     *
     * @param integer $notifiesCnt
     * @return User
     */
    public function setNotifiesCnt($notifiesCnt)
    {
        $this->notifies_cnt = $notifiesCnt;

        return $this;
    }

    /**
     * Get notifies_cnt
     *
     * @return integer 
     */
    public function getNotifiesCnt()
    {
        return $this->notifies_cnt;
    }

    /**
     * Set paid_notified_at
     *
     * @param \DateTime $paidNotifiedAt
     * @return User
     */
    public function setPaidNotifiedAt($paidNotifiedAt)
    {
        $this->paid_notified_at = $paidNotifiedAt;

        return $this;
    }

    /**
     * Get paid_notified_at
     *
     * @return \DateTime 
     */
    public function getPaidNotifiedAt()
    {
        return $this->paid_notified_at;
    }

    /**
     * Set payment_1_paid
     *
     * @param \DateTime $payment1Paid
     * @return User
     */
    public function setPayment1Paid($payment1Paid)
    {
        $this->payment_1_paid = $payment1Paid;

        return $this;
    }

    /**
     * Get payment_1_paid
     *
     * @return \DateTime 
     */
    public function getPayment1Paid()
    {
        return $this->payment_1_paid;
    }

    /**
     * Set payment_1_paid_not_notify
     *
     * @param boolean $payment1PaidNotNotify
     * @return User
     */
    public function setPayment1PaidNotNotify($payment1PaidNotNotify)
    {
        $this->payment_1_paid_not_notify = $payment1PaidNotNotify;

        return $this;
    }

    /**
     * Get payment_1_paid_not_notify
     *
     * @return boolean 
     */
    public function getPayment1PaidNotNotify()
    {
        return $this->payment_1_paid_not_notify;
    }

    /**
     * Set payment_2_paid
     *
     * @param \DateTime $payment2Paid
     * @return User
     */
    public function setPayment2Paid($payment2Paid)
    {
        $this->payment_2_paid = $payment2Paid;

        return $this;
    }

    /**
     * Get payment_2_paid
     *
     * @return \DateTime 
     */
    public function getPayment2Paid()
    {
        return $this->payment_2_paid;
    }

    /**
     * Set payment_2_paid_not_notify
     *
     * @param boolean $payment2PaidNotNotify
     * @return User
     */
    public function setPayment2PaidNotNotify($payment2PaidNotNotify)
    {
        $this->payment_2_paid_not_notify = $payment2PaidNotNotify;

        return $this;
    }

    /**
     * Get payment_2_paid_not_notify
     *
     * @return boolean 
     */
    public function getPayment2PaidNotNotify()
    {
        return $this->payment_2_paid_not_notify;
    }

    /**
     * Set payment_3_paid
     *
     * @param \DateTime $payment3Paid
     * @return User
     */
    public function setPayment3Paid($payment3Paid)
    {
        $this->payment_3_paid = $payment3Paid;

        return $this;
    }

    /**
     * Get payment_3_paid
     *
     * @return \DateTime 
     */
    public function getPayment3Paid()
    {
        return $this->payment_3_paid;
    }

    /**
     * Set payment_3_paid_not_notify
     *
     * @param boolean $payment3PaidNotNotify
     * @return User
     */
    public function setPayment3PaidNotNotify($payment3PaidNotNotify)
    {
        $this->payment_3_paid_not_notify = $payment3PaidNotNotify;

        return $this;
    }

    /**
     * Get payment_3_paid_not_notify
     *
     * @return boolean 
     */
    public function getPayment3PaidNotNotify()
    {
        return $this->payment_3_paid_not_notify;
    }

    /**
     * Set promo_used
     *
     * @param boolean $promoUsed
     * @return User
     */
    public function setPromoUsed($promoUsed)
    {
        $this->promo_used = $promoUsed;

        return $this;
    }

    /**
     * Get promo_used
     *
     * @return boolean 
     */
    public function getPromoUsed()
    {
        return $this->promo_used;
    }

    /**
     * Set white_ips
     *
     * @param array $whiteIps
     * @return User
     */
    public function setWhiteIps($whiteIps)
    {
        $this->white_ips = $whiteIps;

        return $this;
    }

    /**
     * Get white_ips
     *
     * @return array 
     */
    public function getWhiteIps()
    {
        return $this->white_ips;
    }

    /**
     * Set moderated
     *
     * @param boolean $moderated
     * @return User
     */
    public function setModerated($moderated)
    {
        $this->moderated = $moderated;

        return $this;
    }

    /**
     * Get moderated
     *
     * @return boolean 
     */
    public function getModerated()
    {
        return $this->moderated;
    }

    /**
     * Set paradox_id
     *
     * @param integer $paradoxId
     * @return User
     */
    public function setParadoxId($paradoxId)
    {
        $this->paradox_id = $paradoxId;

        return $this;
    }

    /**
     * Get paradox_id
     *
     * @return integer 
     */
    public function getParadoxId()
    {
        return $this->paradox_id;
    }

    /**
     * Set discount_2_notify_first
     *
     * @param boolean $discount2NotifyFirst
     * @return User
     */
    public function setDiscount2NotifyFirst($discount2NotifyFirst)
    {
        $this->discount_2_notify_first = $discount2NotifyFirst;

        return $this;
    }

    /**
     * Get discount_2_notify_first
     *
     * @return boolean 
     */
    public function getDiscount2NotifyFirst()
    {
        return $this->discount_2_notify_first;
    }

    /**
     * Set discount_2_notify_second
     *
     * @param boolean $discount2NotifySecond
     * @return User
     */
    public function setDiscount2NotifySecond($discount2NotifySecond)
    {
        $this->discount_2_notify_second = $discount2NotifySecond;

        return $this;
    }

    /**
     * Get discount_2_notify_second
     *
     * @return boolean 
     */
    public function getDiscount2NotifySecond()
    {
        return $this->discount_2_notify_second;
    }

    /**
     * Set mailing
     *
     * @param boolean $mailing
     * @return User
     */
    public function setMailing($mailing)
    {
        $this->mailing = $mailing;

        return $this;
    }

    /**
     * Get mailing
     *
     * @return boolean 
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * Set not_all_mailing
     *
     * @param boolean $notAllMailing
     * @return User
     */
    public function setNotAllMailing($notAllMailing)
    {
        $this->not_all_mailing = $notAllMailing;

        return $this;
    }

    /**
     * Get not_all_mailing
     *
     * @return boolean 
     */
    public function getNotAllMailing()
    {
        return $this->not_all_mailing;
    }

    /**
     * Set overdue_unsubscribed
     *
     * @param boolean $overdueUnsubscribed
     * @return User
     */
    public function setOverdueUnsubscribed($overdueUnsubscribed)
    {
        $this->overdue_unsubscribed = $overdueUnsubscribed;

        return $this;
    }

    /**
     * Get overdue_unsubscribed
     *
     * @return boolean 
     */
    public function getOverdueUnsubscribed()
    {
        return $this->overdue_unsubscribed;
    }

    /**
     * Set offline
     *
     * @param boolean $offline
     * @return User
     */
    public function setOffline($offline)
    {
        $this->offline = $offline;

        return $this;
    }

    /**
     * Get offline
     *
     * @return boolean 
     */
    public function getOffline()
    {
        return $this->offline;
    }

    /**
     * Set reg_info
     *
     * @param array $regInfo
     * @return User
     */
    public function setRegInfo($regInfo)
    {
        $this->reg_info = $regInfo;

        return $this;
    }

    /**
     * Get reg_info
     *
     * @return array 
     */
    public function getRegInfo()
    {
        return $this->reg_info;
    }

    /**
     * Set popup_info
     *
     * @param array $popupInfo
     * @return User
     */
    public function setPopupInfo($popupInfo)
    {
        $this->popup_info = $popupInfo;

        return $this;
    }

    /**
     * Get popup_info
     *
     * @return array 
     */
    public function getPopupInfo()
    {
        return $this->popup_info;
    }

    /**
     * Set pass_info
     *
     * @param array $passInfo
     * @return User
     */
    public function setPassInfo($passInfo)
    {
        $this->pass_info = $passInfo;

        return $this;
    }

    /**
     * Get pass_info
     *
     * @return array 
     */
    public function getPassInfo()
    {
        return $this->pass_info;
    }

    /**
     * Set close_final_exam
     *
     * @param boolean $closeFinalExam
     * @return User
     */
    public function setCloseFinalExam($closeFinalExam)
    {
        $this->close_final_exam = $closeFinalExam;

        return $this;
    }

    /**
     * Get close_final_exam
     *
     * @return boolean 
     */
    public function getCloseFinalExam()
    {
        return $this->close_final_exam;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return User
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
     * Set required_notify
     *
     * @param \My\AppBundle\Entity\Notify $requiredNotify
     * @return User
     */
    public function setRequiredNotify(\My\AppBundle\Entity\Notify $requiredNotify = null)
    {
        $this->required_notify = $requiredNotify;

        return $this;
    }

    /**
     * Get required_notify
     *
     * @return \My\AppBundle\Entity\Notify 
     */
    public function getRequiredNotify()
    {
        return $this->required_notify;
    }

    /**
     * Set teacher
     *
     * @param \My\AppBundle\Entity\SupportCategory $teacher
     * @return User
     */
    public function setTeacher(\My\AppBundle\Entity\SupportCategory $teacher = null)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return \My\AppBundle\Entity\SupportCategory 
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Add themes_tests_logs
     *
     * @param \My\AppBundle\Entity\ThemeTestLog $themesTestsLogs
     * @return User
     */
    public function addThemesTestsLog(\My\AppBundle\Entity\ThemeTestLog $themesTestsLogs)
    {
        $this->themes_tests_logs[] = $themesTestsLogs;

        return $this;
    }

    /**
     * Remove themes_tests_logs
     *
     * @param \My\AppBundle\Entity\ThemeTestLog $themesTestsLogs
     */
    public function removeThemesTestsLog(\My\AppBundle\Entity\ThemeTestLog $themesTestsLogs)
    {
        $this->themes_tests_logs->removeElement($themesTestsLogs);
    }

    /**
     * Get themes_tests_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThemesTestsLogs()
    {
        return $this->themes_tests_logs;
    }

    /**
     * Add slices_logs
     *
     * @param \My\AppBundle\Entity\SliceLog $slicesLogs
     * @return User
     */
    public function addSlicesLog(\My\AppBundle\Entity\SliceLog $slicesLogs)
    {
        $this->slices_logs[] = $slicesLogs;

        return $this;
    }

    /**
     * Remove slices_logs
     *
     * @param \My\AppBundle\Entity\SliceLog $slicesLogs
     */
    public function removeSlicesLog(\My\AppBundle\Entity\SliceLog $slicesLogs)
    {
        $this->slices_logs->removeElement($slicesLogs);
    }

    /**
     * Get slices_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSlicesLogs()
    {
        return $this->slices_logs;
    }

    /**
     * Add exams_logs
     *
     * @param \My\AppBundle\Entity\ExamLog $examsLogs
     * @return User
     */
    public function addExamsLog(\My\AppBundle\Entity\ExamLog $examsLogs)
    {
        $this->exams_logs[] = $examsLogs;

        return $this;
    }

    /**
     * Remove exams_logs
     *
     * @param \My\AppBundle\Entity\ExamLog $examsLogs
     */
    public function removeExamsLog(\My\AppBundle\Entity\ExamLog $examsLogs)
    {
        $this->exams_logs->removeElement($examsLogs);
    }

    /**
     * Get exams_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExamsLogs()
    {
        return $this->exams_logs;
    }

    /**
     * Add final_exams_logs
     *
     * @param \My\AppBundle\Entity\FinalExamLog $finalExamsLogs
     * @return User
     */
    public function addFinalExamsLog(\My\AppBundle\Entity\FinalExamLog $finalExamsLogs)
    {
        $this->final_exams_logs[] = $finalExamsLogs;

        return $this;
    }

    /**
     * Remove final_exams_logs
     *
     * @param \My\AppBundle\Entity\FinalExamLog $finalExamsLogs
     */
    public function removeFinalExamsLog(\My\AppBundle\Entity\FinalExamLog $finalExamsLogs)
    {
        $this->final_exams_logs->removeElement($finalExamsLogs);
    }

    /**
     * Get final_exams_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFinalExamsLogs()
    {
        return $this->final_exams_logs;
    }

    /**
     * Add notifies
     *
     * @param \My\AppBundle\Entity\Notify $notifies
     * @return User
     */
    public function addNotify(\My\AppBundle\Entity\Notify $notifies)
    {
        $this->notifies[] = $notifies;

        return $this;
    }

    /**
     * Remove notifies
     *
     * @param \My\AppBundle\Entity\Notify $notifies
     */
    public function removeNotify(\My\AppBundle\Entity\Notify $notifies)
    {
        $this->notifies->removeElement($notifies);
    }

    /**
     * Get notifies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifies()
    {
        return $this->notifies;
    }

    /**
     * Add tests_logs
     *
     * @param \My\AppBundle\Entity\TestLog $testsLogs
     * @return User
     */
    public function addTestsLog(\My\AppBundle\Entity\TestLog $testsLogs)
    {
        $this->tests_logs[] = $testsLogs;

        return $this;
    }

    /**
     * Remove tests_logs
     *
     * @param \My\AppBundle\Entity\TestLog $testsLogs
     */
    public function removeTestsLog(\My\AppBundle\Entity\TestLog $testsLogs)
    {
        $this->tests_logs->removeElement($testsLogs);
    }

    /**
     * Get tests_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTestsLogs()
    {
        return $this->tests_logs;
    }

    /**
     * Add tests_knowledge_logs
     *
     * @param \My\AppBundle\Entity\TestKnowledgeLog $testsKnowledgeLogs
     * @return User
     */
    public function addTestsKnowledgeLog(\My\AppBundle\Entity\TestKnowledgeLog $testsKnowledgeLogs)
    {
        $this->tests_knowledge_logs[] = $testsKnowledgeLogs;

        return $this;
    }

    /**
     * Remove tests_knowledge_logs
     *
     * @param \My\AppBundle\Entity\TestKnowledgeLog $testsKnowledgeLogs
     */
    public function removeTestsKnowledgeLog(\My\AppBundle\Entity\TestKnowledgeLog $testsKnowledgeLogs)
    {
        $this->tests_knowledge_logs->removeElement($testsKnowledgeLogs);
    }

    /**
     * Get tests_knowledge_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTestsKnowledgeLogs()
    {
        return $this->tests_knowledge_logs;
    }

    /**
     * Add old_mobile_phones
     *
     * @param \My\AppBundle\Model\UserOldMobilePhone $oldMobilePhones
     * @return User
     */
    public function addOldMobilePhone(\My\AppBundle\Model\UserOldMobilePhone $oldMobilePhones)
    {
        $this->old_mobile_phones[] = $oldMobilePhones;

        return $this;
    }

    /**
     * Remove old_mobile_phones
     *
     * @param \My\AppBundle\Model\UserOldMobilePhone $oldMobilePhones
     */
    public function removeOldMobilePhone(\My\AppBundle\Model\UserOldMobilePhone $oldMobilePhones)
    {
        $this->old_mobile_phones->removeElement($oldMobilePhones);
    }

    /**
     * Get old_mobile_phones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOldMobilePhones()
    {
        return $this->old_mobile_phones;
    }

    /**
     * Add payment_logs
     *
     * @param \My\PaymentBundle\Entity\Log $paymentLogs
     * @return User
     */
    public function addPaymentLog(\My\PaymentBundle\Entity\Log $paymentLogs)
    {
        $this->payment_logs[] = $paymentLogs;

        return $this;
    }

    /**
     * Remove payment_logs
     *
     * @param \My\PaymentBundle\Entity\Log $paymentLogs
     */
    public function removePaymentLog(\My\PaymentBundle\Entity\Log $paymentLogs)
    {
        $this->payment_logs->removeElement($paymentLogs);
    }

    /**
     * Get payment_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaymentLogs()
    {
        return $this->payment_logs;
    }

    /**
     * Add payment_revert_logs
     *
     * @param \My\PaymentBundle\Entity\RevertLog $paymentRevertLogs
     * @return User
     */
    public function addPaymentRevertLog(\My\PaymentBundle\Entity\RevertLog $paymentRevertLogs)
    {
        $this->payment_revert_logs[] = $paymentRevertLogs;

        return $this;
    }

    /**
     * Remove payment_revert_logs
     *
     * @param \My\PaymentBundle\Entity\RevertLog $paymentRevertLogs
     */
    public function removePaymentRevertLog(\My\PaymentBundle\Entity\RevertLog $paymentRevertLogs)
    {
        $this->payment_revert_logs->removeElement($paymentRevertLogs);
    }

    /**
     * Get payment_revert_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaymentRevertLogs()
    {
        return $this->payment_revert_logs;
    }

    /**
     * Add support_dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $supportDialogs
     * @return User
     */
    public function addSupportDialog(\My\AppBundle\Entity\SupportDialog $supportDialogs)
    {
        $this->support_dialogs[] = $supportDialogs;

        return $this;
    }

    /**
     * Remove support_dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $supportDialogs
     */
    public function removeSupportDialog(\My\AppBundle\Entity\SupportDialog $supportDialogs)
    {
        $this->support_dialogs->removeElement($supportDialogs);
    }

    /**
     * Get support_dialogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSupportDialogs()
    {
        return $this->support_dialogs;
    }

    /**
     * Add last_support_dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $lastSupportDialogs
     * @return User
     */
    public function addLastSupportDialog(\My\AppBundle\Entity\SupportDialog $lastSupportDialogs)
    {
        $this->last_support_dialogs[] = $lastSupportDialogs;

        return $this;
    }

    /**
     * Remove last_support_dialogs
     *
     * @param \My\AppBundle\Entity\SupportDialog $lastSupportDialogs
     */
    public function removeLastSupportDialog(\My\AppBundle\Entity\SupportDialog $lastSupportDialogs)
    {
        $this->last_support_dialogs->removeElement($lastSupportDialogs);
    }

    /**
     * Get last_support_dialogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLastSupportDialogs()
    {
        return $this->last_support_dialogs;
    }

    /**
     * Add support_messages
     *
     * @param \My\AppBundle\Entity\SupportMessage $supportMessages
     * @return User
     */
    public function addSupportMessage(\My\AppBundle\Entity\SupportMessage $supportMessages)
    {
        $this->support_messages[] = $supportMessages;

        return $this;
    }

    /**
     * Remove support_messages
     *
     * @param \My\AppBundle\Entity\SupportMessage $supportMessages
     */
    public function removeSupportMessage(\My\AppBundle\Entity\SupportMessage $supportMessages)
    {
        $this->support_messages->removeElement($supportMessages);
    }

    /**
     * Get support_messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSupportMessages()
    {
        return $this->support_messages;
    }

    /**
     * Add user_confirmation
     *
     * @param \My\AppBundle\Model\UserConfirmation $userConfirmation
     * @return User
     */
    public function addUserConfirmation(\My\AppBundle\Model\UserConfirmation $userConfirmation)
    {
        $this->user_confirmation[] = $userConfirmation;

        return $this;
    }

    /**
     * Remove user_confirmation
     *
     * @param \My\AppBundle\Model\UserConfirmation $userConfirmation
     */
    public function removeUserConfirmation(\My\AppBundle\Model\UserConfirmation $userConfirmation)
    {
        $this->user_confirmation->removeElement($userConfirmation);
    }

    /**
     * Get user_confirmation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserConfirmation()
    {
        return $this->user_confirmation;
    }

    /**
     * Add reviews
     *
     * @param \My\AppBundle\Entity\Review $reviews
     * @return User
     */
    public function addReview(\My\AppBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \My\AppBundle\Entity\Review $reviews
     */
    public function removeReview(\My\AppBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set category
     *
     * @param \My\AppBundle\Entity\Category $category
     * @return User
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
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return User
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

    /**
     * Set region_place
     *
     * @param \My\AppBundle\Entity\RegionPlace $regionPlace
     * @return User
     */
    public function setRegionPlace(\My\AppBundle\Entity\RegionPlace $regionPlace = null)
    {
        $this->region_place = $regionPlace;

        return $this;
    }

    /**
     * Get region_place
     *
     * @return \My\AppBundle\Entity\RegionPlace 
     */
    public function getRegionPlace()
    {
        return $this->region_place;
    }

    /**
     * Set webgroup
     *
     * @param \My\AppBundle\Entity\Webgroup $webgroup
     * @return User
     */
    public function setWebgroup(\My\AppBundle\Entity\Webgroup $webgroup = null)
    {
        $this->webgroup = $webgroup;

        return $this;
    }

    /**
     * Get webgroup
     *
     * @return \My\AppBundle\Entity\Webgroup 
     */
    public function getWebgroup()
    {
        return $this->webgroup;
    }

    /**
     * Set offer
     *
     * @param \My\AppBundle\Entity\Offer $offer
     * @return User
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
     * Add closed_subjects
     *
     * @param \My\AppBundle\Entity\Subject $closedSubjects
     * @return User
     */
    public function addClosedSubject(\My\AppBundle\Entity\Subject $closedSubjects)
    {
        $this->closed_subjects[] = $closedSubjects;

        return $this;
    }

    /**
     * Remove closed_subjects
     *
     * @param \My\AppBundle\Entity\Subject $closedSubjects
     */
    public function removeClosedSubject(\My\AppBundle\Entity\Subject $closedSubjects)
    {
        $this->closed_subjects->removeElement($closedSubjects);
    }

    /**
     * Get closed_subjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClosedSubjects()
    {
        return $this->closed_subjects;
    }

    /**
     * Add read_themes
     *
     * @param \My\AppBundle\Entity\Theme $readThemes
     * @return User
     */
    public function addReadTheme(\My\AppBundle\Entity\Theme $readThemes)
    {
        $this->read_themes[] = $readThemes;

        return $this;
    }

    /**
     * Remove read_themes
     *
     * @param \My\AppBundle\Entity\Theme $readThemes
     */
    public function removeReadTheme(\My\AppBundle\Entity\Theme $readThemes)
    {
        $this->read_themes->removeElement($readThemes);
    }

    /**
     * Get read_themes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReadThemes()
    {
        return $this->read_themes;
    }

    /**
     * Add moderated_support_categories
     *
     * @param \My\AppBundle\Entity\SupportCategory $moderatedSupportCategories
     * @return User
     */
    public function addModeratedSupportCategory(\My\AppBundle\Entity\SupportCategory $moderatedSupportCategories)
    {
        $this->moderated_support_categories[] = $moderatedSupportCategories;

        return $this;
    }

    /**
     * Remove moderated_support_categories
     *
     * @param \My\AppBundle\Entity\SupportCategory $moderatedSupportCategories
     */
    public function removeModeratedSupportCategory(\My\AppBundle\Entity\SupportCategory $moderatedSupportCategories)
    {
        $this->moderated_support_categories->removeElement($moderatedSupportCategories);
    }

    /**
     * Get moderated_support_categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModeratedSupportCategories()
    {
        return $this->moderated_support_categories;
    }

    /**
     * Add read_infos
     *
     * @param \My\AppBundle\Entity\Info $readInfos
     * @return User
     */
    public function addReadInfo(\My\AppBundle\Entity\Info $readInfos)
    {
        $this->read_infos[] = $readInfos;

        return $this;
    }

    /**
     * Remove read_infos
     *
     * @param \My\AppBundle\Entity\Info $readInfos
     */
    public function removeReadInfo(\My\AppBundle\Entity\Info $readInfos)
    {
        $this->read_infos->removeElement($readInfos);
    }

    /**
     * Get read_infos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReadInfos()
    {
        return $this->read_infos;
    }
    /**
     * ORM\prePersist
     */
    public function photoPreUpload()
    {
        // Add your code here
    }

    /**
     * ORM\postPersist
     */
    public function photoUpload()
    {
        // Add your code here
    }

    /**
     * ORM\postUpdate
     */
    public function photoRemoveUploadCache()
    {
        // Add your code here
    }

    /**
     * ORM\postRemove
     */
    public function photoRemoveUpload()
    {
        // Add your code here
    }
}
