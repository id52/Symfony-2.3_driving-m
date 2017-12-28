<?php

namespace My\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use My\AppBundle\Entity\CategoryPrice;
use My\AppBundle\Entity\Notify as NotifyEntity;
use My\AppBundle\Entity\PromoKey;
use My\AppBundle\Entity\ServicePrice;
use My\AppBundle\Entity\Subject;
use My\AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;

class Notify
{
    protected $em;
    protected $mailer;
    protected $router;
    protected $templating;
    protected $sender;
    protected $settings;
    protected $codeSymbols = '2456789QWRYUSDFGJLZVN';

    public function __construct(
        EntityManager $em,
        \Swift_Mailer $mailer,
        RouterInterface $router,
        EngineInterface $tempating,
        $senderName,
        $senderEmail
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $tempating;
        $this->sender = array($senderEmail => $senderName);
    }

    public function sendEmail($to, $subject, $message, $type = 'text/html')
    {
        //some default main placeholders
        $placeholders = array();
        if ($to instanceof User) {
            $placeholders['{{ last_name }}'] = $to->getLastName();
            $placeholders['{{ first_name }}'] = $to->getFirstName();
            $placeholders['{{ patronymic }}'] = $to->getPatronymic();
            $placeholders['{{ dear }}'] = ($to->getSex() == 'female' ? 'Уважаемая' : 'Уважаемый');
            $to_email = $to->getEmail();
        } elseif (is_array($to)) {
            if (isset($to['last_name'])) {
                $placeholders['{{ last_name }}'] = $to['last_name'];
            }
            if (isset($to['first_name'])) {
                $placeholders['{{ first_name }}'] = $to['first_name'];
            }
            if (isset($to['patronymic'])) {
                $placeholders['{{ patronymic }}'] = $to['patronymic'];
            }
            if (isset($to['sex'])) {
                $placeholders['{{ dear }}'] = ($to['sex'] == 'female' ? 'Уважаемая' : 'Уважаемый');
            } else {
                $placeholders['{{ dear }}'] = 'Уважаемый/ая';
            }
            $to_email = $to['email'];
        } else {
            $placeholders['{{ dear }}'] = 'Уважаемый/ая';
            $to_email = $to;
        }

        for ($i = 1; $i <= 5; $i ++) {
            $placeholders['{{ sign_'.$i.' }}'] = $this->getSetting('sign_'.$i);
        }

        $subject = str_replace(array_keys($placeholders), array_values($placeholders), $subject);
        $message = str_replace(array_keys($placeholders), array_values($placeholders), $message);

        $message = $this->templating->render('AppBundle::_email.html.twig', array(
            'message' => $message,
        ));

        /** @var $email \Swift_Mime_Message */
        $email = \Swift_Message::newInstance()
            ->setFrom($this->sender)
            ->setTo($to_email)
            ->setSubject($subject)
            ->setBody($message, $type)
        ;
        $this->mailer->send($email);
    }

    public function sendNotify(User $to, $subject, $message, $isRequired = false)
    {
        $notify = new NotifyEntity();
        $notify->setTitle($subject);
        $notify->setText($message);
        $notify->setUser($to);
        $this->em->persist($notify);

        if ($isRequired) {
            $to->setRequiredNotify($notify);
        }
        $to->setNotifiesCnt($to->getNotifiesCnt() + 1);

        $this->em->persist($to);
        $this->em->flush();
    }

    public function send(User $to, $subject, $message, $isRequired = false)
    {
        $this->sendEmail($to, $subject, $message);
        $this->sendNotify($to, $subject, $message, $isRequired);
    }

    public function sendConfirmationRegistration(User $to)
    {
        if ($this->getSetting('confirmation_registration_enabled')) {
            $subject  = $this->getSetting('confirmation_registration_title');
            $message  = $this->getSetting('confirmation_registration_text');
            $link = $this->router->generate('fos_user_registration_confirm', array(
                'token' => $to->getConfirmationToken(),
            ), true);
            $message = str_replace('{{ link_confirm }}', $link, $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendResettingPassword(User $to)
    {
        if ($this->getSetting('resetting_password_enabled')) {
            $subject  = $this->getSetting('resetting_password_title');
            $message  = $this->getSetting('resetting_password_text');
            $link = $this->router->generate('fos_user_resetting_reset', array(
                'token' => $to->getConfirmationToken(),
            ), true);
            $message = str_replace('{{ link_reset }}', $link, $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterConfirmMobile(User $to)
    {
        if ($this->getSetting('after_confirm_mobile_enabled')) {
            $subject = $this->getSetting('after_confirm_mobile_title');
            $message = $this->getSetting('after_confirm_mobile_text');
//            $btn = trim($this->templating->render('AppBundle::_btn_second_payment.html.twig'));
//            $message = str_replace('{{ btn_second_payment }}', $btn, $message);
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_confirm_mobile_email_enabled')) {
            $subject = $this->getSetting('after_confirm_mobile_email_title');
            $message = $this->getSetting('after_confirm_mobile_email_text');
//            $btn = trim($this->templating->render('AppBundle::_link_second_payment.html.twig'));
//            $message = str_replace('{{ link_second_payment }}', $btn, $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterFirstPayment(User $to, $modifier = '')
    {
        if ($modifier) {
            $modifier .= '_';
        }

        $subject = '';
        $message = '';
        $proc_discount = 0;
        $sum_pay = 0;
        $sum_discount = 0;
        $region = $to->getRegion();
        $category = $to->getCategory();
        $price = $category->getPriceByRegion($region);
        $reg_info = $to->getRegInfo();
        $email_subject = '';
        $email_message = '';

        $sum_teor = $price->getPriceEdu();
        $sum_drv = $reg_info['with_at'] ? $price->getPriceDrvAt() : $price->getPriceDrv();
        $sum_full = $sum_teor + $sum_drv;

        switch ($reg_info['pay_method']) {
            case 'online':
                if ($this->getSetting('after_1_'.$modifier.'payment_enabled')) {
                    $subject = $this->getSetting('after_1_'.$modifier.'payment_title');
                    $message = $this->getSetting('after_1_'.$modifier.'payment_text');
                    $proc_discount = $price::DSC1;
                    $sum_pay = $price->getSum($reg_info['with_at']);
                    $sum_discount = $sum_full - $sum_pay;
                    $email_subject = $this->getSetting('after_1_'.$modifier.'payment_email_title');
                    $email_message = $this->getSetting('after_1_'.$modifier.'payment_email_text');
                }
                break;
            case 'online2':
                if ($this->getSetting('after_1_'.$modifier.'payment_enabled')) {
                    $subject = $this->getSetting('after_1_'.$modifier.'payment_2_title');
                    $message = $this->getSetting('after_1_'.$modifier.'payment_2_text');
                    $proc_discount = $price::DSC2;
                    $sum_pay = $price->getSum2();
                    $sum_discount = $sum_teor - $sum_pay;
                    $email_subject = $this->getSetting('after_1_'.$modifier.'payment_2_email_title');
                    $email_message = $this->getSetting('after_1_'.$modifier.'payment_2_email_text');
                }
                break;

            default:
                throw new \Exception('Not found pay method');
        }
        if ($subject && $message) {
            $btn = trim($this->templating->render('AppBundle::_btn_trainings.html.twig'));
            $message = str_replace('{{ btn_trainings }}', $btn, $message);
            $btn = trim($this->templating->render('AppBundle::_btn_profile_edit.html.twig'));
            $message = str_replace('{{ btn_profile_edit }}', $btn, $message);
            $message = str_replace('{{ last_name }}', $to->getLastName(), $message);
            $message = str_replace('{{ first_name }}', $to->getFirstName(), $message);
            $message = str_replace('{{ patronymic }}', $to->getPatronymic(), $message);
            $dear = $to->getSex() ? ($to->getSex() == 'female' ? 'Уважаемая' : 'Уважаемый') : 'Уважаемый/ая';
            $message = str_replace('{{ dear }}', $dear, $message);
            for ($i = 1; $i <= 5; $i ++) {
                $message = str_replace('{{ sign_'.$i.' }}', $this->getSetting('sign_'.$i), $message);
            }
            $message = str_replace('{{ training_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $message = str_replace('{{ proc_discount }}', $proc_discount, $message);
            $message = str_replace('{{ sum_full }}', $sum_full, $message);
            $message = str_replace('{{ sum_discount }}', $sum_discount, $message);
            $message = str_replace('{{ sum_pay }}', $sum_pay, $message);
            $message = str_replace('{{ sum_teor }}', $sum_teor, $message);
            $message = str_replace('{{ sum_drv }}', $sum_drv, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);

            $this->sendNotify($to, $subject, $message, true);
        }

        if ($this->getSetting('after_1_'.$modifier.'payment_email_enabled')) {
            $demo_period = $this->getSetting('access_time_after_2_payment');
            $email_message = str_replace('{{ demo_period }}', $demo_period, $email_message);
            $this->sendEmail($to, $email_subject, $email_message);
        }

        if ($to->isDiscount2FirstEnabled()) {
            $region = $to->getRegion();
            $date = new \DateTime('today');
            $date->add(new \DateInterval('P'.($region->getDiscount2FirstDays()+1).'D'));
            $date->sub(new \DateInterval('P1D'));
            $end_time = $date->format('Y.m.d 23:59:59');
            $discount = $region->getDiscount2FirstAmount();
            $price = 0;
            foreach ($region->getServicesPrices() as $service_price) { /** @var $service_price ServicePrice */
                if ($service_price->getActive() && $service_price->getService()->getType() == 'training') {
                    $price += $service_price->getPrice();
                }
            }
            $new_price = max($price - $discount, 0);

            $subject = $this->getSetting('discount_2_notify_first_email_title');
            $message = $this->getSetting('discount_2_notify_first_email_text');
            $message = str_replace('{{ end_time }}', $end_time, $message);
            $message = str_replace('{{ discount }}', $discount, $message);
            $message = str_replace('{{ price }}', $price, $message);
            $message = str_replace('{{ new_price }}', $new_price, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterSecondPayment(User $to, $modifier = '')
    {
        if ($modifier) {
            $modifier .= '_';
        }
        if ($this->getSetting('after_2_'.$modifier.'payment_enabled')) {
            $subject = $this->getSetting('after_2_'.$modifier.'payment_title');
            $message = $this->getSetting('after_2_'.$modifier.'payment_text');
            $btn = trim($this->templating->render('AppBundle::_btn_trainings.html.twig'));
            $message = str_replace('{{ btn_trainings }}', $btn, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendNotify($to, $subject, $message, true);
        }

        if ($this->getSetting('after_2_'.$modifier.'payment_email_enabled')) {
            $subject = $this->getSetting('after_2_'.$modifier.'payment_email_title');
            $message = $this->getSetting('after_2_'.$modifier.'payment_email_text');
            $btn = trim($this->templating->render('AppBundle::_link_trainings.html.twig'));
            $message = str_replace('{{ link_trainings }}', $btn, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterThirdPayment(User $to, $modifier = '')
    {
        if ($modifier) {
            $modifier .= '_';
        }
        if ($this->getSetting('after_3_'.$modifier.'payment_enabled')) {
            $subject = $this->getSetting('after_3_'.$modifier.'payment_title');
            $message = $this->getSetting('after_3_'.$modifier.'payment_text');
            $btn = trim($this->templating->render('AppBundle::_btn_trainings.html.twig'));
            $message = str_replace('{{ btn_trainings }}', $btn, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendNotify($to, $subject, $message, true);
        }

        if ($this->getSetting('after_3_'.$modifier.'payment_email_enabled')) {
            $subject = $this->getSetting('after_3_'.$modifier.'payment_email_title');
            $message = $this->getSetting('after_3_'.$modifier.'payment_email_text');
            $btn = trim($this->templating->render('AppBundle::_link_trainings.html.twig'));
            $message = str_replace('{{ link_trainings }}', $btn, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterPayment(User $to, $modifier = '')
    {
        if ($modifier) {
            $modifier .= '_';
        }
        if ($this->getSetting('after_'.$modifier.'payment_enabled')) {
            $subject = $this->getSetting('after_'.$modifier.'payment_title');
            $message = $this->getSetting('after_'.$modifier.'payment_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_'.$modifier.'payment_email_enabled')) {
            $subject = $this->getSetting('after_'.$modifier.'payment_email_title');
            $message = $this->getSetting('after_'.$modifier.'payment_email_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterAllSlices(User $to, Subject $oSubject)
    {
        if ($this->getSetting('after_all_slices_'.$oSubject->getId().'_enabled')) {
            $subject = $this->getSetting('after_all_slices_'.$oSubject->getId().'_title');
            $message = $this->getSetting('after_all_slices_'.$oSubject->getId().'_text');
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_all_slices_'.$oSubject->getId().'_email_enabled')) {
            $subject = $this->getSetting('after_all_slices_'.$oSubject->getId().'_email_title');
            $message = $this->getSetting('after_all_slices_'.$oSubject->getId().'_email_text');
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterExam(User $to, Subject $oSubject)
    {
        if ($this->getSetting('after_exam_'.$oSubject->getId().'_enabled')) {
            $subject = $this->getSetting('after_exam_'.$oSubject->getId().'_title');
            $message = $this->getSetting('after_exam_'.$oSubject->getId().'_text');
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_exam_'.$oSubject->getId().'_email_enabled')) {
            $subject = $this->getSetting('after_exam_'.$oSubject->getId().'_email_title');
            $message = $this->getSetting('after_exam_'.$oSubject->getId().'_email_text');
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterFailExam(User $to, Subject $oSubject)
    {
        if ($this->getSetting('after_fail_exam_'.$oSubject->getId().'_enabled')) {
            $subject = $this->getSetting('after_fail_exam_'.$oSubject->getId().'_title');
            $message = $this->getSetting('after_fail_exam_'.$oSubject->getId().'_text');
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_fail_exam_'.$oSubject->getId().'_email_enabled')) {
            $subject = $this->getSetting('after_fail_exam_'.$oSubject->getId().'_email_title');
            $message = $this->getSetting('after_fail_exam_'.$oSubject->getId().'_email_text');
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterAllExams(User $to)
    {
        if ($this->getSetting('after_all_exams_enabled')) {
            $subject = $this->getSetting('after_all_exams_title');
            $message = $this->getSetting('after_all_exams_text');
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_all_exams_email_enabled')) {
            $subject = $this->getSetting('after_all_exams_email_title');
            $message = $this->getSetting('after_all_exams_email_text');
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterFinalExam(User $to)
    {
        if ($this->getSetting('after_final_exam_enabled')) {
            $subject = $this->getSetting('after_final_exam_title');
            $message = $this->getSetting('after_final_exam_text');
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('after_final_exam_email_enabled')) {
            $subject = $this->getSetting('after_final_exam_email_title');
            $message = $this->getSetting('after_final_exam_email_text');
            $link = trim($this->templating->render('AppBundle::_link_certificate.html.twig', array('user' => $to)));
            $message = str_replace('{{ link_certificate }}', $link, $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendNoPayments(User $to, Promo $promoService)
    {
        if ($this->getSetting('no_payments_enabled')) {
            $now = new \DateTime();
            // Guess what number of letter it is
            for ($i = 1; $i <= 5; $i++) {
                $date = clone $to->getCreatedAt();
                $date->add(new \DateInterval('P'.$this->getSetting('notify_no_payments_'.$i).'D'));
                if ($now->format('d-m-Y') === $date->format('d-m-Y')) {
                    $hash = current($promoService->generatePromoKeyHashes(1));

                    $validTo = clone $now;
                    $days = $this->getSetting('notify_no_payments_promo_expiration_'.$i);
                    $validTo = $validTo->add(new \DateInterval('P'.$days.'D'));

                    $key = new PromoKey();
                    $key->setActive(true);
                    $key->setDiscount($this->getSetting('notify_no_payments_promo_discount_'.$i));
                    $key->setHash($hash);
                    $key->setOverdueLetterNum($i);
                    $key->setPromo(null);
                    $key->setSource('auto_overdue');
//                    $key->setType('site_access');
                    $key->setValidTo($validTo);
                    $this->em->persist($key);
                    $this->em->flush();

                    $price = 0;
                    // Take a look only on categories prices - no obsolete services with site_access
                    $categories_prices = $this->em->getRepository('AppBundle:CategoryPrice')->createQueryBuilder('cp')
                        ->andWhere('cp.active = :active')->setParameter(':active', true)
                        ->andWhere('cp.region = :region')->setParameter(':region', $to->getRegion())
                        ->andWhere('cp.category = :category')->setParameter(':category', $to->getCategory())
                        ->getQuery()->execute();
                    foreach ($categories_prices as $categories_price) { /** @var $categories_price CategoryPrice */
                        $price += $categories_price->getPriceEdu();
                    }

                    $newPrice = $price - $key->getDiscount();
                    if ($newPrice < 0) {
                        $newPrice = 0;
                    }

                    $placeholders = array();
                    $placeholders['{{ discount }}'] = $key->getDiscount();
                    $placeholders['{{ promo_key }}'] = $hash;
                    $placeholders['{{ promo_expiration }}'] = $key->getValidTo()->format('d.m.Y');
                    $placeholders['{{ price }}'] = $price;
                    $placeholders['{{ new_price }}'] = $newPrice;
                    $placeholders['{{ demo_period }}'] = $this->settings['access_time_after_2_payment'];
                    $link = $this->router->generate('my_unsubscribe_overdue', array('email'=>$to->getEmail()), true);
                    $placeholders['{{ unsubscribe_link }}'] = $link;

                    $subject = $this->getSetting('no_payments_title');
                    $subject = str_replace(array_keys($placeholders), array_values($placeholders), $subject);
                    $message = $this->getSetting('no_payments_text');
                    $message = str_replace(array_keys($placeholders), array_values($placeholders), $message);
                    $this->sendEmail($to, $subject, $message);

                    break;
                }
            }
        }
    }

    public function sendBeforeAccessTimeEndAfter1Payment(User $to)
    {
        if ($this->getSetting('before_access_time_end_after_1_payment_enabled')) {
            $subject = $this->getSetting('before_access_time_end_after_1_payment_title');
            $message = $this->getSetting('before_access_time_end_after_1_payment_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('before_access_time_end_after_1_payment_email_enabled')) {
            $subject = $this->getSetting('before_access_time_end_after_1_payment_email_title');
            $message = $this->getSetting('before_access_time_end_after_1_payment_email_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterAccessTimeEndAfter1Payment(User $to)
    {
        if ($this->getSetting('after_access_time_end_after_1_payment_enabled')) {
            $subject  = $this->getSetting('after_access_time_end_after_1_payment_title');
            $message  = $this->getSetting('after_access_time_end_after_1_payment_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $link = $this->router->generate('my_unsubscribe_payment_1', array(
                'email' => $to->getEmail(),
            ), true);
            $message = str_replace('{{ link_unsubscribe }}', $link, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendBeforeAccessTimeEndAfter2Payment(User $to)
    {
        if ($this->getSetting('before_access_time_end_after_2_payment_enabled')) {
            $subject = $this->getSetting('before_access_time_end_after_2_payment_title');
            $message = $this->getSetting('before_access_time_end_after_2_payment_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('before_access_time_end_after_2_payment_email_enabled')) {
            $subject = $this->getSetting('before_access_time_end_after_2_payment_email_title');
            $message = $this->getSetting('before_access_time_end_after_2_payment_email_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterAccessTimeEndAfter2Payment(User $to)
    {
        if ($this->getSetting('after_access_time_end_after_2_payment_enabled')) {
            $subject  = $this->getSetting('after_access_time_end_after_2_payment_title');
            $message  = $this->getSetting('after_access_time_end_after_2_payment_text');
            $link = $this->router->generate('my_unsubscribe_payment_2', array(
                'email' => $to->getEmail(),
            ), true);
            $message = str_replace('{{ link_unsubscribe }}', $link, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendBeforeAccessTimeEndAfter3Payment(User $to)
    {
        if ($this->getSetting('before_access_time_end_after_3_payment_enabled')) {
            $subject = $this->getSetting('before_access_time_end_after_3_payment_title');
            $message = $this->getSetting('before_access_time_end_after_3_payment_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendNotify($to, $subject, $message);
        }

        if ($this->getSetting('before_access_time_end_after_3_payment_email_enabled')) {
            $subject = $this->getSetting('before_access_time_end_after_3_payment_email_title');
            $message = $this->getSetting('before_access_time_end_after_3_payment_email_text');
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendAfterAccessTimeEndAfter3Payment(User $to)
    {
        if ($this->getSetting('after_access_time_end_after_3_payment_enabled')) {
            $subject  = $this->getSetting('after_access_time_end_after_3_payment_title');
            $message  = $this->getSetting('after_access_time_end_after_3_payment_text');
            $link = $this->router->generate('my_unsubscribe_payment_3', array(
                'email' => $to->getEmail(),
            ), true);
            $message = str_replace('{{ link_unsubscribe }}', $link, $message);
            $message = str_replace('{{ demo_period }}', $this->getSetting('access_time_after_2_payment'), $message);
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendSupportAnswered(User $to)
    {
        if ($this->getSetting('support_answered_email_enabled')) {
            $subject = $this->getSetting('support_answered_email_title');
            $message = $this->getSetting('support_answered_email_text');
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendSupportAnsweredAdmin(User $to)
    {
        if ($this->getSetting('support_answered_email_admin_enabled')) {
            $subject = $this->getSetting('support_answered_email_admin_title');
            $message = $this->getSetting('support_answered_email_admin_text');
            $this->sendEmail($to, $subject, $message);
        }
    }

    public function sendMailing($to, $subject, $message)
    {
        $message = str_replace('{{ username }}', $to['first_name'].' '.$to['last_name'], $message);
        $link = trim($this->templating->render('AppBundle::_link_unsubscribe_mailing.html.twig', array('user' => $to)));
        $message = str_replace('{{ unsubscribe_link }}', $link, $message);
        $this->sendEmail($to, $subject, $message);
    }

    public function sendCongratulationBirthday($to)
    {
        $subject = $this->getSetting('birthday_greeting_title');
        $message = $this->getSetting('birthday_greeting_text');

        $promo = '';
        for ($i = 0; $i < 10; $i ++) {
            $promo .= $this->codeSymbols[rand(0, strlen($this->codeSymbols)-1)];
        }

        $date = new \DateTime();
        $date->modify('+1 month');
        $expiryDate = $date->format('j / n / Y');

        $placeholders = array(
            '{{ promo }}' => $promo,
            '{{ date }}'  => $expiryDate,
        );

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $message);

        $this->sendEmail($to, $subject, $message);
    }

    protected function getSettings()
    {
        if (!$this->settings) {
            $this->settings = $this->em->getRepository('AppBundle:Setting')->getAllData();
        }
        return $this->settings;
    }

    protected function getSetting($name)
    {
        $settings = $this->getSettings();
        return isset($settings[$name]) ? $settings[$name] : '';
    }
}
