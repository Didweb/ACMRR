<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250629091356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product_edition_tag (product_edition_id INT NOT NULL, product_tag_id INT NOT NULL, INDEX IDX_48D515E6E74F4575 (product_edition_id), INDEX IDX_48D515E6D8AE22B5 (product_tag_id), PRIMARY KEY(product_edition_id, product_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_E3A6E39C5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_tag ADD CONSTRAINT FK_48D515E6E74F4575 FOREIGN KEY (product_edition_id) REFERENCES product_edition (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_tag ADD CONSTRAINT FK_48D515E6D8AE22B5 FOREIGN KEY (product_tag_id) REFERENCES product_tag (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_tag DROP FOREIGN KEY FK_48D515E6E74F4575
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_tag DROP FOREIGN KEY FK_48D515E6D8AE22B5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_edition_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_tag
        SQL);
    }
}
