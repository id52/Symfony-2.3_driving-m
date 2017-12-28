<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151030092937 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE users ADD close_final_exam TINYINT(1) NOT NULL');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("close_final_exam_title", "Заблокирован доступ к Итоговому экзамену {Заголовок}", "string");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("close_final_exam_text", "Заблокирован доступ к Итоговому экзамену {Текст}", "string");');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE users DROP close_final_exam');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="close_final_exam_title";');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="close_final_exam_text";');
    }
}
