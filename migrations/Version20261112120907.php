<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261112120907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD auteur_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66194C10F0 FOREIGN KEY (auteur_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66194C10F0 ON article (auteur_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66194C10F0');
        $this->addSql('DROP INDEX IDX_23A0E66194C10F0 ON article');
        $this->addSql('ALTER TABLE article DROP auteur_user_id');
    }
}
