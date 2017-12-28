<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170425042527 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE questions_i18n (id INT AUTO_INCREMENT NOT NULL, locale_id INT DEFAULT NULL, question_id INT DEFAULT NULL, text LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_E2F6BCC4E559DFD1 (locale_id), INDEX IDX_E2F6BCC41E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locales (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE questions_i18n ADD CONSTRAINT FK_E2F6BCC4E559DFD1 FOREIGN KEY (locale_id) REFERENCES locales (id)');
        $this->addSql('ALTER TABLE questions_i18n ADD CONSTRAINT FK_E2F6BCC41E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('INSERT INTO `locales` (`id`, `code`) VALUES (NULL, \'uz\'), (NULL, \'tj\');');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE questions_i18n DROP FOREIGN KEY FK_E2F6BCC4E559DFD1');
        $this->addSql('DROP TABLE questions_i18n');
        $this->addSql('DROP TABLE locales');
    }
}
