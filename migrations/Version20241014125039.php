<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014125039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feed (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, google_id VARCHAR(255) NOT NULL, title VARCHAR(500) NOT NULL, link CLOB NOT NULL, content CLOB NOT NULL, published DATETIME NOT NULL, updated DATETIME NOT NULL, author VARCHAR(255) DEFAULT NULL, marker_done BOOLEAN DEFAULT 0 NOT NULL, source_name VARCHAR(255) NOT NULL, rss_feed_id INTEGER NOT NULL, CONSTRAINT FK_234044AB59561942 FOREIGN KEY (rss_feed_id) REFERENCES rss_feed (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_234044AB59561942 ON feed (rss_feed_id)');
        $this->addSql('CREATE TABLE marker (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(64) NOT NULL, description CLOB DEFAULT NULL, date DATETIME NOT NULL, link VARCHAR(1024) DEFAULT NULL, author VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE rss_feed (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(1024) NOT NULL, active BOOLEAN NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE feed');
        $this->addSql('DROP TABLE marker');
        $this->addSql('DROP TABLE rss_feed');
    }
}
