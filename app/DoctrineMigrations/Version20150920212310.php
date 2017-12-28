<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150920212310 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("support_answered_email_admin_enabled", 1, "boolean");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("support_answered_email_admin_title", "Оповещение о новом диалоге от администрации { Заголовок }", "string");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("support_answered_email_admin_text", "Оповещение о новом диалоге от администрации { Текст }", "string");');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM `settings` WHERE `_key`="support_answered_email_admin_enabled";');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="support_answered_email_admin_title";');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="support_answered_email_admin_text";');
    }
}
