-- ============================================================
--  pharmacie_db — reconstruit à partir du code PHP fourni
--  (ajouter.php, modifier.php, liste.php, dashboard.php,
--   index.php, nouvelle.php, facture.php, historique.php,
--   supprimerVente.php, install_users.php)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `pharmacie_db`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pharmacie_db`;

-- ------------------------------------------------------------
-- Table : utilisateurs
-- Utilisée dans : index.php (login), navbar.php, auth.php
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE `utilisateurs` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(100) NOT NULL,
  `login`       VARCHAR(5)   NOT NULL,           -- 5 lettres majuscules (regex ^[A-Z]{5}$)
  `motdepasse`  VARCHAR(255) NOT NULL,           -- hash password_hash()
  `role`        ENUM('admin','pharmacien','caissier') NOT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_login_role` (`login`, `role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : categories
-- Utilisée dans : ajouter.php, modifier.php, liste.php
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`  VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : medicaments
-- Utilisée dans : ajouter.php, modifier.php, liste.php,
--                 dashboard.php, nouvelle.php, rechercheMedocVente.php
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `medicaments`;
CREATE TABLE `medicaments` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`             VARCHAR(5)   NOT NULL,      -- 5 caractères alphanumériques (regex ^[A-Z0-9]{5}$)
  `nom`              VARCHAR(150) NOT NULL,
  `categorie_id`     INT UNSIGNED NOT NULL,
  `prix`             DECIMAL(10,2) NOT NULL,
  `quantite`         INT NOT NULL DEFAULT 0,
  `date_peremption`  DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`code`),
  KEY `idx_categorie` (`categorie_id`),
  KEY `idx_peremption` (`date_peremption`),
  CONSTRAINT `fk_medicament_categorie`
    FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : ventes
-- Utilisée dans : nouvelle.php, historique.php, facture.php,
--                 supprimerVente.php, dashboard.php
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `ventes`;
CREATE TABLE `ventes` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_vente`  DATETIME NOT NULL,
  `total`       DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_date_vente` (`date_vente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : details_vente
-- Utilisée dans : nouvelle.php, facture.php, supprimerVente.php
-- Remarque : la colonne `prix` stocke le prix TOTAL de la ligne
-- (quantite * prix_unitaire au moment de la vente), pas le prix
-- unitaire — c'est ainsi que nouvelle.php et facture.php l'utilisent.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `details_vente`;
CREATE TABLE `details_vente` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vente_id`       INT UNSIGNED NOT NULL,
  `medicament_id`  INT UNSIGNED NOT NULL,
  `quantite`       INT NOT NULL,
  `prix`           DECIMAL(10,2) NOT NULL,   -- prix total de la ligne
  PRIMARY KEY (`id`),
  KEY `idx_vente` (`vente_id`),
  KEY `idx_medicament` (`medicament_id`),
  CONSTRAINT `fk_detail_vente`
    FOREIGN KEY (`vente_id`) REFERENCES `ventes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_medicament`
    FOREIGN KEY (`medicament_id`) REFERENCES `medicaments` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  DONNÉES DE DÉMARRAGE
-- ============================================================

-- Catégories de base
INSERT INTO `categories` (`nom`) VALUES
  ('Antalgique'),
  ('Antibiotique'),
  ('Antipaludéen'),
  ('Antiseptique'),
  ('Vitamine / Complément'),
  ('Sirop / Toux'),
  ('Dermatologie'),
  ('Matériel médical');

-- Comptes utilisateurs (mot de passe pour les 3 : 12345)
-- Hash bcrypt compatible password_verify() de PHP.
INSERT INTO `utilisateurs` (`nom`, `login`, `motdepasse`, `role`) VALUES
  ('Administrateur', 'ADMIN', '$2b$10$3AvTC2gzvj9dLXuQDJptgurFb2qCYp/3QxYO4p8VamPqyvW24teVO', 'admin'),
  ('Pharmacien',      'PHARM', '$2b$10$ydF1gfSx8XiJ25Ik4qQf7uhhqtlH0.jJxeHo3YC9zs2K70/0N9/AS', 'pharmacien'),
  ('Caissier',        'CAISS', '$2b$10$Etj4qflyintOMtHwDBdIduGCaFm9EugJKiFPkTcRnO/A6MBImsLV6', 'caissier');

-- Quelques médicaments de test (dates volontairement variées :
-- un périmé, un bientôt périmé, le reste valide — pour que le
-- dashboard affiche des alertes dès le premier lancement)
INSERT INTO `medicaments` (`code`, `nom`, `categorie_id`, `prix`, `quantite`, `date_peremption`) VALUES
  ('PAR50', 'Paracétamol 500mg',        1, 500.00,  120, DATE_ADD(CURDATE(), INTERVAL 12 MONTH)),
  ('IBU40', 'Ibuprofène 400mg',         1, 750.00,   8,  DATE_ADD(CURDATE(), INTERVAL 8  MONTH)),
  ('AMX50', 'Amoxicilline 500mg',       2, 1200.00,  45, DATE_ADD(CURDATE(), INTERVAL 15 DAY)),
  ('COAR1', 'Coartem (paludisme)',      3, 2500.00,  30, DATE_ADD(CURDATE(), INTERVAL 6  MONTH)),
  ('BETA1', 'Bétadine solution',        4, 1500.00,  20, DATE_ADD(CURDATE(), INTERVAL 10 MONTH)),
  ('VITC1', 'Vitamine C 1000mg',        5, 900.00,   60, DATE_ADD(CURDATE(), INTERVAL 18 MONTH)),
  ('SIRT1', 'Sirop antitussif',         6, 1100.00,   5, DATE_SUB(CURDATE(), INTERVAL 5  DAY));
