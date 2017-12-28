<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160808160111 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payments_logs ADD nonce VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payments_revert_logs ADD status VARCHAR(255) DEFAULT NULL, DROP s_type, CHANGE comment comment LONGTEXT DEFAULT NULL, CHANGE sum sum INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payments_logs DROP nonce');
        $this->addSql('ALTER TABLE payments_revert_logs ADD s_type ENUM(\'robokassa\', \'psb\') DEFAULT NULL COMMENT \'(DC2Type:enumpayment)\', DROP status, CHANGE sum sum INT NOT NULL, CHANGE comment comment LONGTEXT NOT NULL');
    }
}
