<?php

namespace My\AppBundle\Entity;

use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use My\AppBundle\Model\User as UserModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;

class User extends UserModel implements EquatableInterface
{
    /**
     * @var $photo_file \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $photo_file;
    protected $notifies_cnt = 0;
    protected $payment_1_paid_not_notify = false;
    protected $payment_2_paid_not_notify = false;
    protected $payment_3_paid_not_notify = false;
    protected $white_ips = array();
    protected $moderated = false;
    protected $not_registration = false;
    protected $foreign_passport = false;
    protected $discount_2_notify_first = false;
    protected $discount_2_notify_second = false;
    protected $mailing = true;
    protected $offline = false;
    protected $overdue_unsubscribed = false;
    protected $reg_info = array();
    protected $popup_info = array();
    protected $pass_info = array();
    protected $user_roles = array(
        'ROLE_USER',
        'ROLE_USER_PAID',
        'ROLE_USER_PAID2',
        'ROLE_USER_PAID3',
        'ROLE_USER_FULL_PROFILE',
    );
    protected $close_final_exam = false;

    /** @var $em \Doctrine\ORM\EntityManager */
    public $em;

    public function __construct()
    {
        if (!$this->paid_notified_at) {
            $this->paid_notified_at = new \DateTime();
        }

        parent::__construct();
    }

    public function getFullName()
    {
        return $this->last_name.' '.$this->first_name.' '.$this->patronymic;
    }

    public function getPhotoFile()
    {
        return $this->photo_file;
    }

    public function setPhotoFile(UploadedFile $file)
    {
        $this->photo_file = $file;
    }

    public function photoPreUpload()
    {
        if ($this->photo_file) {
            if ($this->photo) {
                $this->photoRemove();
            }
            $this->photo = sha1(uniqid()).'.'.$this->photo_file->guessExtension();
        }
    }

    public function photoUpload()
    {
        if ($this->photo_file) {
            $this->photo_file->move($this->getPhotoUploadRootDir(), $this->photo);
            $this->photo_file = null;

            $image = new Imagine();
            $image
                ->open($this->getPhotoAbsolutePath())
                ->thumbnail(new Box(900, 1200))
                ->save($this->getPhotoAbsolutePath())
            ;
        }
    }

    public function photoRemoveUpload()
    {
        if ($file = $this->getPhotoAbsolutePath()) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function photoRemoveUploadCache()
    {
        if ($web_path = $this->getPhotoWebPath()) {
            $config = Yaml::parse(__DIR__.'/../../../../app/config/config.yml');
            if (isset($config['liip_imagine'])) {
                $filter_sets = array_keys($config['liip_imagine']['filter_sets']);
                $imagine_cache_dir = __DIR__.'/../../../../web'.$config['liip_imagine']['cache_prefix'];
                foreach ($filter_sets as $filter) {
                    $file = $imagine_cache_dir.'/'.$filter.'/'.$web_path;
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    public function getPhotoAbsolutePath()
    {
        return empty($this->photo) ? null : $this->getPhotoUploadRootDir().'/'.$this->photo;
    }

    public function getPhotoWebPath()
    {
        return empty($this->photo) ? null : $this->getPhotoUploadDir().'/'.$this->photo;
    }

    public function photoRemove()
    {
        $this->photoRemoveUpload();
        $this->photoRemoveUploadCache();
        $this->photo = null;
        $this->photo_coords = array();
    }

    public function setEmail($email)
    {
        $this->setUsername($email);
        return parent::setEmail($email);
    }

    public function photoRecountCoords()
    {
        $aratio = 3 / 4;
        list($width, $height) = getimagesize($this->getPhotoAbsolutePath());
        if ($width / $height > $aratio) {
            $w = (int)round($height * $aratio);
            $h = $height;
            $x = (int)round(($width - $w) / 2);
            $y = 0;
        } else {
            $w = $width;
            $h = (int)round($width / $aratio);
            $x = 0;
            $y = (int)round(($height - $h) / 2);
        }

        $this->setPhotoCoords(array(
            'w' => $w,
            'h' => $h,
            'x' => $x,
            'y' => $y,
        ));
    }

    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->roles,
            $this->white_ips,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->roles,
            $this->white_ips
        ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof $this) {
            $isEqual = $user->getRoles() === $this->getRoles();
            return $isEqual;
        }
        return false;
    }

    public function setPhoneMobile($phoneMobile)
    {
        $this->phone_mobile = $phoneMobile;
        $this->phone_mobile_status = null;
        return $this;
    }

    public function getPhoneMobileFormat()
    {
        $p = $this->getPhoneMobile();
        if (preg_match('#^(\d{3})(\d{3})(\d{2})(\d{2})$#', $p, $m)) {
            return '+7 ('.$m[1].') '.$m[2].'-'.$m[3].'-'.$m[4];
        }
        return null;
    }

    public function getURoles()
    {
        $roles = $this->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array('ROLE_ADMIN');
        } else {
            $roles = array_diff($roles, $this->user_roles);
        }
        return $roles;
    }

    public function setURoles($uRoles)
    {
        $uRoles = (array)$uRoles;
        $roles = (array)$this->getRoles();
        $roles = array_intersect($roles, $this->user_roles);
        if (in_array('ROLE_ADMIN', $uRoles)) {
            $add_roles = array('ROLE_ADMIN');
        } else {
            $add_roles = $uRoles;
        }
        $this->setRoles(array_merge($roles, $add_roles));
        return $this;
    }

    public function getUWhiteIps()
    {
        $ips = $this->getWhiteIps();
        $ips = implode(PHP_EOL, (array)$ips);
        return $ips;
    }

    public function setUWhiteIps($ips)
    {
        $ips = explode(PHP_EOL, $ips);
        foreach ($ips as $k => $ip) {
            $ip = trim($ip);
            if ($ip) {
                $ips[$k] = $ip;
            } else {
                unset($ips[$k]);
            }
        }
        $ips = array_unique($ips);
        sort($ips);
        $this->setWhiteIps($ips);
        return $this;
    }

    public function getCurrentDiscount2()
    {
        $discount = 0;

        $region = $this->getRegion();
        if ($region) {
            if ($this->isDiscount2FirstEnabled()) {
                $discount = $region->getDiscount2FirstAmount();
            } elseif ($this->isDiscount2SecondEnabled()) {
                $discount = $region->getDiscount2SecondAmount();
            }
        }

        return $discount;
    }

    /**
     * Get end date for second payment discount period # 1 or 2. For timers purposes i.e.
     *
     * @return \DateTime End date for discount period for second payment
     * (either the first or the second discount period!). Null if deiscount is not enabled
     */
    public function getDiscount2PeriodEndDate()
    {
        $region = $this->getRegion();
        if ($region && !$this->getPayment2Paid()) {
            $date = clone $this->getPayment1Paid();
            $now  = new  \DateTime();
            if ($date) {
                // end of first discount period
                // +1 because day of payment is on first discount period
                // as we can see below in isDiscount2[First|Second]Enabled()
                $date->add(new \DateInterval('P'.($region->getDiscount2FirstDays() + 1).'D'));
                if ($now < $date && $region->isDiscount2FirstEnabled()) {
                    return $date;
                } else {
                    $period2StartDate = $date->add(new \DateInterval('P'.$region->getDiscount2BetweenPeriodDays().'D'));
                    $period2EndDate   = clone $period2StartDate;
                    $period2EndDate->add(new \DateInterval('P'.$region->getDiscount2SecondDays().'D'));
                    if ($now < $period2EndDate && $region->isDiscount2SecondEnabled() && $now > $period2StartDate) {
                        return $period2EndDate;
                    }
                }
            }
        }
        return null;
    }

    public function isDiscount2FirstEnabled()
    {
        $enabled = false;

        $region = $this->getRegion();
        if ($region && !$this->getPayment2Paid()) {
            $date1 = $this->getPayment1Paid();
            if ($date1) {
                $after_date1 = $date1->diff(new \DateTime())->days;
                if ($region->isDiscount2FirstEnabled()
                    && $after_date1 <= $region->getDiscount2FirstDays()
                ) {
                    $enabled = true;
                }
            }
        }

        return $enabled;
    }

    public function isDiscount2SecondEnabled()
    {
        $enabled = false;

        $region = $this->getRegion();
        if ($region && !$this->getPayment2Paid()) {
            $date1 = $this->getPayment1Paid();
            if ($date1) {
                $after_date1 = $date1->diff(new \DateTime())->days;
                $betweenPeriodDays = $region->getDiscount2BetweenPeriodDays();
                $start_discount_2_second = $region->getDiscount2FirstDays() + $betweenPeriodDays;
                if ($region->isDiscount2SecondEnabled()
                    && $after_date1 > $start_discount_2_second
                    && $after_date1 <= $start_discount_2_second + $region->getDiscount2SecondDays()
                ) {
                    $enabled = true;
                }
            }
        }

        return $enabled;
    }

    /**
     * Возвращает сумму скидки для второй оплаты
     */
    public function getSecondPaidDiscount()
    {
        $region = $this->getRegion();

        if ($this->isDiscount2FirstEnabled()) {
            return $region->getDiscount2FirstAmount();
        } elseif ($this->isDiscount2SecondEnabled()) {
            return  $region->getDiscount2SecondAmount();
        }
        return 0;
    }

    public function isPromoUsedFor($num = 1)
    {
        $logs = $this->getPaymentLogs();
        foreach ($logs as $log) { /** @var $log \My\PaymentBundle\Entity\Log */
            $comment = json_decode($log->getComment(), true);
            if (isset($comment['categories']) && $log->getPaid()) {
                $paids = explode(',', $comment['paid']);
                return (in_array($num, $paids) && !empty($comment['promo_key']));
            }
        }
        return false;
    }

    public function isPromoUsedFor1()
    {
        return $this->isPromoUsedFor(1);
    }

    public function isPromoUsedFor2()
    {
        return $this->isPromoUsedFor(2);
    }

    public function isPromoUsedFor3()
    {
        return $this->isPromoUsedFor(3);
    }

    public function isSumFor($num = 1)
    {
        $logs = $this->getPaymentLogs();
        foreach ($logs as $log) { /** @var $log \My\PaymentBundle\Entity\Log */
            $comment = json_decode($log->getComment(), true);
            if (isset($comment['categories']) && $log->getPaid()) {
                $paids = explode(',', $comment['paid']);
                return (in_array($num, $paids) && $log->getSum() > 0);
            }
        }
        return false;
    }

    public function isSumFor1()
    {
        return $this->isSumFor(1);
    }

    public function isSumFor2()
    {
        return $this->isSumFor(2);
    }

    public function isSumFor3()
    {
        return $this->isSumFor(3);
    }

    protected function isOfflinePaidFor($num = 1)
    {
        $logs = $this->getPaymentLogs();
        foreach ($logs as $log) { /** @var $log \My\PaymentBundle\Entity\Log */
            $comment = json_decode($log->getComment(), true);
            if (isset($comment['categories']) && $log->getPaid()) {
                $paids = explode(',', $comment['paid']);
                return (in_array($num, $paids) && !empty($comment['moderator_id']));
            }
        }
        return false;
    }

    public function isOfflinePaidFor1()
    {
        return $this->isOfflinePaidFor(1);
    }

    public function isOfflinePaidFor2()
    {
        return $this->isOfflinePaidFor(2);
    }

    public function isOfflinePaidFor3()
    {
        return $this->isOfflinePaidFor(3);
    }

    public function getCaptcha()
    {
    }

    public function setCaptcha($captcha)
    {
    }

    public function getAge()
    {
        if (!$this->birthday) {
            return null;
        }

        return $this->birthday->diff(new \DateTime())->y;
    }

    protected function getPhotoUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getPhotoUploadDir();
    }

    protected function getPhotoUploadDir()
    {
        return '/uploads/photos';
    }
}
