<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160317174935 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('coupons_phone1_prefix', '', 'string');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('coupons_phone1', '', 'string');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('coupons_phone2_prefix', '', 'string');");
        $this->addSql("INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('coupons_phone2', '', 'string');");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'coupons_phone1_prefix';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'coupons_phone1';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'coupons_phone2_prefix';");
        $this->addSql("DELETE FROM `settings` WHERE `_key` = 'coupons_phone2';");
    }
}
