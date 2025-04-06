<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250405173327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE day (id INT AUTO_INCREMENT NOT NULL, diet_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E5A02990E1E13ACE (diet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE day_meal (day_id INT NOT NULL, meal_id INT NOT NULL, INDEX IDX_359E63EA9C24126 (day_id), INDEX IDX_359E63EA639666D6 (meal_id), PRIMARY KEY(day_id, meal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day ADD CONSTRAINT FK_E5A02990E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_meal ADD CONSTRAINT FK_359E63EA9C24126 FOREIGN KEY (day_id) REFERENCES day (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_meal ADD CONSTRAINT FK_359E63EA639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE day DROP FOREIGN KEY FK_E5A02990E1E13ACE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_meal DROP FOREIGN KEY FK_359E63EA9C24126
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE day_meal DROP FOREIGN KEY FK_359E63EA639666D6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE day
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE day_meal
        SQL);
    }
}
