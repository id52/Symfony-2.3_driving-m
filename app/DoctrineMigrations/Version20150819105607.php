<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150819105607 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE training_versions_questions (question_id INT NOT NULL, version_id INT NOT NULL, INDEX IDX_9F4C8971E27F6BF (question_id), INDEX IDX_9F4C8974BBC2705 (version_id), PRIMARY KEY(question_id, version_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_versions (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, start_date DATE NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_FEB9534512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_versions_subjects (subject_id INT NOT NULL, version_id INT NOT NULL, INDEX IDX_503C1C0023EDC87 (subject_id), INDEX IDX_503C1C004BBC2705 (version_id), PRIMARY KEY(subject_id, version_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_versions_themes (theme_id INT NOT NULL, version_id INT NOT NULL, INDEX IDX_B36FE2B59027487 (theme_id), INDEX IDX_B36FE2B4BBC2705 (version_id), PRIMARY KEY(theme_id, version_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training_versions_slices (slice_id INT NOT NULL, version_id INT NOT NULL, INDEX IDX_A3FBFAE16F7A2797 (slice_id), INDEX IDX_A3FBFAE14BBC2705 (version_id), PRIMARY KEY(slice_id, version_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE training_versions_questions ADD CONSTRAINT FK_9F4C8971E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE training_versions_questions ADD CONSTRAINT FK_9F4C8974BBC2705 FOREIGN KEY (version_id) REFERENCES training_versions (id)');
        $this->addSql('ALTER TABLE training_versions ADD CONSTRAINT FK_FEB9534512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE training_versions_subjects ADD CONSTRAINT FK_503C1C0023EDC87 FOREIGN KEY (subject_id) REFERENCES subjects (id)');
        $this->addSql('ALTER TABLE training_versions_subjects ADD CONSTRAINT FK_503C1C004BBC2705 FOREIGN KEY (version_id) REFERENCES training_versions (id)');
        $this->addSql('ALTER TABLE training_versions_themes ADD CONSTRAINT FK_B36FE2B59027487 FOREIGN KEY (theme_id) REFERENCES themes (id)');
        $this->addSql('ALTER TABLE training_versions_themes ADD CONSTRAINT FK_B36FE2B4BBC2705 FOREIGN KEY (version_id) REFERENCES training_versions (id)');
        $this->addSql('ALTER TABLE training_versions_slices ADD CONSTRAINT FK_A3FBFAE16F7A2797 FOREIGN KEY (slice_id) REFERENCES slices (id)');
        $this->addSql('ALTER TABLE training_versions_slices ADD CONSTRAINT FK_A3FBFAE14BBC2705 FOREIGN KEY (version_id) REFERENCES training_versions (id)');
        $this->addSql('ALTER TABLE questions ADD is_pdd TINYINT(1) NOT NULL');
        $this->addSql('UPDATE questions AS q LEFT JOIN themes AS t ON t.id=q.theme_id LEFT JOIN subjects AS s ON s.id=t.subject_id SET q.is_pdd=s.is_pdd');
        $this->addSql('ALTER TABLE subjects DROP is_pdd');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE training_versions_questions DROP FOREIGN KEY FK_9F4C8974BBC2705');
        $this->addSql('ALTER TABLE training_versions_subjects DROP FOREIGN KEY FK_503C1C004BBC2705');
        $this->addSql('ALTER TABLE training_versions_themes DROP FOREIGN KEY FK_B36FE2B4BBC2705');
        $this->addSql('ALTER TABLE training_versions_slices DROP FOREIGN KEY FK_A3FBFAE14BBC2705');
        $this->addSql('DROP TABLE training_versions_questions');
        $this->addSql('DROP TABLE training_versions');
        $this->addSql('DROP TABLE training_versions_subjects');
        $this->addSql('DROP TABLE training_versions_themes');
        $this->addSql('DROP TABLE training_versions_slices');
        $this->addSql('ALTER TABLE questions DROP is_pdd');
        $this->addSql('ALTER TABLE subjects ADD is_pdd TINYINT(1) NOT NULL');
    }
}
