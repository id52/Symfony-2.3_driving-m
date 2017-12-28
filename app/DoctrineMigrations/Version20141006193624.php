<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141006193624 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('support_days_to_answer', '5', 'integer');");
        $this->addSql("INSERT INTO `settings_notifies` (`_key`, `value`, `type`) VALUES
                            ('support_answered_email_enabled', '1', 'boolean'),
                            ('support_answered_email_title', 'Title for support answer notice email', 'string'),
                            ('support_answered_text', 'Text for notice about support answer', 'string');");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("DELETE FROM `settings` WHERE `_key`='support_days_to_answer';");
        $this->addSql("DELETE FROM `settings_notifies` WHERE `_key`='support_answered_email_enabled' OR `_key`='support_answered_email_title' OR `_key`='support_answered_text';");

    }
}
