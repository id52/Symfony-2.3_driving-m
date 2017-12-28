<?php

namespace My\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use My\AppBundle\Entity\Setting;

class LoadSettingsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $subjects = $manager->getRepository('AppBundle:Subject')->findAll();

        $setting = new Setting();
        $setting->setKey('counters_yandex');
        $setting->setValue('');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('contacts_email');
        $setting->setValue('contact@example.com');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('contacts_phone1_prefix');
        $setting->setValue('495');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('contacts_phone1');
        $setting->setValue('00-00-01');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('contacts_phone2_prefix');
        $setting->setValue('499');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('contacts_phone2');
        $setting->setValue('00-00-02');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('socials_vk');
        $setting->setValue('http://vk.com/example');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('socials_facebook');
        $setting->setValue('http://fb.com/example');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('socials_twitter');
        $setting->setValue('http://twitter.com/example');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('profile_final_exam');
        $value = 'Сообщения в профиле {Текст о сдаче финального экзамена с ссылкой';
        $value .= ' (вставьте %certificate_link% для замены на адрес для скачивания)}';
        $setting->setValue($value);
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_timeout');
        $setting->setValue('Сообщения в тесте как в ГИБДД по билетам {Время закончилось}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_complete');
        $setting->setValue('Сообщения в тесте как в ГИБДД по билетам {Тестирование завершено}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_long_time');
        $setting->setValue('Сообщения в тесте как в ГИБДД по билетам {Долго отсутствовал}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_max_errors');
        $setting->setValue('Сообщения в тесте как в ГИБДД по билетам {Много ошибок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_knowledge_timeout');
        $setting->setValue('Сообщения в тесте как в ГИБДД по темам {Время закончилось}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_knowledge_complete');
        $setting->setValue('Сообщения в тесте как в ГИБДД по темам {Тестирование завершено}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_knowledge_long_time');
        $setting->setValue('Сообщения в тесте как в ГИБДД по темам {Долго отсутствовал}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_test_knowledge_max_errors');
        $setting->setValue('Сообщения в тесте как в ГИБДД по темам {Много ошибок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('trainings_help_btn');
        $setting->setValue('Кнопки помощи {В обучении}');
        $manager->persist($setting);

        foreach ($subjects as $subject) {
            $setting = new Setting();
            $setting->setKey('training_'.$subject->getId().'_help_btn');
            $setting->setValue('Кнопки помощи {В предмете «'.$subject->getTitle().'»}');
            $manager->persist($setting);
        }

        $setting = new Setting();
        $setting->setKey('theme_help_btn');
        $setting->setValue('Кнопки помощи {В темах}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('question_help_btn');
        $setting->setValue('Кнопки помощи {В вопросах}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('birthday_greeting_title');
        $setting->setValue('[E-mail] Письмо поздравления с днем рождения {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('birthday_greeting_text');
        $setting->setValue('[E-mail] Письмо поздравления с днем рождения {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('lock_user_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('lock_user_title');
        $setting->setValue('[Email] Письма о блокировке {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('lock_user_text');
        $setting->setValue('[Email] Письма о блокировке {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('unlock_user_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('unlock_user_title');
        $setting->setValue('[Email] Письма о разблокировке {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('unlock_user_text');
        $setting->setValue('[Email] Письма о разблокировке {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('error_account_locked');
        $setting->setValue('Ошибки {Для заблокированного пользователя}');
        $manager->persist($setting);

        for ($i = 1; $i <= 5; $i++) {
            $setting = new Setting();
            $setting->setKey('sign_'.$i);
            $setting->setValue('Текст подписи №'.$i);
            $manager->persist($setting);
        }

        $setting = new Setting();
        $setting->setKey('confirmation_registration_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmation_registration_title');
        $setting->setValue('[Email] Подтверждение регистрации {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmation_registration_text');
        $setting->setValue('[Email] Подтверждение регистрации {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmed_registration_title');
        $setting->setValue('[Text] Текст на странице после регистрации {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmed_registration_text');
        $setting->setValue('[Text] Текст на странице после регистрации {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmed_registration_2_title');
        $setting->setValue('[Text] Текст на странице после регистрации 2 {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmed_registration_2_text');
        $setting->setValue('[Text] Текст на странице после регистрации 2 {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmed_registration_3_title');
        $setting->setValue('[Text] Текст на странице после регистрации 3 {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('confirmed_registration_3_text');
        $setting->setValue('[Text] Текст на странице после регистрации 3 {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('resetting_password_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('resetting_password_title');
        $setting->setValue('[Email] Сброс пароля {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('resetting_password_text');
        $setting->setValue('[Email] Сброс пароля {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('payments_mobile_not_confirmed_title');
        $setting->setValue('[Text] Номер мобильного телефона не подтверждён {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('payments_mobile_not_confirmed_text');
        $setting->setValue('[Text] Номер мобильного телефона не подтверждён {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notfull_text');
        $setting->setValue('[Text] Профиль не заполнен {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_confirm_mobile_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_confirm_mobile_title');
        $setting->setValue('[Notify] Подтверждения номера мобильного телефона {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_confirm_mobile_text');
        $setting->setValue('[Notify] Подтверждения номера мобильного телефона {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_confirm_mobile_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_confirm_mobile_email_title');
        $setting->setValue('[Email] Подтверждения номера мобильного телефона {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_confirm_mobile_email_text');
        $setting->setValue('[Email] Подтверждения номера мобильного телефона {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_after_1_payment');
        $setting->setValue(14);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_after_2_payment');
        $setting->setValue(21);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_after_3_payment');
        $setting->setValue(365);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_end_popup_after_2_payment_1');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_end_popup_after_2_payment_2');
        $setting->setValue(3);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_end_popup_after_2_payment_3');
        $setting->setValue(7);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('access_time_end_popup_after_2_payment_4');
        $setting->setValue(14);
        $setting->setType('integer');
        $manager->persist($setting);

        for ($i = 5; $i <= 10; $i ++) {
            $setting = new Setting();
            $setting->setKey('access_time_end_popup_after_2_payment_'.$i);
            $setting->setValue(0);
            $setting->setType('integer');
            $manager->persist($setting);
        }

        for ($i = 1; $i <= 3; $i ++) {
            $setting = new Setting();
            $setting->setKey('access_time_end_notify_after_'.$i.'_payment_1');
            $setting->setValue(1);
            $setting->setType('integer');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('access_time_end_notify_after_'.$i.'_payment_2');
            $setting->setValue(3);
            $setting->setType('integer');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('access_time_end_notify_after_'.$i.'_payment_3');
            $setting->setValue(7);
            $setting->setType('integer');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('access_time_end_notify_after_'.$i.'_payment_4');
            $setting->setValue(14);
            $setting->setType('integer');
            $manager->persist($setting);

            for ($j = 5; $j <= 16; $j ++) {
                $setting = new Setting();
                $setting->setKey('access_time_end_notify_after_'.$i.'_payment_'.$j);
                $setting->setValue(0);
                $setting->setType('integer');
                $manager->persist($setting);
            }
        }

        $setting = new Setting();
        $setting->setKey('no_payments_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('no_payments_title');
        $setting->setValue('[Email] Оповещение об отсутствии оплат {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('no_payments_text');
        $setting->setValue('[Email] Оповещение об отсутствии оплат {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_without_2_payment_title');
        $setting->setValue('[Text] Не оплачен второй платёж {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_without_2_payment_text');
        $setting->setValue('[Text] Не оплачен второй платёж {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_without_3_payment_title');
        $setting->setValue('[Text] Не оплачен третий платёж {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_without_3_payment_text');
        $setting->setValue('[Text] Не оплачен третий платёж {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('first_payment_text');
        $setting->setValue('[Text] Текст на странице первой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('first_payment_2_text');
        $setting->setValue('[Text] Текст на странице первой оплаты 2 {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_title');
        $setting->setValue('[Notify] После первой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_text');
        $setting->setValue('[Notify] После первой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_email_title');
        $setting->setValue('[Email] После первой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_email_text');
        $setting->setValue('[Email] После первой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_2_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_2_title');
        $setting->setValue('[Notify] После первой оплаты 2 {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_2_text');
        $setting->setValue('[Notify] После первой оплаты 2 {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_2_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_2_email_title');
        $setting->setValue('[Email] После первой оплаты 2 {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_1_payment_2_email_text');
        $setting->setValue('[Email] После первой оплаты 2 {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_2_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_2_payment_title');
        $setting->setValue('[Notify] После второй оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_2_payment_text');
        $setting->setValue('[Notify] После второй оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_2_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_2_payment_email_title');
        $setting->setValue('[Email] После второй оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_2_payment_email_text');
        $setting->setValue('[Email] После второй оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_3_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_3_payment_title');
        $setting->setValue('[Notify] После третьей оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_3_payment_text');
        $setting->setValue('[Notify] После третьей оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_3_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_3_payment_email_title');
        $setting->setValue('[Email] После третьей оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_3_payment_email_text');
        $setting->setValue('[Email] После третьей оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_payment_title');
        $setting->setValue('[Notify] После любой другой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_payment_text');
        $setting->setValue('[Notify] После любой другой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_payment_email_title');
        $setting->setValue('[Email] После любой другой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_payment_email_text');
        $setting->setValue('[Email] После любой другой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_1_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_1_payment_title');
        $setting->setValue('[Notify] До окончания периода после первой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_1_payment_text');
        $setting->setValue('[Notify] До окончания периода после первой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_1_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_1_payment_email_title');
        $setting->setValue('[Email] До окончания периода после первой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_1_payment_email_text');
        $setting->setValue('[Email] До окончания периода после первой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_1_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_1_payment_title');
        $setting->setValue('[Email] После окончания периода после первой оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_1_payment_text');
        $setting->setValue('[Email] После окончания периода после первой оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_popup_title');
        $setting->setValue('[Popup] До окончания периода после второй оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_popup_text');
        $setting->setValue('[Popup] До окончания периода после второй оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_title');
        $setting->setValue('[Notify] До окончания периода после второй оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_text');
        $setting->setValue('[Notify] До окончания периода после второй оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_email_title');
        $setting->setValue('[Email] До окончания периода после второй оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_2_payment_email_text');
        $setting->setValue('[Email] До окончания периода после второй оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_2_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_2_payment_title');
        $setting->setValue('[Email] После окончания периода после второй оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_2_payment_text');
        $setting->setValue('[Email] После окончания периода после второй оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_3_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_3_payment_title');
        $setting->setValue('[Notify] До окончания периода после третьей оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_3_payment_text');
        $setting->setValue('[Notify] До окончания периода после третьей оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_3_payment_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_3_payment_email_title');
        $setting->setValue('[Email] До окончания периода после третьей оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('before_access_time_end_after_3_payment_email_text');
        $setting->setValue('[Email] До окончания периода после третьей оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_3_payment_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_3_payment_title');
        $setting->setValue('[Email] После окончания периода после третьей оплаты {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_access_time_end_after_3_payment_text');
        $setting->setValue('[Email] После окончания периода после третьей оплаты {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('landing_title');
        $setting->setValue('Title');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('landing_keywords');
        $setting->setValue('Meta keywords');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('landing_description');
        $setting->setValue('Meta description');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('article_styles');
        $setting->setValue('');
        $setting->setType('string');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('opacity_not_active_filials');
        $setting->setValue(30);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('opacity_not_active_sites');
        $setting->setValue(30);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_tickets');
        $setting->setValue(2);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_shuffle');
        $setting->setValue(false);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_questions_in_ticket');
        $setting->setValue(20);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_not_repeat_questions_in_tickets');
        $setting->setValue(false);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_max_errors_in_ticket');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_ticket_time');
        $setting->setValue(10);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_shuffle_answers');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('exam_retake_time');
        $setting->setValue(24);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_success');
        $setting->setValue('Сообщения в экзаменах {Верно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_error');
        $setting->setValue('Сообщения в экзаменах {Неверно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_timeout');
        $setting->setValue('Сообщения в экзаменах {Время закончилось}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_complete');
        $setting->setValue('Сообщения в экзаменах {Экзамен завершён}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_long_time');
        $setting->setValue('Сообщения в экзаменах {Долго отсутствовал}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_max_errors');
        $setting->setValue('Сообщения в экзаменах {Много ошибок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_exam_retake');
        $setting->setValue('Сообщения в экзаменах {Пересдача не возможна}');
        $manager->persist($setting);

        foreach ($subjects as $subject) {
            $setting = new Setting();
            $setting->setKey('after_all_slices_'.$subject->getId().'_enabled');
            $setting->setValue(true);
            $setting->setType('boolean');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_all_slices_'.$subject->getId().'_title');
            $setting->setValue('[Notify] После всех срезов по предмету «'.$subject->getTitle().'» {Заголовок}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_all_slices_'.$subject->getId().'_text');
            $setting->setValue('[Notify] После всех срезов по предмету «'.$subject->getTitle().'» {Текст}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_all_slices_'.$subject->getId().'_email_enabled');
            $setting->setValue(true);
            $setting->setType('boolean');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_all_slices_'.$subject->getId().'_email_title');
            $setting->setValue('[Email] После всех срезов по предмету «'.$subject->getTitle().'» {Заголовок}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_all_slices_'.$subject->getId().'_email_text');
            $setting->setValue('[Email] После всех срезов по предмету «'.$subject->getTitle().'» {Текст}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_fail_exam_'.$subject->getId().'_enabled');
            $setting->setValue(true);
            $setting->setType('boolean');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_fail_exam_'.$subject->getId().'_title');
            $setting->setValue('[Notify] После провала экзамена по предмету «'.$subject->getTitle().'» {Заголовок}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_fail_exam_'.$subject->getId().'_text');
            $setting->setValue('[Notify] После провала экзамена по предмету «'.$subject->getTitle().'» {Текст}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_fail_exam_'.$subject->getId().'_email_enabled');
            $setting->setValue(true);
            $setting->setType('boolean');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_fail_exam_'.$subject->getId().'_email_title');
            $setting->setValue('[Email] После провала экзамена по предмету «'.$subject->getTitle().'» {Заголовок}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_fail_exam_'.$subject->getId().'_email_text');
            $setting->setValue('[Email] После провала экзамена по предмету «'.$subject->getTitle().'» {Текст}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_exam_'.$subject->getId().'_enabled');
            $setting->setValue(true);
            $setting->setType('boolean');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_exam_'.$subject->getId().'_title');
            $setting->setValue('[Notify] После экзамена по предмету «'.$subject->getTitle().'» {Заголовок}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_exam_'.$subject->getId().'_text');
            $setting->setValue('[Notify] После экзамена по предмету «'.$subject->getTitle().'» {Текст}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_exam_'.$subject->getId().'_email_enabled');
            $setting->setValue(true);
            $setting->setType('boolean');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_exam_'.$subject->getId().'_email_title');
            $setting->setValue('[Email] После экзамена по предмету «'.$subject->getTitle().'» {Заголовок}');
            $manager->persist($setting);

            $setting = new Setting();
            $setting->setKey('after_exam_'.$subject->getId().'_email_text');
            $setting->setValue('[Email] После экзамена по предмету «'.$subject->getTitle().'» {Текст}');
            $manager->persist($setting);
        }

        $setting = new Setting();
        $setting->setKey('theme_test_correct_answers');
        $setting->setValue(2);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('theme_test_correct_answers_in_row');
        $setting->setValue(false);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('theme_test_questions_method');
        $setting->setValue('shuffle');
        $setting->setType('string');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('theme_test_time');
        $setting->setValue(0);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('theme_test_shuffle_answers');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_theme_test_success');
        $setting->setValue('Сообщения в тестировании после темы {Верно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_theme_test_error');
        $setting->setValue('Сообщения в тестировании после темы {Неверно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_theme_test_timeout');
        $setting->setValue('Сообщения в тестировании после темы {Время закончилось}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_theme_test_complete_next');
        $setting->setValue('Сообщения в тестировании после темы {Тестирование завершено, но есть следующая тема}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_theme_test_complete_list');
        $setting->setValue('Сообщения в тестировании после темы {Тестирование завершено, но нет следующей темы}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_theme_test_long_time');
        $setting->setValue('Сообщения в тестировании после темы {Долго отсутствовал}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('slice_tickets');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('slice_questions_in_ticket');
        $setting->setValue(20);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('slice_not_repeat_questions_in_tickets');
        $setting->setValue(false);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('slice_max_errors_in_ticket');
        $setting->setValue(2);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('slice_ticket_time');
        $setting->setValue(0);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('slice_shuffle_answers');
        $setting->setValue(1);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_slice_success');
        $setting->setValue('Сообщения в срезах {Верно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_slice_error');
        $setting->setValue('Сообщения в срезах {Неверно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_slice_timeout');
        $setting->setValue('Сообщения в срезах {Время закончилось}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_slice_complete');
        $setting->setValue('Сообщения в срезах {Срез завершён}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_slice_long_time');
        $setting->setValue('Сообщения в срезах {Долго отсутствовал}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_slice_max_errors');
        $setting->setValue('Сообщения в срезах {Много ошибок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_1_tickets');
        $setting->setValue(2);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_1_shuffle');
        $setting->setValue(false);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_1_max_errors_in_ticket');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_1_ticket_time');
        $setting->setValue(10);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_1_shuffle_answers');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_2_tickets');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_2_questions_in_ticket');
        $setting->setValue(10);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_2_not_repeat_questions_in_tickets');
        $setting->setValue(false);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_2_max_errors_in_ticket');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_2_ticket_time');
        $setting->setValue(10);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('final_exam_2_shuffle_answers');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_description');
        $setting->setValue('Сообщения в финальном экзамене {Описание}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_success');
        $setting->setValue('Сообщения в финальном экзамене {Верно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_error');
        $setting->setValue('Сообщения в финальном экзамене {Неверно ответили на вопрос}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_timeout');
        $setting->setValue('Сообщения в финальном экзамене {Время закончилось}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_complete');
        $setting->setValue('Сообщения в финальном экзамене {Финальный экзамен завершён}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_long_time');
        $setting->setValue('Сообщения в финальном экзамене {Долго отсутствовал}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_max_errors');
        $setting->setValue('Сообщения в финальном экзамене {Много ошибок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_retake');
        $setting->setValue('Сообщения в финальном экзамене {Пересдача не возможна}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_denied');
        $setting->setValue('Сообщения в финальном экзамене {Не доступен}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('training_final_exam_passed');
        $value = 'Сообщения в финальном экзамене';
        $value .= ' {Уже прошли (%certificate_link% заменится на адрес для скачивания сертификата)}';
        $setting->setValue($value);
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_all_exams_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_all_exams_title');
        $setting->setValue('[Notify] После всех экзаменов по предметам {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_all_exams_text');
        $setting->setValue('[Notify] После всех экзаменов по предметам {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_all_exams_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_all_exams_email_title');
        $setting->setValue('[Email] После всех экзаменов по предметам {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_all_exams_email_text');
        $setting->setValue('[Email] После всех экзаменов по предметам {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('close_final_exam_title');
        $setting->setValue('[Popup] Заблокирован доступ к Итоговому экзамену {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('close_final_exam_text');
        $setting->setValue('[Popup] Заблокирован доступ к Итоговому экзамену {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('closed_subject_title');
        $setting->setValue('[Popup] Заблокирован доступ к Предмету {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('closed_subject_text');
        $setting->setValue('[Popup] Заблокирован доступ к Предмету {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_final_exam_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_final_exam_title');
        $setting->setValue('[Notify] После финального экзамена {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_final_exam_text');
        $setting->setValue('[Notify] После финального экзамена {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_final_exam_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_final_exam_email_title');
        $setting->setValue('[Email] После финального экзамена {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('after_final_exam_email_text');
        $setting->setValue('[Email] После финального экзамена {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('add_user_title');
        $setting->setValue('[Email] Письмо пользователю, созданного через админку {Заголовок}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('add_user_text');
        $setting->setValue('[Email] Письмо пользователю, созданного через админку {Текст}');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_days_to_answer');
        $setting->setValue(3);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_answered_email_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_answered_email_title');
        $setting->setValue('Оповещение об ответе { Заголовок }');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_answered_email_text');
        $setting->setValue('Оповещение об ответе { Текст }');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_answered_email_admin_enabled');
        $setting->setValue(true);
        $setting->setType('boolean');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_answered_email_admin_title');
        $setting->setValue('Оповещение о новом диалоге от администрации { Заголовок }');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('support_answered_email_admin_text');
        $setting->setValue('Оповещение о новом диалоге от администрации { Текст }');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_1');
        $setting->setValue(7);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_2');
        $setting->setValue(14);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_3');
        $setting->setValue(21);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_4');
        $setting->setValue(28);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_5');
        $setting->setValue(35);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_expiration_1');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_expiration_2');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_expiration_3');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_expiration_4');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_expiration_5');
        $setting->setValue(1);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_discount_1');
        $setting->setValue(500);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_discount_2');
        $setting->setValue(400);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_discount_3');
        $setting->setValue(300);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_discount_4');
        $setting->setValue(200);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('notify_no_payments_promo_discount_5');
        $setting->setValue(100);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('opacity_not_active_pass_filials');
        $setting->setValue(30);
        $setting->setType('integer');
        $manager->persist($setting);

        $setting = new Setting();
        $setting->setKey('pass_time_recreating');
        $setting->setValue(24);
        $setting->setType('integer');
        $manager->persist($setting);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
