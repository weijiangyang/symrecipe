<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220928074130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, time INT DEFAULT NULL, nb_people INT DEFAULT NULL, difficulty INT DEFAULT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, is_favorite TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_incredient (recipe_id INT NOT NULL, incredient_id INT NOT NULL, INDEX IDX_A69BF0E959D8A214 (recipe_id), INDEX IDX_A69BF0E923E6930E (incredient_id), PRIMARY KEY(recipe_id, incredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_incredient ADD CONSTRAINT FK_A69BF0E959D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_incredient ADD CONSTRAINT FK_A69BF0E923E6930E FOREIGN KEY (incredient_id) REFERENCES incredient (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_incredient DROP FOREIGN KEY FK_A69BF0E959D8A214');
        $this->addSql('ALTER TABLE recipe_incredient DROP FOREIGN KEY FK_A69BF0E923E6930E');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_incredient');
    }
}
