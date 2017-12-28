<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160805095458 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payments_revert_logs ADD ruid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payments_revert_logs ADD CONSTRAINT FK_70F0B90F4CA8681F FOREIGN KEY (ruid) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_70F0B90F4CA8681F ON payments_revert_logs (ruid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payments_revert_logs DROP FOREIGN KEY FK_70F0B90F4CA8681F');
        $this->addSql('DROP INDEX IDX_70F0B90F4CA8681F ON payments_revert_logs');
        $this->addSql('ALTER TABLE payments_revert_logs DROP ruid');
    }
}
