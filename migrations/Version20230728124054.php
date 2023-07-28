<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230728124054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_order (order_source INT NOT NULL, order_target INT NOT NULL, INDEX IDX_76B7E7659C056FA6 (order_source), INDEX IDX_76B7E76585E03F29 (order_target), PRIMARY KEY(order_source, order_target)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_order ADD CONSTRAINT FK_76B7E7659C056FA6 FOREIGN KEY (order_source) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_order ADD CONSTRAINT FK_76B7E76585E03F29 FOREIGN KEY (order_target) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE product_detail');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_detail (id INT AUTO_INCREMENT NOT NULL, productdetail VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_order DROP FOREIGN KEY FK_76B7E7659C056FA6');
        $this->addSql('ALTER TABLE order_order DROP FOREIGN KEY FK_76B7E76585E03F29');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_order');
    }
}
