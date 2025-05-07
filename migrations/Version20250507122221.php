<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507122221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE spare_parts (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, quantity INT DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE spare_parts_product (spare_parts_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_3FBAA316597E364F (spare_parts_id), INDEX IDX_3FBAA3164584665A (product_id), PRIMARY KEY(spare_parts_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product ADD CONSTRAINT FK_3FBAA316597E364F FOREIGN KEY (spare_parts_id) REFERENCES spare_parts (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product ADD CONSTRAINT FK_3FBAA3164584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product DROP FOREIGN KEY FK_3FBAA316597E364F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE spare_parts_product DROP FOREIGN KEY FK_3FBAA3164584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE spare_parts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE spare_parts_product
        SQL);
    }
}
