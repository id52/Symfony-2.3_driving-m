<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150924141254 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE how_work (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, _desc LONGTEXT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD how_work_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A9FE05ABF FOREIGN KEY (how_work_id) REFERENCES how_work (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A9FE05ABF ON images (how_work_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A9FE05ABF');
        $this->addSql('DROP TABLE how_work');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A9FE05ABF ON images');
        $this->addSql('ALTER TABLE images DROP how_work_id');
    }
}
