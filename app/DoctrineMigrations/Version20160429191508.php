<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160429191508 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE offers_prices_students (at TINYINT(1) NOT NULL, category_id INT NOT NULL, region_id INT NOT NULL, offer_student_id INT NOT NULL, price INT NOT NULL, INDEX IDX_6BDCD1F012469DE2 (category_id), INDEX IDX_6BDCD1F098260155 (region_id), INDEX IDX_6BDCD1F0573E8CCE (offer_student_id), PRIMARY KEY(at, category_id, region_id, offer_student_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offers_students (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, _desc LONGTEXT NOT NULL, description LONGTEXT NOT NULL, started_at DATE DEFAULT NULL, ended_at DATE DEFAULT NULL, is_public TINYINT(1) NOT NULL, is_student TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offers_prices_students ADD CONSTRAINT FK_6BDCD1F012469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE offers_prices_students ADD CONSTRAINT FK_6BDCD1F098260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE offers_prices_students ADD CONSTRAINT FK_6BDCD1F0573E8CCE FOREIGN KEY (offer_student_id) REFERENCES offers_students (id)');
        $this->addSql('ALTER TABLE images ADD offer_student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A573E8CCE FOREIGN KEY (offer_student_id) REFERENCES offers_students (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A573E8CCE ON images (offer_student_id)');
        $this->addSql('ALTER TABLE offers DROP is_student');
        $this->addSql('ALTER TABLE offers_students DROP is_student');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A573E8CCE');
        $this->addSql('ALTER TABLE offers_prices_students DROP FOREIGN KEY FK_6BDCD1F0573E8CCE');
        $this->addSql('DROP TABLE offers_prices_students');
        $this->addSql('DROP TABLE offers_students');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A573E8CCE ON images');
        $this->addSql('ALTER TABLE images DROP offer_student_id');
        $this->addSql('ALTER TABLE offers ADD is_student TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE offers_students ADD is_student TINYINT(1) NOT NULL');
    }
}
