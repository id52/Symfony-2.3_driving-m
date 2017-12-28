<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150918152903 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, patronymic VARCHAR(255) DEFAULT NULL, sex ENUM(\'male\', \'female\') DEFAULT NULL COMMENT \'(DC2Type:enumsex)\', birthday DATE DEFAULT NULL, message LONGTEXT NOT NULL, moderated TINYINT(1) DEFAULT NULL, social VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_6970EB0F12469DE2 (category_id), INDEX IDX_6970EB0FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE images ADD review_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A3E2E969B FOREIGN KEY (review_id) REFERENCES reviews (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A3E2E969B ON images (review_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A3E2E969B');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A3E2E969B ON images');
        $this->addSql('ALTER TABLE images DROP review_id');
    }
}
