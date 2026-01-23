<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118082156 extends AbstractMigration
{
    private function isPostgres(): bool
    {
        return $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if ($this->isPostgres()) {
            $this->addSql('ALTER TABLE movie ADD online BOOLEAN NOT NULL');
        } else {
            $this->addSql('ALTER TABLE movie ADD online TINYINT(1) NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE movie DROP online');
    }
}
