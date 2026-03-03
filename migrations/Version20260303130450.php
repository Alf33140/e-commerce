<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303130450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie CHANGE name name VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE name name VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD5E237E06 ON product (name)');
        $this->addSql('ALTER TABLE product_subcategorie ADD CONSTRAINT FK_50B65F664584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_subcategorie ADD CONSTRAINT FK_50B65F667B1204D FOREIGN KEY (subcategorie_id) REFERENCES subcategorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subcategorie ADD CONSTRAINT FK_DD127D0D12469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D34A04AD5E237E06 ON product');
        $this->addSql('ALTER TABLE product CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product_subcategorie DROP FOREIGN KEY FK_50B65F664584665A');
        $this->addSql('ALTER TABLE product_subcategorie DROP FOREIGN KEY FK_50B65F667B1204D');
        $this->addSql('ALTER TABLE subcategorie DROP FOREIGN KEY FK_DD127D0D12469DE2');
    }
}
