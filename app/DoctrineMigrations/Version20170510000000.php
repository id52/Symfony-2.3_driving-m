<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170510000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('max_errors_questions_text', 'Вы допустили максимальное количество ошибок в основных вопросах.', 'string');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('max_errors_questions_block_text', 'Вы допустили максимальное количество ошибок в основных вопросах в одном блоке.', 'string');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('max_errors_additional_questions_text', 'Вы допустили максимальное количество ошибок в дополнительных вопросах.', 'string');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('max_errors_ticket_text', 'Вы допустили максимальное количество ошибок в вопросах дополнительного билета.', 'string');");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'max_errors_questions_text';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'max_errors_questions_block_text';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'max_errors_additional_questions_text';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'max_errors_ticket_text';");

    }
}
