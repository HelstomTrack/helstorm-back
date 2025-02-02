<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125153910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_programs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, programs_id INT DEFAULT NULL, assigned_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_11C48145A76ED395 (user_id), INDEX IDX_11C4814579AEC3C (programs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_programs ADD CONSTRAINT FK_11C48145A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_programs ADD CONSTRAINT FK_11C4814579AEC3C FOREIGN KEY (programs_id) REFERENCES programs (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_programs DROP FOREIGN KEY FK_11C48145A76ED395');
        $this->addSql('ALTER TABLE user_programs DROP FOREIGN KEY FK_11C4814579AEC3C');
        $this->addSql('DROP TABLE user_programs');
    }
}
