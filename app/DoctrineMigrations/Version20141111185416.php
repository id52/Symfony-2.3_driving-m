<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141111185416 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("ALTER TABLE `promo_key` CHANGE `source` `source` ENUM( 'campaign', 'auto_overdue' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '(DC2Type:enumpromokeysource)';");
        
        $this->addSql("
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_expiration_1', '1', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_expiration_2', '1', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_expiration_3', '1', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_expiration_4', '1', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_expiration_5', '1', 'integer');

                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_discount_1', '1000', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_discount_2', '1000', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_discount_3', '1000', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_discount_4', '1000', 'integer');
                INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ('notify_no_payments_promo_discount_5', '1000', 'integer');
                ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("
            DELETE from `settings` WHERE 
            `_key` = 'notify_no_payments_promo_expiration_1' OR
            `_key` = 'notify_no_payments_promo_expiration_2' OR
            `_key` = 'notify_no_payments_promo_expiration_3' OR
            `_key` = 'notify_no_payments_promo_expiration_4' OR
            `_key` = 'notify_no_payments_promo_expiration_5' OR
            `_key` = 'notify_no_payments_promo_discount_1' OR
            `_key` = 'notify_no_payments_promo_discount_2' OR
            `_key` = 'notify_no_payments_promo_discount_3' OR
            `_key` = 'notify_no_payments_promo_discount_4' OR
            `_key` = 'notify_no_payments_promo_discount_5';
                 ");
    }
}
