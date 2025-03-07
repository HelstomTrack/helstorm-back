<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301130401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE diet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE diet_user (diet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_778B024CE1E13ACE (diet_id), INDEX IDX_778B024CA76ED395 (user_id), PRIMARY KEY(diet_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, calories DOUBLE PRECISION NOT NULL, protein DOUBLE PRECISION NOT NULL, carbs DOUBLE PRECISION NOT NULL, fat DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, total_calories DOUBLE PRECISION NOT NULL, total_protein DOUBLE PRECISION NOT NULL, total_carbs DOUBLE PRECISION NOT NULL, total_fat DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_food (meal_id INT NOT NULL, food_id INT NOT NULL, INDEX IDX_CEE6FA03639666D6 (meal_id), INDEX IDX_CEE6FA03BA8E87C4 (food_id), PRIMARY KEY(meal_id, food_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE diet_user ADD CONSTRAINT FK_778B024CE1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diet_user ADD CONSTRAINT FK_778B024CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_food ADD CONSTRAINT FK_CEE6FA03639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_food ADD CONSTRAINT FK_CEE6FA03BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE diet_user DROP FOREIGN KEY FK_778B024CE1E13ACE');
        $this->addSql('ALTER TABLE diet_user DROP FOREIGN KEY FK_778B024CA76ED395');
        $this->addSql('ALTER TABLE meal_food DROP FOREIGN KEY FK_CEE6FA03639666D6');
        $this->addSql('ALTER TABLE meal_food DROP FOREIGN KEY FK_CEE6FA03BA8E87C4');
        $this->addSql('DROP TABLE diet');
        $this->addSql('DROP TABLE diet_user');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE meal');
        $this->addSql('DROP TABLE meal_food');
    }
}
