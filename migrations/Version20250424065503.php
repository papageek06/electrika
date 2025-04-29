<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424065503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_event_contact (site_event_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_9B96273C7DBA5751 (site_event_id), INDEX IDX_9B96273CE7A1254A (contact_id), PRIMARY KEY(site_event_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE site_event_contact ADD CONSTRAINT FK_9B96273C7DBA5751 FOREIGN KEY (site_event_id) REFERENCES site_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site_event_contact ADD CONSTRAINT FK_9B96273CE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_event_contact DROP FOREIGN KEY FK_9B96273C7DBA5751');
        $this->addSql('ALTER TABLE site_event_contact DROP FOREIGN KEY FK_9B96273CE7A1254A');
        $this->addSql('DROP TABLE site_event_contact');
    }
}
