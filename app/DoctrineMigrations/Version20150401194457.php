<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150401194457 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE images ADD offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A53C674EE FOREIGN KEY (offer_id) REFERENCES offers (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A53C674EE ON images (offer_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A53C674EE');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A53C674EE ON images');
        $this->addSql('ALTER TABLE images DROP offer_id');
    }
}
