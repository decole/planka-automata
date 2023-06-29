<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230621215928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sentry_issue ADD is_created BOOLEAN NOT NULL');
        $this->addSql('CREATE INDEX is_created_idx ON sentry_issue (is_created)');
        $this->addSql('ALTER TABLE sentry_issue ADD list_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE sentry_issue ALTER card_number DROP NOT NULL');
        $this->addSql('ALTER TABLE sentry_issue ADD card_id BIGINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX is_created_idx');
        $this->addSql('ALTER TABLE sentry_issue DROP is_created');
        $this->addSql('ALTER TABLE sentry_issue DROP list_id');
        $this->addSql('ALTER TABLE sentry_issue ALTER card_number SET NOT NULL');
        $this->addSql('ALTER TABLE sentry_issue DROP card_id');
    }
}
