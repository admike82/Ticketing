<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230923190650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE application_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE response_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ticket_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE application (id INT NOT NULL, user_account_id INT NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A45BDDC15F37A13B ON application (token)');
        $this->addSql('CREATE INDEX IDX_A45BDDC13C0C9956 ON application (user_account_id)');
        $this->addSql('CREATE TABLE level (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE response (id INT NOT NULL, ticket_id INT NOT NULL, user_account_id INT DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, api_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E7B0BFB700047D2 ON response (ticket_id)');
        $this->addSql('CREATE INDEX IDX_3E7B0BFB3C0C9956 ON response (user_account_id)');
        $this->addSql('COMMENT ON COLUMN response.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN response.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE status (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ticket (id INT NOT NULL, status_id INT NOT NULL, level_id INT NOT NULL, user_account_id INT DEFAULT NULL, application_id INT DEFAULT NULL, author VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, api_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97A0ADA36BF700BD ON ticket (status_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA35FB14BA7 ON ticket (level_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA33C0C9956 ON ticket (user_account_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA33E030ACD ON ticket (application_id)');
        $this->addSql('COMMENT ON COLUMN ticket.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN ticket.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_account (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_253B48AEE7927C74 ON user_account (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC13C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB3C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA36BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA35FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33E030ACD FOREIGN KEY (application_id) REFERENCES application (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE application_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE level_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE response_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ticket_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_account_id_seq CASCADE');
        $this->addSql('ALTER TABLE application DROP CONSTRAINT FK_A45BDDC13C0C9956');
        $this->addSql('ALTER TABLE response DROP CONSTRAINT FK_3E7B0BFB700047D2');
        $this->addSql('ALTER TABLE response DROP CONSTRAINT FK_3E7B0BFB3C0C9956');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA36BF700BD');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA35FB14BA7');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA33C0C9956');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA33E030ACD');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE user_account');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
