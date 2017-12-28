<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141104195057 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('INSERT INTO `settings` SET `_key`= "notify_no_payments_3", `value` = "100", `type`="integer";');
        $this->addSql('INSERT INTO `settings` SET `_key`= "notify_no_payments_4", `value` = "101", `type`="integer";');
        $this->addSql('INSERT INTO `settings` SET `_key`= "notify_no_payments_5", `value` = "102", `type`="integer";');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM `settings` WHERE `_key`= "notify_no_payments_3" OR `_key`= "notify_no_payments_4" OR `_key`= "notify_no_payments_5";');
    }
}
