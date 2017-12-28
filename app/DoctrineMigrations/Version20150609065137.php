<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150609065137 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flash_blocks (id INT AUTO_INCREMENT NOT NULL, _key VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, is_simple TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_B8C38DADC7FBB253 (_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flash_blocks_items (id INT AUTO_INCREMENT NOT NULL, block_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, category VARCHAR(255) DEFAULT NULL, lft INT NOT NULL, rgt INT NOT NULL, lvl INT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_28679D82E9ED820C (block_id), INDEX IDX_28679D82727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flash_blocks_items ADD CONSTRAINT FK_28679D82E9ED820C FOREIGN KEY (block_id) REFERENCES flash_blocks (id)');
        $this->addSql('ALTER TABLE flash_blocks_items ADD CONSTRAINT FK_28679D82727ACA70 FOREIGN KEY (parent_id) REFERENCES flash_blocks_items (id)');
        $this->addSql('ALTER TABLE images ADD flash_block_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A4E976491 FOREIGN KEY (flash_block_item_id) REFERENCES flash_blocks_items (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A4E976491 ON images (flash_block_item_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flash_blocks_items DROP FOREIGN KEY FK_28679D82E9ED820C');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A4E976491');
        $this->addSql('ALTER TABLE flash_blocks_items DROP FOREIGN KEY FK_28679D82727ACA70');
        $this->addSql('DROP TABLE flash_blocks');
        $this->addSql('DROP TABLE flash_blocks_items');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A4E976491 ON images');
        $this->addSql('ALTER TABLE images DROP flash_block_item_id');
    }
}
