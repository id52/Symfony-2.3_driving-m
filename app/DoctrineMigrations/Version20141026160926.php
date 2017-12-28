<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141026160926 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE users_confirmation (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, hash VARCHAR(40) NOT NULL, sms_code VARCHAR(255) NOT NULL, last_sent DATETIME NOT NULL, UNIQUE INDEX UNIQ_1B793430D1B862B8 (hash), INDEX IDX_1B793430539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_confirmation ADD CONSTRAINT FK_1B793430539B0606 FOREIGN KEY (uid) REFERENCES users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE users_confirmation');
    }
}
