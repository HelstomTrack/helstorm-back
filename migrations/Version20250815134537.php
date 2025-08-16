<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250815134537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE programs ADD user_id INT DEFAULT NULL, ADD content LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programs ADD CONSTRAINT FK_F1496545A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F1496545A76ED395 ON programs (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE programs DROP FOREIGN KEY FK_F1496545A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F1496545A76ED395 ON programs
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE programs DROP user_id, DROP content
        SQL);
    }
}
