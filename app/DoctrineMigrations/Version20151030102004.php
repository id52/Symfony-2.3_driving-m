<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151030102004 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE sites ADD active_auth TINYINT(1) NOT NULL, ADD _show TINYINT(1) NOT NULL, ADD show_auth TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE filials ADD active_auth TINYINT(1) NOT NULL, ADD _show TINYINT(1) NOT NULL, ADD show_auth TINYINT(1) NOT NULL');
        $this->addSql('UPDATE sites SET active_auth=active, _show=1, show_auth=1');
        $this->addSql('UPDATE filials SET active_auth=active, _show=1, show_auth=1');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE filials DROP active_auth, DROP _show, DROP show_auth');
        $this->addSql('ALTER TABLE sites DROP active_auth, DROP _show, DROP show_auth');
    }
}
