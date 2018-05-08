-- Pour désactiver temporairement la gestion de l'intégrité référentielle, enlevez les marques de commentaire de la prochaine ligne :
-- SET FOREIGN_KEY_CHECKS=0;
  
-- Base de données 
-- ---
DROP DATABASE IF EXISTS panierachats;
CREATE DATABASE panierachats;
USE panierachats;


-- ---
-- Table 'panier'
-- Panier d''achats unique à chaque utilisateur.
-- ---

DROP TABLE IF EXISTS `panier`;
    
CREATE TABLE `panier` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `numero` CHAR(32) NOT NULL COMMENT 'Identifiant unique généré côté serveur en PHP (avec uniqid())',
  `date_modif` DATE NOT NULL COMMENT 'Date de dernière modification du panier.',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`numero`)
) COMMENT 'Panier d''achats unique à chaque utilisateur.';

-- ---
-- Table 'produit'
-- 
-- ---

DROP TABLE IF EXISTS `produit`;
    
CREATE TABLE `produit` (
  `id` TINYINT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `description` VARCHAR(250) NULL,
  `prix` DECIMAL(6,2) NOT NULL,
  `stock` SMALLINT NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'panier_article'
-- Les articles contenus dans les différents paniers d''achats.
-- ---

DROP TABLE IF EXISTS `panier_article`;
    
CREATE TABLE `panier_article` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `quantite` TINYINT NOT NULL DEFAULT 1,
  `produit_id` TINYINT NOT NULL,
  `panier_id` INTEGER NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`produit_id`, `panier_id`)
) COMMENT 'Les articles contenus dans les différents paniers d''achats.';

-- ---
-- Propriétés des tables
-- ---
ALTER TABLE `panier` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `produit` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `panier_article` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- ---
-- Relations (clés étrangères) 
-- ---
ALTER TABLE `panier_article` ADD FOREIGN KEY (produit_id) REFERENCES `produit` (`id`);
ALTER TABLE `panier_article` ADD FOREIGN KEY (panier_id) REFERENCES `panier` (`id`);

--
INSERT INTO `produit` (`id`, `nom`, `description`, `prix`, `stock`) VALUES
(10, 'Vélo Midtown', 'Notre vélo urbain d\'entrée de gamme est parfaitement adapté aux déplacements quotidiens et aux balades en ville.', '800.00', 12),
(11, 'Sac à dos Trail', 'Idéal pour partir à l\'aventure et vous accompagner lors de vos déplacements quotidiens, ce sac épuré et bien conçu vous évite de traîner des articles inutiles.', '45.79', 25),
(12, 'Dégaine Positron de Black Diamond', 'Un excellent rapport qualité-prix pour une dégaine dotée du système Keylock et d\'une sangle Dogbone en polyester.', '18.50', 100),
(13, 'Casque de vélo Terra', 'Profitez d\'une aération très bienvenue grâce aux nombreux orifices de ce casque de vélo de montagne. ', '89.25', 124),
(14, 'Chandail à capuchon Mission Control', 'Restez bien au chaud, sans traîner de poids superflu.', '79.99', 12),
(15, 'T-shirt Ringer', 'T-shirt passe-partout bien confectionné et prêt à accueillir les belles journées ensoleillées.', '18.00', 30),
(16, 'Sac étanche Pro Pack', 'Le sac Pro est depuis longtemps la référence pour les grands chargements et les portages.', '295.99', 10),
(17, 'Montre Fenix 5', 'Une montre GPS intelligente pleine de fonctions, le tout dans un style épuré.', '799.95', 5),
(18, 'Manteau de surf Sombrio', 'Lors de journées froides et humides, enfilez ce manteau à glissière pleine longueur pour pagayer dans le brouillard.', '159.97', 14);