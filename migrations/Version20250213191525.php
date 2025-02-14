<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213191525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan_programs (plan_id INT NOT NULL, programs_id INT NOT NULL, INDEX IDX_FF2C523FE899029B (plan_id), INDEX IDX_FF2C523F79AEC3C (programs_id), PRIMARY KEY(plan_id, programs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plan_programs ADD CONSTRAINT FK_FF2C523FE899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan_programs ADD CONSTRAINT FK_FF2C523F79AEC3C FOREIGN KEY (programs_id) REFERENCES programs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan_programs DROP FOREIGN KEY FK_FF2C523FE899029B');
        $this->addSql('ALTER TABLE plan_programs DROP FOREIGN KEY FK_FF2C523F79AEC3C');
        $this->addSql('DROP TABLE plan_programs');
    }
}
