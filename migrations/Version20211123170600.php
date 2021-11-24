<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211123170600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E7927C74E7927C74 ON email (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_444F97DD96901F54 ON phone (number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E7927C74E7927C74 ON email');
        $this->addSql('DROP INDEX UNIQ_444F97DD96901F54 ON phone');
    }
}
