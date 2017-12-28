<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160811130356 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payments_revert_logs DROP INDEX UNIQ_70F0B90FCF5FC610, ADD INDEX IDX_70F0B90FCF5FC610 (payment_log_id)');
        $this->addSql('ALTER TABLE payments_revert_logs DROP FOREIGN KEY FK_70F0B90F4CA8681F');
        $this->addSql('DROP INDEX IDX_70F0B90F4CA8681F ON payments_revert_logs');
        $this->addSql('ALTER TABLE payments_revert_logs ADD moderator_id INT DEFAULT NULL, ADD info LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP ruid, DROP comment, DROP s_id, DROP sum, DROP status');
        $this->addSql('ALTER TABLE payments_revert_logs ADD CONSTRAINT FK_70F0B90FD0AFA354 FOREIGN KEY (moderator_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_70F0B90FD0AFA354 ON payments_revert_logs (moderator_id)');
        $this->addSql('ALTER TABLE payments_logs DROP nonce');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payments_logs ADD nonce VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payments_revert_logs DROP INDEX IDX_70F0B90FCF5FC610, ADD UNIQUE INDEX UNIQ_70F0B90FCF5FC610 (payment_log_id)');
        $this->addSql('ALTER TABLE payments_revert_logs DROP FOREIGN KEY FK_70F0B90FD0AFA354');
        $this->addSql('DROP INDEX IDX_70F0B90FD0AFA354 ON payments_revert_logs');
        $this->addSql('ALTER TABLE payments_revert_logs ADD comment LONGTEXT DEFAULT NULL, ADD s_id VARCHAR(255) DEFAULT NULL, ADD sum INT DEFAULT NULL, ADD status VARCHAR(255) DEFAULT NULL, DROP info, CHANGE moderator_id ruid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payments_revert_logs ADD CONSTRAINT FK_70F0B90F4CA8681F FOREIGN KEY (ruid) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_70F0B90F4CA8681F ON payments_revert_logs (ruid)');
    }
}
