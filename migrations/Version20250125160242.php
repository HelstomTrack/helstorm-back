<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125160242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exercises (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, rest_time INT NOT NULL, difficulty VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programs_exercises (id INT AUTO_INCREMENT NOT NULL, program_id INT DEFAULT NULL, exercise_id INT DEFAULT NULL, INDEX IDX_D88108283EB8070A (program_id), INDEX IDX_D8810828E934951A (exercise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE programs_exercises ADD CONSTRAINT FK_D88108283EB8070A FOREIGN KEY (program_id) REFERENCES programs (id)');
        $this->addSql('ALTER TABLE programs_exercises ADD CONSTRAINT FK_D8810828E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programs_exercises DROP FOREIGN KEY FK_D88108283EB8070A');
        $this->addSql('ALTER TABLE programs_exercises DROP FOREIGN KEY FK_D8810828E934951A');
        $this->addSql('DROP TABLE exercises');
        $this->addSql('DROP TABLE programs_exercises');
    }
}
