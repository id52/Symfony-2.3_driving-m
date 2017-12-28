<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150518125039 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE sites (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, coords LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', counters_code LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_BC00AA63F47645AE (url), INDEX IDX_BC00AA6398260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sites ADD CONSTRAINT FK_BC00AA6398260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE images ADD site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AF6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6AF6BD1646 ON images (site_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AF6BD1646');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP INDEX UNIQ_E01FBE6AF6BD1646 ON images');
        $this->addSql('ALTER TABLE images DROP site_id');
    }
}
