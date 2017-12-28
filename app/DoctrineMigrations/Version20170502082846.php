<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170502082846 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO settings SET _key="final_exam_1_max_errors_in_ticket", value=1, type="integer"');
        $this->addSql('INSERT INTO settings SET _key="final_exam_1_extra_time", value=5, type="integer"');
        $this->addSql('INSERT INTO settings (`_key`, `value`, `type`) VALUES ("ticket_test_old_style", "0", "boolean");');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('DELETE FROM settings WHERE _key="final_exam_1_max_errors_in_ticket"');
        $this->addSql('DELETE FROM settings WHERE _key="final_exam_1_extra_time"');
        $this->addSql('DELETE FROM settings WHERE _key = "ticket_test_old_style";');

    }
}
