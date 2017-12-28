<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151009132337 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE promo_key DROP FOREIGN KEY FK_CC96ECCDD0C07AFF');
        $this->addSql('CREATE TABLE promo_campaigns (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, payment_type VARCHAR(255) NOT NULL, used_from DATETIME NOT NULL, used_to DATETIME NOT NULL, active TINYINT(1) NOT NULL, discount INT NOT NULL, max_activations INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE promo');
        $this->addSql('ALTER TABLE payments_logs DROP FOREIGN KEY FK_8A8D45B814914A7E');
        $this->addSql('DROP INDEX IDX_8A8D45B814914A7E ON payments_logs');
        $this->addSql('ALTER TABLE payments_logs DROP promo_key_id');
        $this->addSql('DROP INDEX UNIQ_CC96ECCDD1B862B8 ON promo_key');
        $this->addSql('DROP INDEX IDX_CC96ECCDD0C07AFF ON promo_key');
        $this->addSql('ALTER TABLE promo_key ADD campaign_id INT DEFAULT NULL, ADD _key VARCHAR(255) NOT NULL, ADD activations_info LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP promo_id, DROP active, DROP created, DROP activated, DROP discount, DROP hash, DROP type, DROP source, DROP valid_to, DROP overdue_letter_num');
        $this->addSql('ALTER TABLE promo_key ADD CONSTRAINT FK_CC96ECCDF639F774 FOREIGN KEY (campaign_id) REFERENCES promo_campaigns (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC96ECCDC7FBB253 ON promo_key (_key)');
        $this->addSql('CREATE INDEX IDX_CC96ECCDF639F774 ON promo_key (campaign_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE promo_key DROP FOREIGN KEY FK_CC96ECCDF639F774');
        $this->addSql('CREATE TABLE promo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) NOT NULL, created DATETIME NOT NULL, used_from DATETIME NOT NULL, used_to DATETIME NOT NULL, active TINYINT(1) NOT NULL, restricted ENUM(\'users\', \'keys\') NOT NULL COMMENT \'(DC2Type:enumpromorestricted)\', maxUsers INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE promo_campaigns');
        $this->addSql('ALTER TABLE payments_logs ADD promo_key_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payments_logs ADD CONSTRAINT FK_8A8D45B814914A7E FOREIGN KEY (promo_key_id) REFERENCES promo_key (id)');
        $this->addSql('CREATE INDEX IDX_8A8D45B814914A7E ON payments_logs (promo_key_id)');
        $this->addSql('DROP INDEX UNIQ_CC96ECCDC7FBB253 ON promo_key');
        $this->addSql('DROP INDEX IDX_CC96ECCDF639F774 ON promo_key');
        $this->addSql('ALTER TABLE promo_key ADD active TINYINT(1) DEFAULT NULL, ADD created DATETIME NOT NULL, ADD activated DATETIME DEFAULT NULL, ADD discount INT NOT NULL, ADD hash VARCHAR(128) NOT NULL, ADD type ENUM(\'\') DEFAULT NULL COMMENT \'(DC2Type:enumservice)\', ADD source ENUM(\'campaign\', \'auto_overdue\') DEFAULT NULL COMMENT \'(DC2Type:enumpromokeysource)\', ADD valid_to DATETIME DEFAULT NULL, ADD overdue_letter_num INT DEFAULT NULL, DROP _key, DROP activations_info, CHANGE campaign_id promo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE promo_key ADD CONSTRAINT FK_CC96ECCDD0C07AFF FOREIGN KEY (promo_id) REFERENCES promo (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC96ECCDD1B862B8 ON promo_key (hash)');
        $this->addSql('CREATE INDEX IDX_CC96ECCDD0C07AFF ON promo_key (promo_id)');
    }
}
