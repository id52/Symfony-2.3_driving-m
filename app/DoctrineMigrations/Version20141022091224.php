<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141022091224 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE support_categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_1847824A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_support_categories (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_5B88C716A76ED395 (user_id), INDEX IDX_5B88C71612469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support_dialogs (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, last_moderator_id INT DEFAULT NULL, user_id INT DEFAULT NULL, answered TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, last_message_time DATETIME NOT NULL, last_message_text LONGTEXT NOT NULL, limit_answer_date DATE NOT NULL, user_read TINYINT(1) NOT NULL, INDEX IDX_5C2A65B12469DE2 (category_id), INDEX IDX_5C2A65BD959D900 (last_moderator_id), INDEX IDX_5C2A65BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE support_messages (id INT AUTO_INCREMENT NOT NULL, dialog_id INT DEFAULT NULL, user_id INT DEFAULT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6FB495A95E46C4E2 (dialog_id), INDEX IDX_6FB495A9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holidays (id INT AUTO_INCREMENT NOT NULL, day SMALLINT NOT NULL, month SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE support_categories ADD CONSTRAINT FK_1847824A727ACA70 FOREIGN KEY (parent_id) REFERENCES support_categories (id)');
        $this->addSql('ALTER TABLE users_support_categories ADD CONSTRAINT FK_5B88C716A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_support_categories ADD CONSTRAINT FK_5B88C71612469DE2 FOREIGN KEY (category_id) REFERENCES support_categories (id)');
        $this->addSql('ALTER TABLE support_dialogs ADD CONSTRAINT FK_5C2A65B12469DE2 FOREIGN KEY (category_id) REFERENCES support_categories (id)');
        $this->addSql('ALTER TABLE support_dialogs ADD CONSTRAINT FK_5C2A65BD959D900 FOREIGN KEY (last_moderator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE support_dialogs ADD CONSTRAINT FK_5C2A65BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE support_messages ADD CONSTRAINT FK_6FB495A95E46C4E2 FOREIGN KEY (dialog_id) REFERENCES support_dialogs (id)');
        $this->addSql('ALTER TABLE support_messages ADD CONSTRAINT FK_6FB495A9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE support_categories DROP FOREIGN KEY FK_1847824A727ACA70');
        $this->addSql('ALTER TABLE users_support_categories DROP FOREIGN KEY FK_5B88C71612469DE2');
        $this->addSql('ALTER TABLE support_dialogs DROP FOREIGN KEY FK_5C2A65B12469DE2');
        $this->addSql('ALTER TABLE support_messages DROP FOREIGN KEY FK_6FB495A95E46C4E2');
        $this->addSql('DROP TABLE support_categories');
        $this->addSql('DROP TABLE users_support_categories');
        $this->addSql('DROP TABLE support_dialogs');
        $this->addSql('DROP TABLE support_messages');
        $this->addSql('DROP TABLE holidays');
    }
}
