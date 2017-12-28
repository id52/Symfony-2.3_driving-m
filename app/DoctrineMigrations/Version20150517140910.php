<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150517140910 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE support_dialogs ADD theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE support_dialogs ADD CONSTRAINT FK_5C2A65B59027487 FOREIGN KEY (theme_id) REFERENCES themes (id)');
        $this->addSql('CREATE INDEX IDX_5C2A65B59027487 ON support_dialogs (theme_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE support_dialogs DROP FOREIGN KEY FK_5C2A65B59027487');
        $this->addSql('DROP INDEX IDX_5C2A65B59027487 ON support_dialogs');
        $this->addSql('ALTER TABLE support_dialogs DROP theme_id');
    }
}
