<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241010102439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feed ADD COLUMN source_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__symfony_demo_user AS SELECT id, full_name, username, email, password, roles FROM symfony_demo_user');
        $this->addSql('DROP TABLE symfony_demo_user');
        $this->addSql('CREATE TABLE symfony_demo_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL)');
        $this->addSql('INSERT INTO symfony_demo_user (id, full_name, username, email, password, roles) SELECT id, full_name, username, email, password, roles FROM __temp__symfony_demo_user');
        $this->addSql('DROP TABLE __temp__symfony_demo_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8FB094A1E7927C74 ON symfony_demo_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8FB094A1F85E0677 ON symfony_demo_user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__feed AS SELECT id, google_id, title, link, content, published, updated, author, marker_done FROM feed');
        $this->addSql('DROP TABLE feed');
        $this->addSql('CREATE TABLE feed (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, google_id VARCHAR(255) NOT NULL, title VARCHAR(500) NOT NULL, link CLOB NOT NULL, content CLOB NOT NULL, published DATETIME NOT NULL, updated DATETIME NOT NULL, author VARCHAR(255) DEFAULT NULL, marker_done BOOLEAN DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO feed (id, google_id, title, link, content, published, updated, author, marker_done) SELECT id, google_id, title, link, content, published, updated, author, marker_done FROM __temp__feed');
        $this->addSql('DROP TABLE __temp__feed');
        $this->addSql('CREATE TEMPORARY TABLE __temp__symfony_demo_user AS SELECT id, full_name, username, email, password, roles FROM symfony_demo_user');
        $this->addSql('DROP TABLE symfony_demo_user');
        $this->addSql('CREATE TABLE symfony_demo_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO symfony_demo_user (id, full_name, username, email, password, roles) SELECT id, full_name, username, email, password, roles FROM __temp__symfony_demo_user');
        $this->addSql('DROP TABLE __temp__symfony_demo_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8FB094A1F85E0677 ON symfony_demo_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8FB094A1E7927C74 ON symfony_demo_user (email)');
    }
}
