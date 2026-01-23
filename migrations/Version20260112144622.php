<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112144622 extends AbstractMigration
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
            $this->addSql('CREATE TABLE director (id SERIAL NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, dob TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, dod TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
            $this->addSql('ALTER TABLE movie ADD director_id INT DEFAULT NULL, ADD nb_entries INT DEFAULT NULL, ADD url VARCHAR(500) DEFAULT NULL, ADD budget NUMERIC(15, 2) DEFAULT NULL');
            $this->addSql('ALTER TABLE movie ADD CONSTRAINT FK_1D5EF26F899FB366 FOREIGN KEY (director_id) REFERENCES director (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('CREATE INDEX IDX_1D5EF26F899FB366 ON movie (director_id)');
        } else {
            $this->addSql('CREATE TABLE director (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, dob DATETIME NOT NULL, dod DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE movie ADD director_id INT DEFAULT NULL, ADD nb_entries INT DEFAULT NULL, ADD url VARCHAR(500) DEFAULT NULL, ADD budget NUMERIC(15, 2) DEFAULT NULL');
            $this->addSql('ALTER TABLE movie ADD CONSTRAINT FK_1D5EF26F899FB366 FOREIGN KEY (director_id) REFERENCES director (id)');
            $this->addSql('CREATE INDEX IDX_1D5EF26F899FB366 ON movie (director_id)');
        }
    }

    public function down(Schema $schema): void
    {
        if ($this->isPostgres()) {
            $this->addSql('ALTER TABLE movie DROP CONSTRAINT FK_1D5EF26F899FB366');
            $this->addSql('DROP TABLE director');
            $this->addSql('DROP INDEX IDX_1D5EF26F899FB366');
            $this->addSql('ALTER TABLE movie DROP director_id, DROP nb_entries, DROP url, DROP budget');
        } else {
            $this->addSql('ALTER TABLE movie DROP FOREIGN KEY FK_1D5EF26F899FB366');
            $this->addSql('DROP TABLE director');
            $this->addSql('DROP INDEX IDX_1D5EF26F899FB366 ON movie');
            $this->addSql('ALTER TABLE movie DROP director_id, DROP nb_entries, DROP url, DROP budget');
        }
    }
}
