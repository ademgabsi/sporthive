<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413225545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE assurance (id_contrat INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, duree INT NOT NULL, type_de_couverture VARCHAR(255) NOT NULL, date_debut VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, conditions VARCHAR(255) NOT NULL, INDEX IDX_386829AEA76ED395 (user_id), PRIMARY KEY(id_contrat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, heure TIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_B50A2CB16B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, annee_fondation DATE NOT NULL, stade VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, INDEX IDX_2449BA156B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gestion_match (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, heure TIME NOT NULL, INDEX IDX_3744AAE66B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, equipe INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, photo VARCHAR(255) NOT NULL, INDEX IDX_FD71A9C52449BA15 (equipe), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reclamation (id_reclamation INT AUTO_INCREMENT NOT NULL, date DATE DEFAULT NULL, description LONGTEXT DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, montant_reclame NUMERIC(10, 0) DEFAULT NULL, montant_rembourse NUMERIC(10, 0) DEFAULT NULL, documents VARCHAR(255) DEFAULT NULL, ID_contrat INT DEFAULT NULL, INDEX IDX_CE6064047305B190 (ID_contrat), PRIMARY KEY(id_reclamation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reginerate (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reservation (id_reservation INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, date_heure DATETIME NOT NULL, duree INT NOT NULL, idTerrain INT NOT NULL, INDEX IDX_42C849556B3CA4B (id_user), INDEX IDX_42C84955D8CF3843 (idTerrain), PRIMARY KEY(id_reservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sponsor (id_sp INT AUTO_INCREMENT NOT NULL, nom_sp VARCHAR(255) NOT NULL, type_sp VARCHAR(255) NOT NULL, montant_contrat INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, idMatch INT DEFAULT NULL, INDEX IDX_818CC9D46D9F8618 (idMatch), PRIMARY KEY(id_sp)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE terrain (id_Terrain INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type_surface VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, prix NUMERIC(10, 0) NOT NULL, image_ter VARCHAR(255) NOT NULL, ID_Proprietaire INT DEFAULT NULL, INDEX IDX_C87653B117B2711A (ID_Proprietaire), PRIMARY KEY(id_Terrain)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tournoi (id INT AUTO_INCREMENT NOT NULL, competition_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, lieu VARCHAR(255) NOT NULL, INDEX IDX_18AFD9DF7B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, date_naissance DATE DEFAULT NULL, numero_tel VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_497B315ED60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
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
            ALTER TABLE reservation ADD CONSTRAINT FK_42C849556B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateurs (id)
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
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556B3CA4B
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
        $this->addSql(<<<'SQL'
            DROP TABLE assurance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE competition
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gestion_match
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE joueur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reginerate
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reservation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE roles
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sponsor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE terrain
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tournoi
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateurs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
