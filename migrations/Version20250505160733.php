<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505160733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance DROP FOREIGN KEY FK_386829AEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance ADD CONSTRAINT FK_386829AEA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance_utilisateur DROP FOREIGN KEY FK_786E2157B288C3E3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance_utilisateur ADD CONSTRAINT FK_786E2157B288C3E3 FOREIGN KEY (assurance_id) REFERENCES assurance (ID_contrat) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance DROP FOREIGN KEY FK_386829AEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance ADD CONSTRAINT FK_386829AEA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance_utilisateur DROP FOREIGN KEY FK_786E2157B288C3E3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance_utilisateur ADD CONSTRAINT FK_786E2157B288C3E3 FOREIGN KEY (assurance_id) REFERENCES assurance (ID_contrat)
        SQL);
    }
}
