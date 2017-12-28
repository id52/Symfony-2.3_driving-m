<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160210023445 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE payments_logs MODIFY `s_type` enum(\'robokassa\', \'psb\') COLLATE utf8_unicode_ci NOT NULL COMMENT \'(DC2Type:enumpayment)\'');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE payments_logs MODIFY `s_type` enum(\'robokassa\') COLLATE utf8_unicode_ci NOT NULL COMMENT \'(DC2Type:enumpayment)\'');
    }
}
