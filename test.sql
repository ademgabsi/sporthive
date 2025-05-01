-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 03:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `assurance`
--

CREATE TABLE `assurance` (
  `ID_contrat` int(11) NOT NULL,
  `Duree` int(11) NOT NULL,
  `Statut` varchar(255) NOT NULL,
  `Conditions` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type_de_couverture` varchar(255) NOT NULL,
  `date_debut` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assurance`
--

INSERT INTO `assurance` (`ID_contrat`, `Duree`, `Statut`, `Conditions`, `user_id`, `type_de_couverture`, `date_debut`) VALUES
(1, 20, 'En cous ', 'Dedie au footballeurs', 1, 'premium', '4/13/2025'),
(20, 12, 'Actif', 'Couverture complète pour blessures sportives, équipement et responsabilité civile', 1, 'Premium', '2025-01-01'),
(21, 6, 'Actif', 'Couverture de base pour blessures sportives uniquement', 1, 'Basic', '2025-02-15'),
(22, 24, 'En attente', 'Couverture étendue internationale pour tous types de sports', 1, 'International', '2024-12-10'),
(23, 12, 'Inactif', 'Couverture spécifique pour sports extrêmes avec options de sauvetage', 1, 'Extreme', '2025-03-01');

-- --------------------------------------------------------

--
-- Table structure for table `competition`
--

CREATE TABLE `competition` (
  `ID` int(11) NOT NULL,
  `Nom` varchar(100) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Heure` time DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `nom` varchar(25) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `annee_fondation` date NOT NULL,
  `stade` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gestion_match`
--

CREATE TABLE `gestion_match` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `heure` time NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `joueur`
--

CREATE TABLE `joueur` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `equipe` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reclamation`
--

CREATE TABLE `reclamation` (
  `ID_reclamation` int(11) NOT NULL,
  `Date` date DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Etat` varchar(50) DEFAULT NULL,
  `Montant_reclame` decimal(10,2) DEFAULT NULL,
  `Montant_rembourse` decimal(10,2) DEFAULT NULL,
  `Documents` varchar(255) DEFAULT NULL,
  `ID_contrat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reginerate`
--

CREATE TABLE `reginerate` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `ID_Reservation` int(11) NOT NULL,
  `id` int(11) DEFAULT NULL,
  `ID_Terrain` int(11) DEFAULT NULL,
  `Date_Heure` datetime DEFAULT NULL,
  `Duree` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nom`) VALUES
(2, 'admin'),
(1, 'client');

-- --------------------------------------------------------

--
-- Table structure for table `sponsor`
--

CREATE TABLE `sponsor` (
  `id_Sp` int(11) NOT NULL,
  `nom_Sp` varchar(255) NOT NULL,
  `type_Sp` varchar(255) NOT NULL,
  `montantContrat` int(11) NOT NULL,
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  `idMatch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `terrain`
--

CREATE TABLE `terrain` (
  `id_Terrain` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Type_surface` varchar(100) NOT NULL,
  `Localisation` varchar(100) NOT NULL,
  `Prix` decimal(10,0) NOT NULL,
  `ID_Proprietaire` int(11) DEFAULT NULL,
  `image_ter` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournoi`
--

CREATE TABLE `tournoi` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `competition_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `numero_tel` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `adresse`, `date_naissance`, `numero_tel`, `image`, `role_id`) VALUES
(1, 'gabsi', 'adem', 'guebsiadem@gmail.com', '1234', 'djerba', '0000-00-00', '25290690', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assurance`
--
ALTER TABLE `assurance`
  ADD PRIMARY KEY (`ID_contrat`),
  ADD KEY `fk_client_assurance` (`user_id`);

--
-- Indexes for table `competition`
--
ALTER TABLE `competition`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_competition_user` (`id_user`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_equipe_user` (`id_user`);

--
-- Indexes for table `gestion_match`
--
ALTER TABLE `gestion_match`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_match_user` (`id_user`);

--
-- Indexes for table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_equipe_joueur` (`equipe`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indexes for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`ID_reclamation`),
  ADD KEY `fk_reclamation_assurance` (`ID_contrat`);

--
-- Indexes for table `reginerate`
--
ALTER TABLE `reginerate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ID_Reservation`),
  ADD KEY `fk_client_reservation` (`id`),
  ADD KEY `fk_terrain_reservation` (`ID_Terrain`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`id_Sp`),
  ADD KEY `fk_client_match` (`idMatch`);

--
-- Indexes for table `terrain`
--
ALTER TABLE `terrain`
  ADD PRIMARY KEY (`id_Terrain`),
  ADD KEY `fk_terrain_user` (`ID_Proprietaire`);

--
-- Indexes for table `tournoi`
--
ALTER TABLE `tournoi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `competition_id` (`competition_id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `numero_tel` (`numero_tel`),
  ADD KEY `fk_user_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assurance`
--
ALTER TABLE `assurance`
  MODIFY `ID_contrat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `competition`
--
ALTER TABLE `competition`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gestion_match`
--
ALTER TABLE `gestion_match`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `joueur`
--
ALTER TABLE `joueur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `ID_reclamation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reginerate`
--
ALTER TABLE `reginerate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ID_Reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sponsor`
--
ALTER TABLE `sponsor`
  MODIFY `id_Sp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `terrain`
--
ALTER TABLE `terrain`
  MODIFY `id_Terrain` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tournoi`
--
ALTER TABLE `tournoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assurance`
--
ALTER TABLE `assurance`
  ADD CONSTRAINT `FK_386829AEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `competition`
--
ALTER TABLE `competition`
  ADD CONSTRAINT `fk_competition_user` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `fk_equipe_user` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gestion_match`
--
ALTER TABLE `gestion_match`
  ADD CONSTRAINT `fk_match_user` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `joueur`
--
ALTER TABLE `joueur`
  ADD CONSTRAINT `fk_equipe_joueur` FOREIGN KEY (`equipe`) REFERENCES `equipe` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `fk_reclamation_assurance` FOREIGN KEY (`ID_contrat`) REFERENCES `assurance` (`ID_contrat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_client_reservation` FOREIGN KEY (`id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_terrain_reservation` FOREIGN KEY (`ID_Terrain`) REFERENCES `terrain` (`id_Terrain`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sponsor`
--
ALTER TABLE `sponsor`
  ADD CONSTRAINT `fk_client_match` FOREIGN KEY (`idMatch`) REFERENCES `gestion_match` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `terrain`
--
ALTER TABLE `terrain`
  ADD CONSTRAINT `fk_terrain_user` FOREIGN KEY (`ID_Proprietaire`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournoi`
--
ALTER TABLE `tournoi`
  ADD CONSTRAINT `tournoi_ibfk_1` FOREIGN KEY (`competition_id`) REFERENCES `competition` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
