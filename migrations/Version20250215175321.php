<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215175321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan_program_day (id INT AUTO_INCREMENT NOT NULL, plan_id INT DEFAULT NULL, program_id INT DEFAULT NULL, dayofweek VARCHAR(255) NOT NULL, INDEX IDX_9A26C871E899029B (plan_id), INDEX IDX_9A26C8713EB8070A (program_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plan_program_day ADD CONSTRAINT FK_9A26C871E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE plan_program_day ADD CONSTRAINT FK_9A26C8713EB8070A FOREIGN KEY (program_id) REFERENCES programs (id)');
        $this->addSql('ALTER TABLE programs DROP FOREIGN KEY FK_F1496545E899029B');
        $this->addSql('DROP INDEX IDX_F1496545E899029B ON programs');
        $this->addSql('ALTER TABLE programs DROP plan_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan_program_day DROP FOREIGN KEY FK_9A26C871E899029B');
        $this->addSql('ALTER TABLE plan_program_day DROP FOREIGN KEY FK_9A26C8713EB8070A');
        $this->addSql('DROP TABLE plan_program_day');
        $this->addSql('ALTER TABLE programs ADD plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD CONSTRAINT FK_F1496545E899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F1496545E899029B ON programs (plan_id)');
    }
}
