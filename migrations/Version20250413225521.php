<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413225521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance ADD CONSTRAINT FK_386829AEA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB16B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT FK_2449BA156B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match ADD CONSTRAINT FK_3744AAE66B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C52449BA15 FOREIGN KEY (equipe) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064047305B190 FOREIGN KEY (ID_contrat) REFERENCES assurance (ID_contrat)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D8CF3843 FOREIGN KEY (idTerrain) REFERENCES terrain (idTerrain)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor ADD CONSTRAINT FK_818CC9D46D9F8618 FOREIGN KEY (idMatch) REFERENCES gestion_match (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B117B2711A FOREIGN KEY (ID_Proprietaire) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (ID)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assurance DROP FOREIGN KEY FK_386829AEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB16B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA156B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match DROP FOREIGN KEY FK_3744AAE66B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C52449BA15
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064047305B190
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D8CF3843
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor DROP FOREIGN KEY FK_818CC9D46D9F8618
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B117B2711A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF7B39D312
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315ED60322AC
        SQL);
    }
}
