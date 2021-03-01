<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301193605 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, portal_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094FE7927C74 (email), UNIQUE INDEX UNIQ_4FBF094F989D9B62 (slug), UNIQUE INDEX UNIQ_4FBF094FB887E1DD (portal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, score INT DEFAULT NULL, INDEX IDX_1FD77566979B1AD6 (company_id), INDEX IDX_1FD775665D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_feature_tag (feature_id INT NOT NULL, feature_tag_id INT NOT NULL, INDEX IDX_99F76E0760E4B879 (feature_id), INDEX IDX_99F76E07C3C785BF (feature_tag_id), PRIMARY KEY(feature_id, feature_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_tag (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_41E4F230979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, description LONGTEXT NOT NULL, source LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, from_portal TINYINT(1) NOT NULL, is_new TINYINT(1) NOT NULL, INDEX IDX_D2294458979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback_feature (feedback_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_8E8E55C5D249A887 (feedback_id), INDEX IDX_8E8E55C560E4B879 (feature_id), PRIMARY KEY(feedback_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portal (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, display TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portal_feature (id INT AUTO_INCREMENT NOT NULL, feature_id INT DEFAULT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, display TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, feedback_count INT NOT NULL, UNIQUE INDEX UNIQ_1E12AD7F60E4B879 (feature_id), INDEX IDX_1E12AD7F5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portal_feature_state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FB887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id)');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD77566979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD775665D83CC1 FOREIGN KEY (state_id) REFERENCES feature_state (id)');
        $this->addSql('ALTER TABLE feature_feature_tag ADD CONSTRAINT FK_99F76E0760E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_feature_tag ADD CONSTRAINT FK_99F76E07C3C785BF FOREIGN KEY (feature_tag_id) REFERENCES feature_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_tag ADD CONSTRAINT FK_41E4F230979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE feedback_feature ADD CONSTRAINT FK_8E8E55C5D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feedback_feature ADD CONSTRAINT FK_8E8E55C560E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portal_feature ADD CONSTRAINT FK_1E12AD7F60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE portal_feature ADD CONSTRAINT FK_1E12AD7F5D83CC1 FOREIGN KEY (state_id) REFERENCES portal_feature_state (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD77566979B1AD6');
        $this->addSql('ALTER TABLE feature_tag DROP FOREIGN KEY FK_41E4F230979B1AD6');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458979B1AD6');
        $this->addSql('ALTER TABLE feature_feature_tag DROP FOREIGN KEY FK_99F76E0760E4B879');
        $this->addSql('ALTER TABLE feedback_feature DROP FOREIGN KEY FK_8E8E55C560E4B879');
        $this->addSql('ALTER TABLE portal_feature DROP FOREIGN KEY FK_1E12AD7F60E4B879');
        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD775665D83CC1');
        $this->addSql('ALTER TABLE feature_feature_tag DROP FOREIGN KEY FK_99F76E07C3C785BF');
        $this->addSql('ALTER TABLE feedback_feature DROP FOREIGN KEY FK_8E8E55C5D249A887');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FB887E1DD');
        $this->addSql('ALTER TABLE portal_feature DROP FOREIGN KEY FK_1E12AD7F5D83CC1');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE feature_feature_tag');
        $this->addSql('DROP TABLE feature_state');
        $this->addSql('DROP TABLE feature_tag');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE feedback_feature');
        $this->addSql('DROP TABLE portal');
        $this->addSql('DROP TABLE portal_feature');
        $this->addSql('DROP TABLE portal_feature_state');
    }
}