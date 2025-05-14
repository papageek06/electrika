<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512161138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE connector (id INT AUTO_INCREMENT NOT NULL, power VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_connector (id INT AUTO_INCREMENT NOT NULL, connector_id INT NOT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, plug_direction VARCHAR(255) NOT NULL, INDEX IDX_7A2CF3E34D085745 (connector_id), INDEX IDX_7A2CF3E34584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connector ADD CONSTRAINT FK_7A2CF3E34D085745 FOREIGN KEY (connector_id) REFERENCES connector (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connector ADD CONSTRAINT FK_7A2CF3E34584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connector DROP FOREIGN KEY FK_7A2CF3E34D085745
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connector DROP FOREIGN KEY FK_7A2CF3E34584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE connector
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_connector
        SQL);
    }
}
