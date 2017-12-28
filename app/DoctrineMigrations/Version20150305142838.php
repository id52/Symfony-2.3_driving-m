<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150305142838 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE filials (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, station VARCHAR(255) DEFAULT NULL, work_time VARCHAR(255) DEFAULT NULL, phones LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', emails LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', coords LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', address_geo VARCHAR(255) DEFAULT NULL, map_code LONGTEXT DEFAULT NULL, counters_code LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_94C7FCCF47645AE (url), INDEX IDX_94C7FCC98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE filials ADD CONSTRAINT FK_94C7FCC98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE filials');
    }
}
