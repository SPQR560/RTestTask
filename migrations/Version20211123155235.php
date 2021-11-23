<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211123155235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, email VARCHAR(255) NOT NULL, is_main TINYINT(1) NOT NULL, INDEX IDX_E7927C74CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, type_id INT NOT NULL, number VARCHAR(255) NOT NULL, is_main TINYINT(1) NOT NULL, INDEX IDX_444F97DDCCFA12B8 (profile_id), INDEX IDX_444F97DDC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, patronymic VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDC54C8C93 FOREIGN KEY (type_id) REFERENCES phone_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DDC54C8C93');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74CCFA12B8');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DDCCFA12B8');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE phone_type');
        $this->addSql('DROP TABLE profile');
    }
}
