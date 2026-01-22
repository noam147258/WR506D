<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122213208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, actor_id INT DEFAULT NULL, movie_id INT DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_14D4313210DAF24A (actor_id), INDEX IDX_14D431328F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_object ADD CONSTRAINT FK_14D4313210DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id)');
        $this->addSql('ALTER TABLE media_object ADD CONSTRAINT FK_14D431328F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE user ADD rate_limit INT DEFAULT NULL, ADD api_key_hash VARCHAR(64) DEFAULT NULL, ADD api_key_prefix VARCHAR(16) DEFAULT NULL, ADD api_key_enabled TINYINT(1) NOT NULL, ADD api_key_created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD api_key_last_used_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649848473FE ON user (api_key_hash)');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_object DROP FOREIGN KEY FK_14D4313210DAF24A');
        $this->addSql('ALTER TABLE media_object DROP FOREIGN KEY FK_14D431328F93B6FC');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('DROP INDEX UNIQ_8D93D649848473FE ON `user`');
        $this->addSql('ALTER TABLE `user` DROP rate_limit, DROP api_key_hash, DROP api_key_prefix, DROP api_key_enabled, DROP api_key_created_at, DROP api_key_last_used_at');
    }
}
