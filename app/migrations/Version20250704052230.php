<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704052230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition DROP FOREIGN KEY FK_794803AFA9F87BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition ADD CONSTRAINT FK_794803AFA9F87BD FOREIGN KEY (title_id) REFERENCES product_title (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition DROP FOREIGN KEY FK_794803AFA9F87BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition ADD CONSTRAINT FK_794803AFA9F87BD FOREIGN KEY (title_id) REFERENCES product_title (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
