<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122220424 extends AbstractMigration
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
            $this->addSql('ALTER TABLE "user" ADD two_factor_secret VARCHAR(255) DEFAULT NULL, ADD two_factor_enabled BOOLEAN NOT NULL, ADD two_factor_backup_codes JSON DEFAULT NULL');
        } else {
            $this->addSql('ALTER TABLE user ADD two_factor_secret VARCHAR(255) DEFAULT NULL, ADD two_factor_enabled TINYINT(1) NOT NULL, ADD two_factor_backup_codes JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        }
    }

    public function down(Schema $schema): void
    {
        if ($this->isPostgres()) {
            $this->addSql('ALTER TABLE "user" DROP two_factor_secret, DROP two_factor_enabled, DROP two_factor_backup_codes');
        } else {
            $this->addSql('ALTER TABLE `user` DROP two_factor_secret, DROP two_factor_enabled, DROP two_factor_backup_codes');
        }
    }
}
