<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512125314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product DROP FOREIGN KEY FK_3FBAA3164584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product DROP FOREIGN KEY FK_3FBAA316597E364F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors DROP FOREIGN KEY FK_2277503A4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors DROP FOREIGN KEY FK_2277503A4D085745
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE connector
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE spare_parts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE spare_parts_product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_connectors
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE connector (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, power INT NOT NULL, in_out VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phase_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE spare_parts (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, reference VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, quantity INT DEFAULT NULL, picture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE spare_parts_product (spare_parts_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_3FBAA316597E364F (spare_parts_id), INDEX IDX_3FBAA3164584665A (product_id), PRIMARY KEY(spare_parts_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_connectors (id INT AUTO_INCREMENT NOT NULL, connector_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_2277503A4D085745 (connector_id), INDEX IDX_2277503A4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product ADD CONSTRAINT FK_3FBAA3164584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product ADD CONSTRAINT FK_3FBAA316597E364F FOREIGN KEY (spare_parts_id) REFERENCES spare_parts (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors ADD CONSTRAINT FK_2277503A4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors ADD CONSTRAINT FK_2277503A4D085745 FOREIGN KEY (connector_id) REFERENCES connector (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
