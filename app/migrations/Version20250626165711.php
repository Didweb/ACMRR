<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626165711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_edition (id INT AUTO_INCREMENT NOT NULL, title_id INT NOT NULL, label_id INT NOT NULL, year INT DEFAULT NULL, format VARCHAR(10) NOT NULL COMMENT '(DC2Type:product_format)', barcode CHAR(13) DEFAULT NULL COMMENT '(DC2Type:product_barcode)', stock_new INT NOT NULL, INDEX IDX_794803AFA9F87BD (title_id), INDEX IDX_794803AF33B92F39 (label_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_edition_artist (product_edition_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_B1A95CB3E74F4575 (product_edition_id), INDEX IDX_B1A95CB3B7970CF8 (artist_id), PRIMARY KEY(product_edition_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_title (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_used_item (id INT AUTO_INCREMENT NOT NULL, edition_id INT NOT NULL, barcode CHAR(13) NOT NULL COMMENT '(DC2Type:product_barcode)', `condition` VARCHAR(3) NOT NULL COMMENT '(DC2Type:product_status)', price DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_DEB53EDC97AE0266 (barcode), INDEX IDX_DEB53EDC74281A5E (edition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE record_label (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE riddim (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE track (id INT AUTO_INCREMENT NOT NULL, product_edition_id INT NOT NULL, riddim_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_D6E3F8A6E74F4575 (product_edition_id), INDEX IDX_D6E3F8A6DF0A4717 (riddim_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE track_artist (track_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_499B576E5ED23C43 (track_id), INDEX IDX_499B576EB7970CF8 (artist_id), PRIMARY KEY(track_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition ADD CONSTRAINT FK_794803AFA9F87BD FOREIGN KEY (title_id) REFERENCES product_title (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition ADD CONSTRAINT FK_794803AF33B92F39 FOREIGN KEY (label_id) REFERENCES record_label (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_artist ADD CONSTRAINT FK_B1A95CB3E74F4575 FOREIGN KEY (product_edition_id) REFERENCES product_edition (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_artist ADD CONSTRAINT FK_B1A95CB3B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_used_item ADD CONSTRAINT FK_DEB53EDC74281A5E FOREIGN KEY (edition_id) REFERENCES product_edition (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A6E74F4575 FOREIGN KEY (product_edition_id) REFERENCES product_edition (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A6DF0A4717 FOREIGN KEY (riddim_id) REFERENCES riddim (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track_artist ADD CONSTRAINT FK_499B576E5ED23C43 FOREIGN KEY (track_id) REFERENCES track (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track_artist ADD CONSTRAINT FK_499B576EB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition DROP FOREIGN KEY FK_794803AFA9F87BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition DROP FOREIGN KEY FK_794803AF33B92F39
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_artist DROP FOREIGN KEY FK_B1A95CB3E74F4575
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition_artist DROP FOREIGN KEY FK_B1A95CB3B7970CF8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_used_item DROP FOREIGN KEY FK_DEB53EDC74281A5E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A6E74F4575
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A6DF0A4717
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track_artist DROP FOREIGN KEY FK_499B576E5ED23C43
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE track_artist DROP FOREIGN KEY FK_499B576EB7970CF8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE artist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_edition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_edition_artist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_title
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_used_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE record_label
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE riddim
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE track
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE track_artist
        SQL);
    }
}
