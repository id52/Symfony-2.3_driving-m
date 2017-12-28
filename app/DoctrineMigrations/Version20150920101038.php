<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150920101038 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pass_filials (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, station VARCHAR(255) DEFAULT NULL, work_time VARCHAR(255) DEFAULT NULL, phones LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', emails LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', coords LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', address_geo VARCHAR(255) DEFAULT NULL, map_code LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, groups LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_2B38E3098260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pass_filials ADD CONSTRAINT FK_2B38E3098260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE images ADD pass_filial_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A5B2842B3 FOREIGN KEY (pass_filial_id) REFERENCES pass_filials (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A5B2842B3 ON images (pass_filial_id)');
        $this->addSql('ALTER TABLE users ADD pass_info LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('UPDATE users SET pass_info="a:0:{}"');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("opacity_not_active_pass_filials", 30, "integer");');
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("pass_time_recreating", 24, "integer");');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A5B2842B3');
        $this->addSql('DROP TABLE pass_filials');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A5B2842B3 ON images');
        $this->addSql('ALTER TABLE images DROP pass_filial_id');
        $this->addSql('ALTER TABLE users DROP pass_info');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="opacity_not_active_pass_filials";');
        $this->addSql('DELETE FROM `settings` WHERE `_key`="pass_time_recreating";');
    }
}
