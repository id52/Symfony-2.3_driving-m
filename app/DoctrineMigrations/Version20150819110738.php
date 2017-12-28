<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150819110738 extends AbstractMigration
{
    public function up(Schema $schema)

    {
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('exam_questions_in_ticket', '10', 'integer');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('exam_not_repeat_questions_in_tickets', '0', 'boolean');");
    }

    public function down(Schema $schema)

    {
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'exam_questions_in_ticket';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'exam_not_repeat_questions_in_tickets';");
    }
}
