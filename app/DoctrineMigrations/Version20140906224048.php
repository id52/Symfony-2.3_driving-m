<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140906224048 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type ENUM(\'site_access\', \'training\') DEFAULT NULL COMMENT \'(DC2Type:enumservice)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE final_exams_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, questions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', passed TINYINT(1) NOT NULL, INDEX IDX_8CB1316FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test_emails (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions_places (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_B2578D4898260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_prices (category_id INT NOT NULL, region_id INT NOT NULL, price INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_EB23A72212469DE2 (category_id), INDEX IDX_EB23A72298260155 (region_id), PRIMARY KEY(category_id, region_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback_emails (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, subjects LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_old_mobile_phones (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, phone VARCHAR(255) NOT NULL, INDEX IDX_6EE221D7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, discount_1_amount INT NOT NULL, discount_1_date_from DATE DEFAULT NULL, discount_1_date_to DATE DEFAULT NULL, discount_1_timer_period INT NOT NULL, discount_2_first_amount INT NOT NULL, discount_2_first_days INT NOT NULL, discount_2_second_amount INT NOT NULL, discount_2_second_days INT NOT NULL, discount_2_between_period_days INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo_key (id INT AUTO_INCREMENT NOT NULL, promo_id INT DEFAULT NULL, active TINYINT(1) DEFAULT NULL, created DATETIME NOT NULL, activated DATETIME DEFAULT NULL, discount INT NOT NULL, hash VARCHAR(128) NOT NULL, type ENUM(\'site_access\', \'training\') DEFAULT NULL COMMENT \'(DC2Type:enumservice)\', UNIQUE INDEX UNIQ_CC96ECCDD1B862B8 (hash), INDEX IDX_CC96ECCDD0C07AFF (promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, category_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, file VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E01FBE6A8C9F3610 (file), UNIQUE INDEX UNIQ_E01FBE6A1E27F6BF (question_id), UNIQUE INDEX UNIQ_E01FBE6A12469DE2 (category_id), UNIQUE INDEX UNIQ_E01FBE6A23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, theme_id INT DEFAULT NULL, num VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_8ADC54D559027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (_key VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(_key)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mailing (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, date DATE NOT NULL, filters LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', users LONGTEXT NOT NULL, forceSending TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings_notifies (_key VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(_key)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slices_logs (id INT AUTO_INCREMENT NOT NULL, slice_id INT DEFAULT NULL, user_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, questions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', passed TINYINT(1) NOT NULL, INDEX IDX_5688AC306F7A2797 (slice_id), INDEX IDX_5688AC30A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, position INT NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifies (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, readed TINYINT(1) NOT NULL, sended_at DATETIME NOT NULL, INDEX IDX_51E1EC13A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback_teacher_emails (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subjects (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, brief_description VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_pdd TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, keywords VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, top_menu TINYINT(1) NOT NULL, bottom_menu TINYINT(1) NOT NULL, private TINYINT(1) NOT NULL, position INT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BFDD3168F47645AE (url), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webgroups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themes (id INT AUTO_INCREMENT NOT NULL, subject_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, position INT NOT NULL, INDEX IDX_154232DE23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themes_readers (theme_id INT NOT NULL, reader_id INT NOT NULL, INDEX IDX_861E0EB859027487 (theme_id), INDEX IDX_861E0EB81717D737 (reader_id), PRIMARY KEY(theme_id, reader_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) NOT NULL, created DATETIME NOT NULL, used_from DATETIME NOT NULL, used_to DATETIME NOT NULL, active TINYINT(1) NOT NULL, restricted ENUM(\'users\', \'keys\') NOT NULL COMMENT \'(DC2Type:enumpromorestricted)\', maxUsers INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services_prices (service_id INT NOT NULL, region_id INT NOT NULL, price INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_7CA5AF5FED5CA9E6 (service_id), INDEX IDX_7CA5AF5F98260155 (region_id), PRIMARY KEY(service_id, region_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, required_notify_id INT DEFAULT NULL, category_id INT DEFAULT NULL, region_id INT DEFAULT NULL, region_place_id INT DEFAULT NULL, webgroup_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, certificate VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, patronymic VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, photo_coords LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', sex ENUM(\'male\', \'female\') DEFAULT NULL COMMENT \'(DC2Type:enumsex)\', birthday DATE DEFAULT NULL, birth_country VARCHAR(255) DEFAULT NULL, birth_region VARCHAR(255) DEFAULT NULL, birth_city VARCHAR(255) DEFAULT NULL, foreign_passport TINYINT(1) NOT NULL, foreign_passport_number VARCHAR(255) DEFAULT NULL, passport_number VARCHAR(255) DEFAULT NULL, passport_rovd VARCHAR(255) DEFAULT NULL, passport_rovd_number VARCHAR(255) DEFAULT NULL, passport_rovd_date DATE DEFAULT NULL, not_registration TINYINT(1) NOT NULL, registration_country VARCHAR(255) DEFAULT NULL, registration_region VARCHAR(255) DEFAULT NULL, registration_city VARCHAR(255) DEFAULT NULL, registration_street VARCHAR(255) DEFAULT NULL, registration_house VARCHAR(255) DEFAULT NULL, registration_stroenie VARCHAR(255) DEFAULT NULL, registration_korpus VARCHAR(255) DEFAULT NULL, registration_apartament VARCHAR(255) DEFAULT NULL, place_country VARCHAR(255) DEFAULT NULL, place_region VARCHAR(255) DEFAULT NULL, place_city VARCHAR(255) DEFAULT NULL, place_street VARCHAR(255) DEFAULT NULL, place_house VARCHAR(255) DEFAULT NULL, place_stroenie VARCHAR(255) DEFAULT NULL, place_korpus VARCHAR(255) DEFAULT NULL, place_apartament VARCHAR(255) DEFAULT NULL, work_place VARCHAR(255) DEFAULT NULL, work_position VARCHAR(255) DEFAULT NULL, phone_home VARCHAR(255) DEFAULT NULL, phone_mobile VARCHAR(255) DEFAULT NULL, phone_mobile_status ENUM(\'sended\', \'confirmed\') DEFAULT NULL COMMENT \'(DC2Type:enummobilestatus)\', phone_mobile_code VARCHAR(255) DEFAULT NULL, notifies_cnt INT NOT NULL, paid_notified_at DATE NOT NULL, payment_1_paid DATE DEFAULT NULL, payment_1_paid_not_notify TINYINT(1) NOT NULL, payment_2_paid DATE DEFAULT NULL, payment_2_paid_not_notify TINYINT(1) NOT NULL, promo_used TINYINT(1) DEFAULT NULL, white_ips LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', moderated TINYINT(1) NOT NULL, paradox_id INT DEFAULT NULL, discount_2_notify_first TINYINT(1) NOT NULL, discount_2_notify_second TINYINT(1) NOT NULL, mailing TINYINT(1) NOT NULL, offline TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_1483A5E9BB0A6438 (required_notify_id), INDEX IDX_1483A5E912469DE2 (category_id), INDEX IDX_1483A5E998260155 (region_id), INDEX IDX_1483A5E97E61A6EB (region_place_id), INDEX IDX_1483A5E962B396A4 (webgroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themes_tests_logs (id INT AUTO_INCREMENT NOT NULL, theme_id INT DEFAULT NULL, user_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, questions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', passed TINYINT(1) NOT NULL, INDEX IDX_4041A00F59027487 (theme_id), INDEX IDX_4041A00FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exams_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, questions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', passed TINYINT(1) NOT NULL, INDEX IDX_8F2175BAA76ED395 (user_id), INDEX IDX_8F2175BA23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slices (id INT AUTO_INCREMENT NOT NULL, after_theme_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_BD8F3614BC7E206 (after_theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tests_knowledge_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, questions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', passed TINYINT(1) NOT NULL, INDEX IDX_8D104917A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tests_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, questions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', passed TINYINT(1) NOT NULL, INDEX IDX_D61AD501A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payments_logs (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, promo_key_id INT DEFAULT NULL, s_type ENUM(\'robokassa\') NOT NULL COMMENT \'(DC2Type:enumpayment)\', s_id VARCHAR(255) DEFAULT NULL, sum INT NOT NULL, paid TINYINT(1) NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8A8D45B8539B0606 (uid), INDEX IDX_8A8D45B814914A7E (promo_key_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sms_uslugi_ru_logs (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, s_id VARCHAR(255) DEFAULT NULL, s_answer LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE final_exams_logs ADD CONSTRAINT FK_8CB1316FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE regions_places ADD CONSTRAINT FK_B2578D4898260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE category_prices ADD CONSTRAINT FK_EB23A72212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE category_prices ADD CONSTRAINT FK_EB23A72298260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE users_old_mobile_phones ADD CONSTRAINT FK_6EE221D7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE promo_key ADD CONSTRAINT FK_CC96ECCDD0C07AFF FOREIGN KEY (promo_id) REFERENCES promo (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A23EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D559027487 FOREIGN KEY (theme_id) REFERENCES themes (id)');
        $this->addSql('ALTER TABLE slices_logs ADD CONSTRAINT FK_5688AC306F7A2797 FOREIGN KEY (slice_id) REFERENCES slices (id)');
        $this->addSql('ALTER TABLE slices_logs ADD CONSTRAINT FK_5688AC30A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notifies ADD CONSTRAINT FK_51E1EC13A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE themes ADD CONSTRAINT FK_154232DE23EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE themes_readers ADD CONSTRAINT FK_861E0EB859027487 FOREIGN KEY (theme_id) REFERENCES themes (id)');
        $this->addSql('ALTER TABLE themes_readers ADD CONSTRAINT FK_861E0EB81717D737 FOREIGN KEY (reader_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE services_prices ADD CONSTRAINT FK_7CA5AF5FED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE services_prices ADD CONSTRAINT FK_7CA5AF5F98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9BB0A6438 FOREIGN KEY (required_notify_id) REFERENCES notifies (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E912469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E998260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E97E61A6EB FOREIGN KEY (region_place_id) REFERENCES regions_places (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E962B396A4 FOREIGN KEY (webgroup_id) REFERENCES webgroups (id)');
        $this->addSql('ALTER TABLE themes_tests_logs ADD CONSTRAINT FK_4041A00F59027487 FOREIGN KEY (theme_id) REFERENCES themes (id)');
        $this->addSql('ALTER TABLE themes_tests_logs ADD CONSTRAINT FK_4041A00FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE exams_logs ADD CONSTRAINT FK_8F2175BAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE exams_logs ADD CONSTRAINT FK_8F2175BA23EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE slices ADD CONSTRAINT FK_BD8F3614BC7E206 FOREIGN KEY (after_theme_id) REFERENCES themes (id)');
        $this->addSql('ALTER TABLE tests_knowledge_logs ADD CONSTRAINT FK_8D104917A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tests_logs ADD CONSTRAINT FK_D61AD501A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE payments_logs ADD CONSTRAINT FK_8A8D45B8539B0606 FOREIGN KEY (uid) REFERENCES users (id)');
        $this->addSql('ALTER TABLE payments_logs ADD CONSTRAINT FK_8A8D45B814914A7E FOREIGN KEY (promo_key_id) REFERENCES promo_key (id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE services_prices DROP FOREIGN KEY FK_7CA5AF5FED5CA9E6');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E97E61A6EB');
        $this->addSql('ALTER TABLE regions_places DROP FOREIGN KEY FK_B2578D4898260155');
        $this->addSql('ALTER TABLE category_prices DROP FOREIGN KEY FK_EB23A72298260155');
        $this->addSql('ALTER TABLE services_prices DROP FOREIGN KEY FK_7CA5AF5F98260155');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E998260155');
        $this->addSql('ALTER TABLE payments_logs DROP FOREIGN KEY FK_8A8D45B814914A7E');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A1E27F6BF');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9BB0A6438');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A23EDC87');
        $this->addSql('ALTER TABLE themes DROP FOREIGN KEY FK_154232DE23EDC87');
        $this->addSql('ALTER TABLE exams_logs DROP FOREIGN KEY FK_8F2175BA23EDC87');
        $this->addSql('ALTER TABLE category_prices DROP FOREIGN KEY FK_EB23A72212469DE2');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A12469DE2');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E912469DE2');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E962B396A4');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D559027487');
        $this->addSql('ALTER TABLE themes_readers DROP FOREIGN KEY FK_861E0EB859027487');
        $this->addSql('ALTER TABLE themes_tests_logs DROP FOREIGN KEY FK_4041A00F59027487');
        $this->addSql('ALTER TABLE slices DROP FOREIGN KEY FK_BD8F3614BC7E206');
        $this->addSql('ALTER TABLE promo_key DROP FOREIGN KEY FK_CC96ECCDD0C07AFF');
        $this->addSql('ALTER TABLE final_exams_logs DROP FOREIGN KEY FK_8CB1316FA76ED395');
        $this->addSql('ALTER TABLE users_old_mobile_phones DROP FOREIGN KEY FK_6EE221D7A76ED395');
        $this->addSql('ALTER TABLE slices_logs DROP FOREIGN KEY FK_5688AC30A76ED395');
        $this->addSql('ALTER TABLE notifies DROP FOREIGN KEY FK_51E1EC13A76ED395');
        $this->addSql('ALTER TABLE themes_readers DROP FOREIGN KEY FK_861E0EB81717D737');
        $this->addSql('ALTER TABLE themes_tests_logs DROP FOREIGN KEY FK_4041A00FA76ED395');
        $this->addSql('ALTER TABLE exams_logs DROP FOREIGN KEY FK_8F2175BAA76ED395');
        $this->addSql('ALTER TABLE tests_knowledge_logs DROP FOREIGN KEY FK_8D104917A76ED395');
        $this->addSql('ALTER TABLE tests_logs DROP FOREIGN KEY FK_D61AD501A76ED395');
        $this->addSql('ALTER TABLE payments_logs DROP FOREIGN KEY FK_8A8D45B8539B0606');
        $this->addSql('ALTER TABLE slices_logs DROP FOREIGN KEY FK_5688AC306F7A2797');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE final_exams_logs');
        $this->addSql('DROP TABLE test_emails');
        $this->addSql('DROP TABLE regions_places');
        $this->addSql('DROP TABLE category_prices');
        $this->addSql('DROP TABLE feedback_emails');
        $this->addSql('DROP TABLE users_old_mobile_phones');
        $this->addSql('DROP TABLE regions');
        $this->addSql('DROP TABLE promo_key');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE mailing');
        $this->addSql('DROP TABLE settings_notifies');
        $this->addSql('DROP TABLE slices_logs');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE notifies');
        $this->addSql('DROP TABLE feedback_teacher_emails');
        $this->addSql('DROP TABLE subjects');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE webgroups');
        $this->addSql('DROP TABLE themes');
        $this->addSql('DROP TABLE themes_readers');
        $this->addSql('DROP TABLE promo');
        $this->addSql('DROP TABLE services_prices');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE themes_tests_logs');
        $this->addSql('DROP TABLE exams_logs');
        $this->addSql('DROP TABLE slices');
        $this->addSql('DROP TABLE tests_knowledge_logs');
        $this->addSql('DROP TABLE tests_logs');
        $this->addSql('DROP TABLE payments_logs');
        $this->addSql('DROP TABLE sms_uslugi_ru_logs');
    }
}
