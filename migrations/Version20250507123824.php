<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507123824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE connector (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, power INT NOT NULL, in_out VARCHAR(255) NOT NULL, phase_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE connector_product (connector_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8842E8664D085745 (connector_id), INDEX IDX_8842E8664584665A (product_id), PRIMARY KEY(connector_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE connector_site_event (connector_id INT NOT NULL, site_event_id INT NOT NULL, INDEX IDX_9E1E978A4D085745 (connector_id), INDEX IDX_9E1E978A7DBA5751 (site_event_id), PRIMARY KEY(connector_id, site_event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_product ADD CONSTRAINT FK_8842E8664D085745 FOREIGN KEY (connector_id) REFERENCES connector (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_product ADD CONSTRAINT FK_8842E8664584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_site_event ADD CONSTRAINT FK_9E1E978A4D085745 FOREIGN KEY (connector_id) REFERENCES connector (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_site_event ADD CONSTRAINT FK_9E1E978A7DBA5751 FOREIGN KEY (site_event_id) REFERENCES site_event (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_product DROP FOREIGN KEY FK_8842E8664D085745
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_product DROP FOREIGN KEY FK_8842E8664584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_site_event DROP FOREIGN KEY FK_9E1E978A4D085745
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector_site_event DROP FOREIGN KEY FK_9E1E978A7DBA5751
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE connector
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE connector_product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE connector_site_event
        SQL);
    }
}
