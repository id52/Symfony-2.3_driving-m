<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150515135515 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE support_categories ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE support_categories ADD CONSTRAINT FK_1847824AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1847824AA76ED395 ON support_categories (user_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE support_categories DROP FOREIGN KEY FK_1847824AA76ED395');
        $this->addSql('DROP INDEX UNIQ_1847824AA76ED395 ON support_categories');
        $this->addSql('ALTER TABLE support_categories DROP user_id');
    }
}
