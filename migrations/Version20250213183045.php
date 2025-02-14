<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213183045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan_user (plan_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_98451ABBE899029B (plan_id), INDEX IDX_98451ABBA76ED395 (user_id), PRIMARY KEY(plan_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plan_user ADD CONSTRAINT FK_98451ABBE899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan_user ADD CONSTRAINT FK_98451ABBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_programs DROP FOREIGN KEY FK_11C4814579AEC3C');
        $this->addSql('ALTER TABLE user_programs DROP FOREIGN KEY FK_11C48145A76ED395');
        $this->addSql('DROP TABLE user_programs');
        $this->addSql('ALTER TABLE programs ADD plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE programs ADD CONSTRAINT FK_F1496545E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_F1496545E899029B ON programs (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE programs DROP FOREIGN KEY FK_F1496545E899029B');
        $this->addSql('CREATE TABLE user_programs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, programs_id INT DEFAULT NULL, assigned_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_11C4814579AEC3C (programs_id), INDEX IDX_11C48145A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_programs ADD CONSTRAINT FK_11C4814579AEC3C FOREIGN KEY (programs_id) REFERENCES programs (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user_programs ADD CONSTRAINT FK_11C48145A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE plan_user DROP FOREIGN KEY FK_98451ABBE899029B');
        $this->addSql('ALTER TABLE plan_user DROP FOREIGN KEY FK_98451ABBA76ED395');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE plan_user');
        $this->addSql('DROP INDEX IDX_F1496545E899029B ON programs');
        $this->addSql('ALTER TABLE programs DROP plan_id');
    }
}
