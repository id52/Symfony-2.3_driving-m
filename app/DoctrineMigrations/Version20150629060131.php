<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150629060131 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO settings SELECT * FROM settings_notifies');
    }

    public function down(Schema $schema)
    {
    }
}
