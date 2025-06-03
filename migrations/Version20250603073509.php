<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603073509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, technicians_id INT NOT NULL, type VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_765AE0C9FEFB4E80 (technicians_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE intervention_team (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, stard_date DATE NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_CB5FBEDD71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE intervention_team_technician (intervention_team_id INT NOT NULL, technician_id INT NOT NULL, INDEX IDX_4FDCD2C06282ACCB (intervention_team_id), INDEX IDX_4FDCD2C0E6C5D496 (technician_id), PRIMARY KEY(intervention_team_id, technician_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE technician (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, hire_date DATE DEFAULT NULL, specialities LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', UNIQUE INDEX UNIQ_F244E948A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9FEFB4E80 FOREIGN KEY (technicians_id) REFERENCES technician (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention_team ADD CONSTRAINT FK_CB5FBEDD71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention_team_technician ADD CONSTRAINT FK_4FDCD2C06282ACCB FOREIGN KEY (intervention_team_id) REFERENCES intervention_team (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention_team_technician ADD CONSTRAINT FK_4FDCD2C0E6C5D496 FOREIGN KEY (technician_id) REFERENCES technician (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE technician ADD CONSTRAINT FK_F244E948A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture_link DROP FOREIGN KEY FK_894C1C645600E0BA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture_link DROP FOREIGN KEY FK_894C1C6487C6452C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture_link DROP FOREIGN KEY FK_894C1C64FBC57B6B
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE picture_link
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connector CHANGE connector_id connector_id INT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE picture_link (id INT AUTO_INCREMENT NOT NULL, events_link_id INT DEFAULT NULL, site_link_id INT DEFAULT NULL, galery_picture_id INT DEFAULT NULL, INDEX IDX_894C1C64FBC57B6B (events_link_id), INDEX IDX_894C1C645600E0BA (site_link_id), INDEX IDX_894C1C6487C6452C (galery_picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture_link ADD CONSTRAINT FK_894C1C645600E0BA FOREIGN KEY (site_link_id) REFERENCES site_event (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture_link ADD CONSTRAINT FK_894C1C6487C6452C FOREIGN KEY (galery_picture_id) REFERENCES galery_picture (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture_link ADD CONSTRAINT FK_894C1C64FBC57B6B FOREIGN KEY (events_link_id) REFERENCES event (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9FEFB4E80
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention_team DROP FOREIGN KEY FK_CB5FBEDD71F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention_team_technician DROP FOREIGN KEY FK_4FDCD2C06282ACCB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention_team_technician DROP FOREIGN KEY FK_4FDCD2C0E6C5D496
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE technician DROP FOREIGN KEY FK_F244E948A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE absence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE intervention_team
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE intervention_team_technician
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE technician
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connector CHANGE connector_id connector_id INT NOT NULL
        SQL);
    }
}
