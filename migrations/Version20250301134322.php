<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301134322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meal_diet (meal_id INT NOT NULL, diet_id INT NOT NULL, INDEX IDX_873AB6D4639666D6 (meal_id), INDEX IDX_873AB6D4E1E13ACE (diet_id), PRIMARY KEY(meal_id, diet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meal_diet ADD CONSTRAINT FK_873AB6D4639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_diet ADD CONSTRAINT FK_873AB6D4E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_diet DROP FOREIGN KEY FK_873AB6D4639666D6');
        $this->addSql('ALTER TABLE meal_diet DROP FOREIGN KEY FK_873AB6D4E1E13ACE');
        $this->addSql('DROP TABLE meal_diet');
    }
}
