<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170222000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("closed_subject_title", "Заблокирован доступ к Предмету {Заголовок}", "string");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("closed_subject_text", "Заблокирован доступ к Предмету {Текст}", "string");');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE users DROP close_final_exam');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="closed_subject_title";');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="closed_subject_text";');
    }
}
