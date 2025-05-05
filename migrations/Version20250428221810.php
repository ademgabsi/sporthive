<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428221810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE id_user id_user INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT FK_2449BA156B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_equipe_user ON equipe
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2449BA156B3CA4B ON equipe (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match DROP FOREIGN KEY fk_match_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match DROP FOREIGN KEY fk_match_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match ADD qr_code VARCHAR(255) DEFAULT NULL, CHANGE id_user id_user INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match ADD CONSTRAINT FK_3744AAE66B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_match_user ON gestion_match
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3744AAE66B3CA4B ON gestion_match (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match ADD CONSTRAINT fk_match_user FOREIGN KEY (id_user) REFERENCES utilisateurs (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur DROP FOREIGN KEY fk_equipe_joueur
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur DROP FOREIGN KEY fk_equipe_joueur
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur CHANGE equipe equipe INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C52449BA15 FOREIGN KEY (equipe) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_equipe_joueur ON joueur
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FD71A9C52449BA15 ON joueur (equipe)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur ADD CONSTRAINT fk_equipe_joueur FOREIGN KEY (equipe) REFERENCES equipe (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY fk_reclamation_assurance
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY fk_reclamation_assurance
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation CHANGE Description Description LONGTEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064047305B190 FOREIGN KEY (ID_contrat) REFERENCES assurance (ID_contrat) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_reclamation_assurance ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CE6064047305B190 ON reclamation (ID_contrat)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT fk_reclamation_assurance FOREIGN KEY (ID_contrat) REFERENCES assurance (ID_contrat) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_client_reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_terrain_reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_client_reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY fk_terrain_reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation CHANGE Duree duree VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955BF396750 FOREIGN KEY (id) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C8495514FA422E FOREIGN KEY (ID_Terrain) REFERENCES terrain (id_Terrain)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_client_reservation ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955BF396750 ON reservation (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_terrain_reservation ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C8495514FA422E ON reservation (ID_Terrain)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_client_reservation FOREIGN KEY (id) REFERENCES utilisateurs (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_terrain_reservation FOREIGN KEY (ID_Terrain) REFERENCES terrain (id_Terrain) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX nom ON roles
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE roles CHANGE nom nom VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor DROP FOREIGN KEY fk_client_match
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor DROP FOREIGN KEY fk_client_match
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor CHANGE idMatch idMatch INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor ADD CONSTRAINT FK_818CC9D46D9F8618 FOREIGN KEY (idMatch) REFERENCES gestion_match (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_client_match ON sponsor
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_818CC9D46D9F8618 ON sponsor (idMatch)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor ADD CONSTRAINT fk_client_match FOREIGN KEY (idMatch) REFERENCES gestion_match (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY fk_terrain_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY fk_terrain_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain CHANGE Nom nom VARCHAR(255) NOT NULL, CHANGE Type_surface type_surface VARCHAR(255) NOT NULL, CHANGE Localisation localisation VARCHAR(255) NOT NULL, CHANGE image_ter image_ter VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B117B2711A FOREIGN KEY (ID_Proprietaire) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_terrain_user ON terrain
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C87653B117B2711A ON terrain (ID_Proprietaire)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT fk_terrain_user FOREIGN KEY (ID_Proprietaire) REFERENCES utilisateurs (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi DROP FOREIGN KEY tournoi_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi DROP FOREIGN KEY tournoi_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi CHANGE competition_id competition_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (ID)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX competition_id ON tournoi
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_18AFD9DF7B39D312 ON tournoi (competition_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi ADD CONSTRAINT tournoi_ibfk_1 FOREIGN KEY (competition_id) REFERENCES competition (ID) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX email ON utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX numero_tel ON utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs DROP FOREIGN KEY fk_user_role
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE numero_tel numero_tel VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_user_role ON utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_497B315ED60322AC ON utilisateurs (role_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs ADD CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA156B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA156B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe CHANGE id_user id_user INT NOT NULL, CHANGE nom nom VARCHAR(25) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_2449ba156b3ca4b ON equipe
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_equipe_user ON equipe (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT FK_2449BA156B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match DROP FOREIGN KEY FK_3744AAE66B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match DROP FOREIGN KEY FK_3744AAE66B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match DROP qr_code, CHANGE id_user id_user INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match ADD CONSTRAINT fk_match_user FOREIGN KEY (id_user) REFERENCES utilisateurs (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_3744aae66b3ca4b ON gestion_match
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_match_user ON gestion_match (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestion_match ADD CONSTRAINT FK_3744AAE66B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C52449BA15
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C52449BA15
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur CHANGE equipe equipe INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur ADD CONSTRAINT fk_equipe_joueur FOREIGN KEY (equipe) REFERENCES equipe (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_fd71a9c52449ba15 ON joueur
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_equipe_joueur ON joueur (equipe)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C52449BA15 FOREIGN KEY (equipe) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064047305B190
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064047305B190
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation CHANGE Description Description TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT fk_reclamation_assurance FOREIGN KEY (ID_contrat) REFERENCES assurance (ID_contrat) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ce6064047305b190 ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_reclamation_assurance ON reclamation (ID_contrat)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064047305B190 FOREIGN KEY (ID_contrat) REFERENCES assurance (ID_contrat) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495514FA422E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495514FA422E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation CHANGE duree Duree VARCHAR(20) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_client_reservation FOREIGN KEY (id) REFERENCES utilisateurs (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_terrain_reservation FOREIGN KEY (ID_Terrain) REFERENCES terrain (id_Terrain) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_42c84955bf396750 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_client_reservation ON reservation (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_42c8495514fa422e ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_terrain_reservation ON reservation (ID_Terrain)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955BF396750 FOREIGN KEY (id) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C8495514FA422E FOREIGN KEY (ID_Terrain) REFERENCES terrain (id_Terrain)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE roles CHANGE nom nom VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX nom ON roles (nom)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor DROP FOREIGN KEY FK_818CC9D46D9F8618
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor DROP FOREIGN KEY FK_818CC9D46D9F8618
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor CHANGE idMatch idMatch INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor ADD CONSTRAINT fk_client_match FOREIGN KEY (idMatch) REFERENCES gestion_match (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_818cc9d46d9f8618 ON sponsor
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_client_match ON sponsor (idMatch)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sponsor ADD CONSTRAINT FK_818CC9D46D9F8618 FOREIGN KEY (idMatch) REFERENCES gestion_match (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B117B2711A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B117B2711A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain CHANGE nom Nom VARCHAR(100) NOT NULL, CHANGE type_surface Type_surface VARCHAR(100) NOT NULL, CHANGE localisation Localisation VARCHAR(100) NOT NULL, CHANGE image_ter image_ter VARCHAR(100) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT fk_terrain_user FOREIGN KEY (ID_Proprietaire) REFERENCES utilisateurs (id) ON UPDATE CASCADE ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_c87653b117b2711a ON terrain
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_terrain_user ON terrain (ID_Proprietaire)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B117B2711A FOREIGN KEY (ID_Proprietaire) REFERENCES utilisateurs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF7B39D312
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF7B39D312
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi CHANGE competition_id competition_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi ADD CONSTRAINT tournoi_ibfk_1 FOREIGN KEY (competition_id) REFERENCES competition (ID) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_18afd9df7b39d312 ON tournoi
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX competition_id ON tournoi (competition_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (ID)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315ED60322AC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE prenom prenom VARCHAR(50) NOT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE numero_tel numero_tel VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX email ON utilisateurs (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX numero_tel ON utilisateurs (numero_tel)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_497b315ed60322ac ON utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_user_role ON utilisateurs (role_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
        SQL);
    }
}
