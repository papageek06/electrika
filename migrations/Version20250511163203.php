<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511163203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product_connectors (id INT AUTO_INCREMENT NOT NULL, connector_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_2277503A4D085745 (connector_id), INDEX IDX_2277503A4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors ADD CONSTRAINT FK_2277503A4D085745 FOREIGN KEY (connector_id) REFERENCES connector (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors ADD CONSTRAINT FK_2277503A4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector CHANGE power power INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors DROP FOREIGN KEY FK_2277503A4D085745
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_connectors DROP FOREIGN KEY FK_2277503A4584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_connectors
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE connector CHANGE power power VARCHAR(255) NOT NULL
        SQL);
    }
}
