<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112130213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meal (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, calories INT NOT NULL, protein DOUBLE PRECISION NOT NULL, carbs DOUBLE PRECISION NOT NULL, fat DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_program (meal_id INT NOT NULL, program_id INT NOT NULL, INDEX IDX_EB5EC59D639666D6 (meal_id), INDEX IDX_EB5EC59D3EB8070A (program_id), PRIMARY KEY(meal_id, program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, goal VARCHAR(255) NOT NULL, total_calories INT NOT NULL, total_protein DOUBLE PRECISION NOT NULL, total_carbs DOUBLE PRECISION NOT NULL, total_fat DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_92ED77849D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, age INT NOT NULL, weight DOUBLE PRECISION NOT NULL, height DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_preferences (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, dietary_restriction VARCHAR(255) NOT NULL, calorie_goal INT NOT NULL, protein_goal DOUBLE PRECISION NOT NULL, fat_goal DOUBLE PRECISION NOT NULL, carb_goal DOUBLE PRECISION NOT NULL, goal VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_402A6F609D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meal_program ADD CONSTRAINT FK_EB5EC59D639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_program ADD CONSTRAINT FK_EB5EC59D3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED77849D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F609D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_program DROP FOREIGN KEY FK_EB5EC59D639666D6');
        $this->addSql('ALTER TABLE meal_program DROP FOREIGN KEY FK_EB5EC59D3EB8070A');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED77849D86650F');
        $this->addSql('ALTER TABLE user_preferences DROP FOREIGN KEY FK_402A6F609D86650F');
        $this->addSql('DROP TABLE meal');
        $this->addSql('DROP TABLE meal_program');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_preferences');
    }
}
