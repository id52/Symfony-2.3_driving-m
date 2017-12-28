<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150518153847 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_BC00AA63F47645AE ON sites');
        $this->addSql('ALTER TABLE sites DROP url, DROP address, DROP counters_code');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("opacity_not_active_sites", "30", "integer")');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sites ADD url VARCHAR(255) NOT NULL, ADD address VARCHAR(255) NOT NULL, ADD counters_code LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE sites SET url=id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BC00AA63F47645AE ON sites (url)');
    }
}
