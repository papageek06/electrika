<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250320151130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE galery_picture_event (galery_picture_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_ECF5320687C6452C (galery_picture_id), INDEX IDX_ECF5320671F7E88B (event_id), PRIMARY KEY(galery_picture_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galery_picture_site_event (galery_picture_id INT NOT NULL, site_event_id INT NOT NULL, INDEX IDX_29BC2E4387C6452C (galery_picture_id), INDEX IDX_29BC2E437DBA5751 (site_event_id), PRIMARY KEY(galery_picture_id, site_event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE galery_picture_event ADD CONSTRAINT FK_ECF5320687C6452C FOREIGN KEY (galery_picture_id) REFERENCES galery_picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE galery_picture_event ADD CONSTRAINT FK_ECF5320671F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE galery_picture_site_event ADD CONSTRAINT FK_29BC2E4387C6452C FOREIGN KEY (galery_picture_id) REFERENCES galery_picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE galery_picture_site_event ADD CONSTRAINT FK_29BC2E437DBA5751 FOREIGN KEY (site_event_id) REFERENCES site_event (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE galery_picture_event DROP FOREIGN KEY FK_ECF5320687C6452C');
        $this->addSql('ALTER TABLE galery_picture_event DROP FOREIGN KEY FK_ECF5320671F7E88B');
        $this->addSql('ALTER TABLE galery_picture_site_event DROP FOREIGN KEY FK_29BC2E4387C6452C');
        $this->addSql('ALTER TABLE galery_picture_site_event DROP FOREIGN KEY FK_29BC2E437DBA5751');
        $this->addSql('DROP TABLE galery_picture_event');
        $this->addSql('DROP TABLE galery_picture_site_event');
    }
}
