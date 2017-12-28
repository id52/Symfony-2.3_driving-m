<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150914092052 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users ADD popup_info LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('UPDATE users SET popup_info="a:0:{}"');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("access_time_end_popup_after_2_payment_1", "1", "integer");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("access_time_end_popup_after_2_payment_2", "3", "integer");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("access_time_end_popup_after_2_payment_3", "7", "integer");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("access_time_end_popup_after_2_payment_4", "14", "integer");');
        for ($i = 5; $i <= 10; $i ++) {
            $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("access_time_end_popup_after_2_payment_'.$i.'", "0", "integer");');
        }
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("before_access_time_end_after_2_payment_popup_title", "[Popup] До окончания периода после второй оплаты {Заголовок}", "string");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("before_access_time_end_after_2_payment_popup_text", "[Popup] До окончания периода после второй оплаты {Текст}", "string");');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP popup_info');
        for ($i = 1; $i <= 10; $i ++) {
            $this->addSql('DELETE FROM `settings` WHERE `_key`="access_time_end_popup_after_2_payment_'.$i.'";');
        }
        $this->addSql('DELETE FROM `settings` WHERE `_key`="before_access_time_end_after_2_payment_popup_title";');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="before_access_time_end_after_2_payment_popup_text";');
    }
}
