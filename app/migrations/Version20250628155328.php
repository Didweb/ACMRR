<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250628155328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition ADD price_new DOUBLE PRECISION NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_used_item ADD condition_folder VARCHAR(3) NOT NULL COMMENT '(DC2Type:product_status)', CHANGE `condition` condition_vinyl VARCHAR(3) NOT NULL COMMENT '(DC2Type:product_status)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_edition DROP price_new
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_used_item ADD `condition` VARCHAR(3) NOT NULL COMMENT '(DC2Type:product_status)', DROP condition_vinyl, DROP condition_folder
        SQL);
    }
}
