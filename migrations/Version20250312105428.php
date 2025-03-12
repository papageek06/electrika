<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312105428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE galery_picture (id INT AUTO_INCREMENT NOT NULL, picture VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture_link (id INT AUTO_INCREMENT NOT NULL, events_link_id INT DEFAULT NULL, site_link_id INT DEFAULT NULL, galery_picture_id INT DEFAULT NULL, INDEX IDX_894C1C64FBC57B6B (events_link_id), INDEX IDX_894C1C645600E0BA (site_link_id), INDEX IDX_894C1C6487C6452C (galery_picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE picture_link ADD CONSTRAINT FK_894C1C64FBC57B6B FOREIGN KEY (events_link_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE picture_link ADD CONSTRAINT FK_894C1C645600E0BA FOREIGN KEY (site_link_id) REFERENCES site_event (id)');
        $this->addSql('ALTER TABLE picture_link ADD CONSTRAINT FK_894C1C6487C6452C FOREIGN KEY (galery_picture_id) REFERENCES galery_picture (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture_link DROP FOREIGN KEY FK_894C1C64FBC57B6B');
        $this->addSql('ALTER TABLE picture_link DROP FOREIGN KEY FK_894C1C645600E0BA');
        $this->addSql('ALTER TABLE picture_link DROP FOREIGN KEY FK_894C1C6487C6452C');
        $this->addSql('DROP TABLE galery_picture');
        $this->addSql('DROP TABLE picture_link');
    }
}
