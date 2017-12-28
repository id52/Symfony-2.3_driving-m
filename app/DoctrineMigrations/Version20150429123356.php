<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150429123356 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE filials ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE images ADD filial_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A299B2577 FOREIGN KEY (filial_id) REFERENCES filials (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A299B2577 ON images (filial_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE filials DROP active');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A299B2577');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A299B2577 ON images');
        $this->addSql('ALTER TABLE images DROP filial_id');
    }
}
