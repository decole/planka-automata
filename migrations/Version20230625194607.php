<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230625194607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE notify_event_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notify_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notify_event_log (id INT NOT NULL, board_id BIGINT NOT NULL, card_id BIGINT NOT NULL, user_id BIGINT NOT NULL, type INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notify_event_log.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notify_event_log.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notify_user (id INT NOT NULL, at_bugfix_created BOOLEAN NOT NULL, at_week BOOLEAN NOT NULL, at_three_days BOOLEAN NOT NULL, at_tomorrow BOOLEAN NOT NULL, at_today BOOLEAN NOT NULL, at_deadline BOOLEAN NOT NULL, after_deadline_by_every_day BOOLEAN NOT NULL, telegram_user_id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE notify_user ADD board_id BIGINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE notify_event_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notify_user_id_seq CASCADE');
        $this->addSql('DROP TABLE notify_event_log');
        $this->addSql('DROP TABLE notify_user');
        $this->addSql('ALTER TABLE notify_user DROP board_id');
    }
}
