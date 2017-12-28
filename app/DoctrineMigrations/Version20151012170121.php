<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151012170121 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE offers_prices (at TINYINT(1) NOT NULL, category_id INT NOT NULL, region_id INT NOT NULL, offer_id INT NOT NULL, price INT NOT NULL, INDEX IDX_12293BCA12469DE2 (category_id), INDEX IDX_12293BCA98260155 (region_id), INDEX IDX_12293BCA53C674EE (offer_id), PRIMARY KEY(at, category_id, region_id, offer_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offers_prices ADD CONSTRAINT FK_12293BCA12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE offers_prices ADD CONSTRAINT FK_12293BCA98260155 FOREIGN KEY (region_id) REFERENCES regions (id)');
        $this->addSql('ALTER TABLE offers_prices ADD CONSTRAINT FK_12293BCA53C674EE FOREIGN KEY (offer_id) REFERENCES offers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE offers_prices');
    }
}
