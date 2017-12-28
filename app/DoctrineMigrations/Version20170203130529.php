<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170203130529 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE infos (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, release_time DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE infos_readers (info_id INT NOT NULL, reader_id INT NOT NULL, INDEX IDX_331567A55D8BC1F8 (info_id), INDEX IDX_331567A51717D737 (reader_id), PRIMARY KEY(info_id, reader_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE infos_readers ADD CONSTRAINT FK_331567A55D8BC1F8 FOREIGN KEY (info_id) REFERENCES infos (id)');
        $this->addSql('ALTER TABLE infos_readers ADD CONSTRAINT FK_331567A51717D737 FOREIGN KEY (reader_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infos_readers DROP FOREIGN KEY FK_331567A55D8BC1F8');
        $this->addSql('DROP TABLE infos');
        $this->addSql('DROP TABLE infos_readers');
    }
}
