<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260325094919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE add_product_history ADD CONSTRAINT FK_EDEB7BDE4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993988BAC62AF FOREIGN KEY (city_id) REFERENCES cost (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6A35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_subcategorie ADD CONSTRAINT FK_50B65F664584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_subcategorie ADD CONSTRAINT FK_50B65F667B1204D FOREIGN KEY (subcategorie_id) REFERENCES subcategorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subcategorie ADD CONSTRAINT FK_DD127D0D12469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE add_product_history DROP FOREIGN KEY FK_EDEB7BDE4584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993988BAC62AF');
        $this->addSql('ALTER TABLE `order` DROP email');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6A35F2858');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE product_subcategorie DROP FOREIGN KEY FK_50B65F664584665A');
        $this->addSql('ALTER TABLE product_subcategorie DROP FOREIGN KEY FK_50B65F667B1204D');
        $this->addSql('ALTER TABLE subcategorie DROP FOREIGN KEY FK_DD127D0D12469DE2');
    }
}
