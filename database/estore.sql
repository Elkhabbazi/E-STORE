-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 30 mai 2026 à 15:01
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `estore`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(1, 2, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'En attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `occasion` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `taille` varchar(100) DEFAULT NULL,
  `couleur` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `created_at`, `occasion`, `region`, `taille`, `couleur`) VALUES
(1, 'Caftan Royal Bleu', 'Magnifique caftan brodé à la main', 4000.00, 'CaftanRoyal.jfif', 'Caftan', 10, '2026-05-17 19:21:22', 'Mariage', 'Fès', 'M', 'Bleu'),
(2, 'Takchita de Haute Couture', 'Takchita dorée élégante avec ceinture et touches de rouge.', 2500.00, 'takchitaDoreeRouge.jfif', 'Takchita', 5, '2026-05-17 19:21:22', 'Mariages', 'Marrakech', 'L', 'Or'),
(3, 'Jellaba Femme Rose', 'Jellaba légère pour soirée', 1000.00, 'prod_6a1867f9048b9.jfif', 'Jellaba Femme', 8, '2026-05-17 19:21:22', 'Soirée', 'Rabat', 'S', 'Rose'),
(4, 'Jabador Homme Blanc', 'Jabador classique en tissu premium', 600.00, 'prod_6a1866988a3f2.webp', 'Jabador', 15, '2026-05-17 19:21:22', 'Quotidien', 'Casablanca', 'XL', 'Blanc'),
(5, 'Caftan Vert  Turquoise', 'Caftan deux pièces en organza vert turquoise', 3490.00, 'prod_6a1867cb0cfe4.webp', 'Caftan', 7, '2026-05-17 19:21:22', 'Mariage', 'Fès', 'M', 'Vert'),
(7, 'Takchita Rose Bonbon', 'Laissez-vous séduire par l\'élégance intemporelle de cette magnifique Takchita haute couture, une pièce maîtresse pour toutes vos grandes occasions (mariages, fiançailles, fêtes traditionnelles).', 1400.00, 'prod_6a19cb19ac67d.jfif', 'Takchita', 9, '2026-05-29 17:21:29', 'Mariage (Invitée), Fiançailles, Baptême (Aqiqa)', 'Fès', 'M / L', 'Rose Bonbon / Rose'),
(9, 'Takchita Moderne Vert Menthe & Émeraude', 'Laissez-vous charmer par la fraîcheur et le minimalisme chic de cette Takchita contemporaine. Un modèle d\'une élégance rare, parfait pour les convives ou pour briller lors d\'une cérémonie estivale ou printanière.', 1800.00, 'prod_6a19cbcd7f8b3.jpeg', 'Takchita', 7, '2026-05-29 17:24:29', 'Soirée Chic, Réception, Henné, Aïd', 'Rabat', 'S / M', 'Vert Menthe / Vert Émeraude'),
(10, 'Takchita Pastel en Brocart & Soie Douce', 'Succombez au charme poétique et à la finesse de cette Takchita d\'exception. Avec son harmonie de tons pastel, elle incarne un romantisme discret et une élégance haute couture, idéale pour une mariée (pour son henné ou ses fiançailles) ou une invitée de marque.', 1900.00, 'prod_6a19cc1337752.jpeg', 'Takchita', 7, '2026-05-29 17:25:39', 'Soirée Chic, Réception, Henné, Aïd', 'Fès', 'M / L / XL', 'Tons Pastel (Rose/Bleu/Vert clair)'),
(11, 'Takchita Mawarda Florale & Strassée', 'Célébrez le printemps et la féminité avec cette somptueuse Takchita moderne et poétique. Surnommée \"Mawarda\" pour son imprimé floral captivant, elle est la tenue idéale pour rayonner lors d\'une fête de famille, d\'un henné ou pour les invités de premier rang.', 1750.00, 'prod_6a19cc59b5bfb.jpeg', 'Takchita', 5, '2026-05-29 17:26:49', 'Mariage, Cérémonie de Henné, Fêtes Familiales', 'Marrakech', 'M / L', 'Floral / Multicolore'),
(12, 'Takchita Mauve Poudré & Broderies Perlées', 'Succombez à l\'élégance absolue avec la Takchita Mauve Poudré  un chef-d\'œuvre de raffinement. Teintée d\'un mauve poudré délicat et impérial, cette création haute couture associe à la perfection la grâce traditionnelle et la sophistication moderne pour vos événements prestigieux.', 1900.00, 'prod_6a19ccafb476e.jpeg', 'Takchita', 4, '2026-05-29 17:28:15', 'Mariage (Invitée), Baptême, Fiançailles', 'Fès', 'S / M', 'Mauve Poudré / Violet clair'),
(13, 'Takchita Moderne Gris Anthracite & Plissé Couture', 'Affirmez votre style avec cette somptueuse Takchita au design contemporain et avant-gardiste. D\'un gris perle à anthracite ultra-tendance, cette création revisite les codes du caftan traditionnel avec une touche géométrique et une élégance minimaliste digne des plus grands défilés.', 2000.00, 'prod_6a19ccf5d6157.jpeg', 'Takchita', 14, '2026-05-29 17:29:25', 'Soirée Chic, Réception, Cocktails', 'Rabat', 'M / L', 'Gris Anthracite / Noir'),
(14, 'Takchita Fassie Prestige – Blanc Crème & Or Royal', 'Incarnez la quintessence de la haute couture marocaine avec cette somptueuse Takchita inspirée de l\'élégance intemporelle du style fassi. Alliant pureté et prestige, cette création est la tenue idéale pour une mariée (pour son henné, son mariage ou ses fiançailles) ou pour un événement traditionnel de grande envergure.', 2500.00, 'prod_6a19cd38d70fd.jpeg', 'Takchita', 5, '2026-05-29 17:30:32', 'Mariage (Mariée), Henné, Grande Cérémonie', 'Fès', 'M / L / XL', 'Blanc Crème / Doré'),
(16, 'Takchita en Velours Prestige', 'Incarnez la grâce et le raffinement absolu avec cette somptueuse Takchita deux pièces, une création de haute couture qui célèbre la richesse du patrimoine marocain. Sa coupe majestueuse et ses finitions minutieuses en font la tenue idéale pour les mariées, les grandes réceptions ou les célébrations de prestige', 2550.00, 'prod_6a19ce46309ce.jpeg', 'Takchita', 3, '2026-05-29 17:35:02', 'Mariage (Proche), Fêtes d\'Hiver, Cérémonie', 'Marrakech', 'M / L', 'Velours (Couleur sombre ex: Vert émeraude / Bordeaux / Bleu nuit)'),
(17, 'Takchita Privilège – Douceur Bleu Ciel & Éclat Cristallin', 'Laissez-vous envoûter par le charme romantique de cette somptueuse Takchita deux pièces, une création de haute couture marocaine qui allie à la perfection la délicatesse d\'une teinte pastel et le prestige d\'un travail d\'orfèvre. Fluide, lumineuse et résolument moderne, c\'est la tenue idéale pour une mariée (pour le jour du henné ou une dfol), une fiancée, ou pour rayonner lors de prestigieuses réceptions estivales.', 2600.00, 'prod_6a19cf0e0b942.jpeg', 'Takchita', 8, '2026-05-29 17:38:22', 'Mariage, Henné, Fiançailles', 'Tanger', 'S / M / L', 'Tons doux (ex: Saumon / Rose pastel / Crème)'),
(18, 'Caftan Organza Imprimé – Fraîcheur Bohème & Éclat Doré', 'Laissez-vous charmer par la poésie et la fluidité de ce magnifique Caftan moderne, une création artistique qui revisite la tenue traditionnelle marocaine avec une légèreté contemporaine unique. Alliant un jeu subtil de transparences et des reflets métalliques, cette pièce est idéale pour les soirées d\'été, les réceptions élégantes ou les célébrations de fiançailles.', 1400.00, 'prod_6a19cf73cfa79.jpeg', 'Caftan', 3, '2026-05-29 17:40:03', 'Baptême (Aqiqa), Aïd, Circoncision, Réception', 'Marrakech', 'S / M', 'Imprimé / Multicolore / Pastel'),
(19, 'Caftan Prestige – Féerie Saumon Poudré & Éclat de Diamants', 'Plongez dans un univers de douceur et de romantisme absolu avec ce somptueux Caftan de haute couture, une pièce magistrale qui incarne le raffinement de la mariée moderne ou de l\'invitée de prestige. Porté par une teinte délicate et un travail de broderie d\'une finesse inouïe, ce modèle offre une allure de reine digne des plus grands contes de fées.', 1850.00, 'prod_6a19cfbb427c1.jpeg', 'Caftan', 9, '2026-05-29 17:41:15', 'Fiançailles, Khotoba, Mariage (Invitée d\'honneur)', 'Fès', 'M / L', 'Saumon Poudré / Doré'),
(20, 'Caftan Velours Terroir – Noblesse du Chocolat & Broderies Géométriques', 'Laissez-vous séduire par l\'élégance authentique et intemporelle de ce magnifique Caftan une pièce, une création qui sublime les matières nobles à travers un design épuré et moderne. Alliant le confort d\'un tissu d\'exception à la rigueur de motifs traditionnels revisités, cette pièce est idéale pour les célébrations de l\'Aïd, les soirées ramadanesques ou les réceptions familiales chics.', 1900.00, 'prod_6a19cffac343a.jpeg', 'Caftan', 8, '2026-05-29 17:42:18', 'Soirée d\'Hiver, Fêtes Familiales, Réception', 'Meknès', 'M / L / XL', 'Chocolat / Marron Foncé'),
(21, 'Jabador Moderne pour Homme – Éclat Jaune Citron & Contrastes Graphiques', 'Affirmez votre style avec audace et élégance grâce à cet ensemble Jabador et pantalon assorti, une création contemporaine qui revisite le vestiaire traditionnel masculin marocain. Alliant une couleur estivale vibrante à des finitions artisanales soignées, cette tenue est parfaite pour les célébrations de l\'Aïd, les soirées d\'été ou les événements décontractés chic.', 1200.00, 'prod_6a19d04fbf706.jpeg', 'Jabador', 10, '2026-05-29 17:43:43', 'Aïd, Circoncision (Tahar), Henné (Marié), Vendredi', 'Fès', 'M / L', 'Jaune / Moutarde / Or'),
(22, 'Jellaba Prestige pour Homme – Élégance Rouge Brique & Transparence Organza', 'Affirmez votre distinction avec cette somptueuse Jellaba marocaine pour homme, une pièce de haute couture traditionnelle qui revisite les classiques avec une touche de modernité absolue. Conçue pour les grandes occasions (fêtes religieuses, mariages, cérémonies), elle incarne le raffinement masculin par excellence.', 1700.00, 'prod_6a19d092dc942.jpeg', 'Jellaba Homme', 6, '2026-05-29 17:44:50', 'Prière du Vendredi, Aïd, Cérémonies, Mariage (Invitée)', 'Oued Zem / Fès', 'M / L / XL', 'Rouge Brique / Terracotta'),
(23, 'Jellaba Light Prestige – Douceur Beige Crème & Raffinement Épuré', 'Incarnez l\'élégance minimaliste et le confort absolu avec cette magnifique  (Jellaba d\'été à col rond) pour homme. Conçue dans un esprit à la fois traditionnel et résolument contemporain, cette pièce se distingue par sa sobriété raffinée, idéale pour les prières de l\'Aïd, les journées de ramadan, ou pour recevoir vos proches avec distinction.', 1700.00, 'prod_6a19d10fe8daa.jpeg', 'Jellaba Homme', 14, '2026-05-29 17:46:55', 'Hiver/Mi-saison, Quotidien Chic, Réception Familiale', 'Chefchaouen / Fès', 'M / L / XL', 'Beige Crème / Blanc Cassé'),
(24, 'Jellaba Light Prestige – Douceur Rose Poudré & Esprit Urbain Chic', 'Osez la modernité et l\'élégance subtile avec cette magnifique Jellaba pour homme (souvent appelée à tort jellaba d\'été sans capuche). Alliant à la perfection le confort absolu et une esthétique contemporaine épurée, cette pièce se distingue par sa nuance pastel ultra-tendance. C\'est la tenue idéale pour célébrer l\'Aïd avec style, pour les prières du vendredi ou pour vos moments de détente lors des douces soirées d\'été.', 1550.00, 'prod_6a19d1674e081.jpeg', 'Jellaba Homme', 8, '2026-05-29 17:48:23', 'Célébrations d\'Été, Aïd, Moussem, Rencontres Officielles', 'Rabat / Casablanca', 'S / M / L', 'Rose Poudré / Pastel'),
(25, 'Jabador Prestige \"Metrouz\" – Pureté Blanc Cassé & Broderies Arabesques', 'Incarnez l\'élégance absolue et la distinction avec ce somptueux Jabador haut de gamme pour homme. Alliance parfaite entre la pureté des lignes traditionnelles et un travail de broderie d\'une finesse digne des plus grands Maâlems, cette pièce est la tenue de prestige par excellence pour un marié (lors du hna ou du mariage), pour les célébrations de l\'Aïd, ou pour toute grande cérémonie officielle.', 1560.00, 'prod_6a19d1b9841da.jpeg', 'Jabador', 5, '2026-05-29 17:49:45', 'Mariage (Marié), Henné, Circoncision, Grande Fête religieuse', 'Salé / Rabat', 'M / L', 'Blanc / Doré (ou selon broderie Metrouz)'),
(26, 'Jabador Velvet Prestige – Profondeur Bourgogne & Sobriété Masculine', 'Alliez le confort absolu à une élégance feutrée avec cette superbe Gandoura pour homme (parfois appelée caftan d\'été ou de mi-saison). Conçue dans une matière noble et enveloppante, cette pièce revisite le vestiaire traditionnel marocain avec une sobriété minimaliste d\'une grande distinction. C\'est la tenue idéale pour recevoir vos proches, célébrer les fêtes religieuses ou passer de douces soirées en toute sérénité.', 1700.00, 'prod_6a19d1fe2a986.jpeg', 'Jabador', 6, '2026-05-29 17:50:54', 'Soirée de Mariage, Henné (Marié), Fêtes d\'Hiver', 'Marrakech / Fès', 'M / L / XL', 'Bourgogne / Bordeaux'),
(27, 'Jabador-Gandoura Éléganz Kaki', 'Ce produit est une Jabador pour homme (souvent associée aux ensembles de type Jabador par sa coupe noble), un vêtement traditionnel marocain qui allie à la perfection élégance, confort et authenticité. Conçue dans un tissu fluide et léger, elle offre une allure à la fois distinguée et décontractée, idéale pour les grandes occasions comme pour le quotidien.', 1800.00, 'prod_6a19d32bd1e08.jpeg', 'Jabador', 3, '2026-05-29 17:55:55', 'Prière du Vendredi, Aïd, Réception Familiale, Ramadan', 'Rabat / Casablanca', 'S / M / L', 'Kaki / Vert Olive'),
(28, 'Jabaddor à rayures', 'Cette Jabador marocaine pour homme (souvent appelée à tort jellaba sans capuche) allie modernité et tradition avec son tissu fluide marron foncé à fines rayures satinées qui structurent élégamment la silhouette. Elle se distingue par son col et son buste richement ornés d\'une broderie artisanale (Sfifa) beige doré, créant un contraste lumineux et raffiné qui se prolonge discrètement sur le bord des manches longues. Légère, noble et confortable, c\'est la tenue idéale pour célébrer les fêtes religieuses ou les occasions spéciales avec distinction.', 1900.00, 'prod_6a19d3a4ae682.jpeg', 'Jabador', 4, '2026-05-29 17:57:56', 'Aïd, Circoncision, Fêtes Traditionnelles, Vendredi', 'Chefchaouen / Fès', 'M / L', 'À rayures (comme: Blanc et Bleu / Crème et Gris)'),
(29, 'Djellaba Moderne  – Blanche et Rose Poudré', 'Laissez-vous séduire par le charme intemporel de notre nouvelle Djellaba deux pièces (sous-djellaba incluse), une pièce maîtresse qui allie parfaitement tradition marocaine et modernité épurée.', 1800.00, 'prod_6a19d5ad72281.jpeg', 'Jellaba Femme', 9, '2026-05-29 18:06:37', 'Visites Familiales, Fêtes de l\'Aïd, Quotidien Chic', 'Rabat', 'S / M / L', 'Blanc / Rose Poudré'),
(30, 'Djellaba Harmonie Mauve', 'Cette pièce est confectionnée dans un tissu haut de gamme, fluide et au tombé impeccable, offrant à la fois un confort absolu et une silhouette d\'une élégance naturelle. Sa couleur principale, un parme/mauve clair très doux, est magnifiquement contrastée par des finitions d\'un violet plus soutenu, créant un effet bicolore harmonieux et captivant.', 1500.00, 'prod_6a19d63a45b7a.jpeg', 'Jellaba Femme', 12, '2026-05-29 18:08:58', 'Réceptions, Sorties Habillées, Aïd, Ramadan', 'Fès / Tanger', 'M / L / XL', 'Selon le tissu (comme: Tons Pastel / Crème)'),
(31, 'Djellaba Prestige Nude & Dentelle', 'Ce modèle d\'exception se distingue par sa couleur nude / vieux rose poudré, une nuance intemporelle, douce et résolument sophistiquée. Confectionnée dans un tissu au tombé lourd et fluide, la djellaba s\'ouvre subtilement sur une sous-djellaba assortie, magnifiée par un plastron en dentelle de guipure haut de gamme.', 1600.00, 'prod_6a19d6779f375.jpeg', 'Jellaba Femme', 15, '2026-05-29 18:09:59', 'Réception, Visites Familiales, Aïd, Cocktails', 'Salé / Rabat', 'S / M / L', ' Beige clair'),
(32, 'Jellaba Prestige \"Bleu Royal & Dentelle\"', 'Ce modèle fascine dès le premier regard par sa teinte Bleu Royal (ou Bleu Majorelle) vibrante et profondément raffinée. Confectionnée dans un tissu haut de gamme au fini mat et au tombé impeccablement fluide, cette djellaba s’ouvre sur une sous-djellaba unique, entièrement sublimée par un col montant et un plastron en dentelle florale fine et transparente.', 1750.00, 'prod_6a19d6b97f2ec.jpeg', 'Jellaba Femme', 11, '2026-05-29 18:11:05', 'Fêtes Traditionnelles, Grande Réception, Cérémonie', 'Fès / Marrakech', 'M / L / XL', 'Bleu Royal'),
(33, 'Jellaba Prestige \"Vert Olive & Noir\"', 'Ce modèle d\'exception se distingue par son alliance de couleurs audacieuse et hautement sophistiquée : un fond Vert Olive (ou Vert Moutarde foncé) vibrant, sublimé par des contrastes d\'un Noir de jais profond. Confectionnée dans un tissu de qualité supérieure au fini mat et fluide, la djellaba extérieure s\'ouvre avec noblesse sur une sous-djellaba noire dotée d\'un col montant raffiné.', 1800.00, 'prod_6a19d705dd25e.jpeg', 'Jellaba Femme', 8, '2026-05-29 18:12:21', 'Soirée Chic, Réception de Prestige, Ramadan', 'Meknès / Fès', 'M / L', 'Vert Olive / Noir'),
(34, 'Jellaba Menthe Royale', 'Ce modèle coup de cœur se distingue par sa nuance pastel ultra-fraîche, lumineuse et flatteuse pour le teint. Confectionnée dans un tissu crêpe de soie haut de gamme au tombé lourd, fluide et d\'une douceur absolue, elle habille la silhouette avec une grâce et une fluidité incomparables.', 1000.00, 'prod_6a19efdb7e861.jpeg', 'Jellaba Femme', 6, '2026-05-29 18:15:02', 'Quotidien Chic, Sorties, Aïd, Ramadan', 'Rabat / Casablanca', 'S / M / L', 'Vert Menthe / Menthe'),
(35, 'Jellaba Bleu Céleste', 'Ce modèle d\'exception se distingue par sa nuance pastel lumineuse, idéale pour les beaux jours et particulièrement flatteuse pour la silhouette. Confectionnée dans un tissu crêpe de soie premium d\'une fluidité remarquable, cette djellaba offre un tombé lourd et impeccable tout en garantissant un confort optimal tout au long de la journée.', 990.00, 'prod_6a19d80abd6f3.jpeg', 'Jellaba Femme', 7, '2026-05-29 18:16:42', 'Sorties d\'Été, Quotidien, Visites en matinée', 'Tanger / Tétouan', 'S / M / L', 'Bleu Céleste / Bleu Clair'),
(36, 'Jellaba Homme Kachiri Prestige', 'Ce modèle se distingue par sa couleur Kachiri (un magnifique vieux rose tirant sur le bordeaux terreux), une teinte noble, chaleureuse et très tendance, idéale pour toutes les saisons. Confectionnée dans un tissu haut de gamme texturé à fines rayures verticales ton sur ton, elle offre une tenue impeccable, un tombé lourd et une élégance naturelle qui structure la silhouette avec virilité.', 1750.00, 'prod_6a19db15b2cdd.jpeg', 'Jellaba Homme', 10, '2026-05-29 18:29:41', 'Prière du Vendredi, Aïd, Cérémonies Officielles, Fêtes familiales', 'Fès / Khouribga', 'M / L / XL', 'Kachiri / Vert Olive Foncé'),
(37, 'Jellaba Homme Gris Graphite & Noir', 'Ce modèle se distingue par sa superbe teinte Gris Graphite / Gris Chiné, une couleur neutre, masculine et intemporelle, idéale pour une allure soignée en toute saison. Confectionnée dans un tissu de qualité supérieure au tombé lourd et structuré, elle offre une excellente tenue et un confort absolu tout au long de la journée.', 850.00, 'prod_6a19db5cf3c04.jpeg', 'Jellaba Homme', 14, '2026-05-29 18:30:53', 'Quotidien Chic, Visites en mi-saison, Mosquée', 'Casablanca / Rabat', 'M / L / XL', 'Gris Graphite / Noir'),
(38, 'Jellaba Homme Bleu Indigo', 'Ce modèle se distingue par sa riche teinte Bleu Indigo / Bleu Jean profond, une nuance masculine, polyvalente et profondément élégante. Confectionnée dans un tissu texturé de haute qualité (parfait pour la mi-saison ou l\'hiver), cette djellaba offre un tombé lourd, une excellente tenue et une allure noble qui s\'adapte à toutes les morphologies', 750.00, 'prod_6a19db97be918.jpeg', 'Jellaba Homme', 5, '2026-05-29 18:31:51', 'Sorties, Réceptions simples, Quotidien', 'Chefchaouen / Marrakech', 'S / M / L', 'Bleu Indigo'),
(39, 'Jellaba Homme Bleu Nuit Prestige', 'Ce modèle se distingue par sa teinte bleu nuit, profonde et intensément masculine, qui offre une alternative ultra-chic au noir classique. Confectionnée dans un tissu noble et lourd (parfait pour l’hiver ou la mi-saison), cette djellaba offre un tombé majestueux et une structure impeccable qui met en valeur la carrure tout en garantissant un confort thermique absolu.', 950.00, 'prod_6a19dbdf3a02f.jpeg', 'Jellaba Homme', 9, '2026-05-29 18:33:03', 'Cérémonie, Soirée de Mariage (Invitée), Aïd', 'Fès / Rabat', 'M / L / XL', 'Bleu Nuit'),
(40, 'Jellaba Homme Blanc Cassé Prestige', 'Ce modèle se distingue par sa teinte blanc cassé douce et lumineuse, une couleur royale traditionnellement plébiscitée pour les grandes occasions et les jours de fête. Confectionnée dans un tissu haut de gamme au tombé fluide et impeccable, elle offre une silhouette élancée tout en garantissant une opacité parfaite et un confort absolu.', 800.00, 'prod_6a19dc286a489.jpeg', 'Jellaba Homme', 3, '2026-05-29 18:34:16', 'Prière de l\'Aïd, Vendredi, Moussem, Cérémonie de Henné', 'Oued Zem / Fès', 'S / M / L / XL', 'Blanc Cassé'),
(41, 'Jellaba Homme Beige Camel & Chocolat', 'Ce modèle se distingue par sa superbe teinte Beige / Café au lait, une couleur chaude, sobre et profondément masculine, idéale pour une allure distinguée en toute saison. Confectionnée dans un tissu texturé de qualité supérieure offrant un superbe tombé lourd, cette djellaba garantit une structure impeccable qui met en valeur la carrure tout en offrant un confort absolu.', 850.00, 'prod_6a19dc686f0e7.jpeg', 'Jellaba Homme', 4, '2026-05-29 18:35:20', 'Quotidien Chic, Prière du Vendredi, Visites Familiales', 'Chefchaouen / Fès', 'M / L / XL', 'Beige Camel / Chocolat'),
(42, 'Jellaba Homme Bleu Azur', 'Ce modèle se distingue par sa superbe teinte bleu azur pastel, une couleur lumineuse, apaisante et hautement distinguée, particulièrement appréciée pour les saisons douces. Confectionnée dans un tissu léger, fluide et d\'une remarquable douceur, elle épouse les mouvements avec naturel tout en offrant un tombé droit impeccable et un confort thermique optimal.', 700.00, 'prod_6a19dcabb3d24.jpeg', 'Jellaba Homme', 6, '2026-05-29 18:36:27', 'Sorties d\'Été, Quotidien, Réceptions simples, Aïd', 'Tanger / Chefchaouen', 'S / M / L', 'Bleu Azur / Bleu Clair');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'oumaima', 'oumaima@gmail.com', '$2y$10$pqh/APpsCmv42mGBxIQpFuMvh2tuhBF0DJbsZjVOgaNueF5aGncqm', 'client', '2026-05-17 18:49:01'),
(2, 'fatimaezzahrae', 'fatimaezzahrae697@gmail.com', '$2y$10$HeG8mYi12.qTIHXsFirNN.iaL.ZREwPPa0km1foTHJ21cUx4ceJ.6', 'client', '2026-05-25 18:26:29'),
(4, 'FatimaZahra', 'fati86868@gmail.com', '$2y$10$PRagp.dcF1ei0aD0VlqvhuM2x261/qF1ENxKPtTeNJiss7KQHcpUi', 'admin', '2026-05-25 19:30:54'),
(5, 'ihssane jallali', 'ihsanjallali@gmail.com', '$2y$10$MiG.YlBgznECf3/SYCpTb.Pb8C27mWHrCW1R9XRGBWPHWZ2dAVN3S', 'admin', '2026-05-29 17:16:42');

-- --------------------------------------------------------

--
-- Structure de la table `visites`
--

CREATE TABLE `visites` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `visites`
--

INSERT INTO `visites` (`id`, `ip`, `pays`, `ville`, `page`, `created_at`) VALUES
(1, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 18:38:26'),
(2, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 18:38:34'),
(3, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 18:38:49'),
(4, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 18:38:58'),
(5, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-29 18:39:57'),
(6, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 18:39:57'),
(7, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 18:46:52'),
(8, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-29 19:02:12'),
(9, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 19:02:12'),
(10, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 19:37:58'),
(11, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-29 19:39:18'),
(12, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-29 19:51:11'),
(13, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 19:53:55'),
(14, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 19:55:34'),
(15, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 19:57:00'),
(16, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 19:58:24'),
(17, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:49:11'),
(18, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:49:13'),
(19, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:49:16'),
(20, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:49:19'),
(21, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:49:24'),
(22, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:49:29'),
(23, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 20:49:30'),
(24, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 20:49:33'),
(25, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 20:49:50'),
(26, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-29 20:50:27'),
(27, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-29 20:50:29'),
(28, '::1', 'Inconnu', 'Inconnue', 'Collection Hommes', '2026-05-29 20:50:36'),
(29, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:50:52'),
(30, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:22'),
(31, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:36'),
(32, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:39'),
(33, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:43'),
(34, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:44'),
(35, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:50'),
(36, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:51:55'),
(37, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:52:06'),
(38, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:52:17'),
(39, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:52:19'),
(40, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:53:57'),
(41, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 20:58:31'),
(42, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:23:55'),
(43, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:23:57'),
(44, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:23:59'),
(45, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:24:00'),
(46, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:24:02'),
(47, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:26:17'),
(48, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:26:19'),
(49, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:26:21'),
(50, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:26:24'),
(51, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:26:25'),
(52, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-29 21:27:35'),
(53, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:27:35'),
(54, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-29 21:31:05'),
(55, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:05'),
(56, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:08'),
(57, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:10'),
(58, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:12'),
(59, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:16'),
(60, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:19'),
(61, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:21'),
(62, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:31:22'),
(63, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:36:02'),
(64, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:36:04'),
(65, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:36:05'),
(66, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 21:36:06'),
(67, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-29 22:21:07'),
(68, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:21:07'),
(69, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-29 22:27:45'),
(70, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:27:45'),
(71, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:27:49'),
(72, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:27:51'),
(73, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:27:52'),
(74, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:27:53'),
(75, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-29 22:27:53'),
(76, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-30 12:09:44'),
(77, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-30 12:09:44'),
(78, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-30 12:09:57'),
(79, '::1', 'Inconnu', 'Inconnue', 'Catalogue', '2026-05-30 12:10:25'),
(80, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-30 12:10:59'),
(81, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-30 12:11:26'),
(82, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-30 12:11:33'),
(83, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-30 12:11:39'),
(84, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-30 12:11:46'),
(85, '::1', 'Inconnu', 'Inconnue', 'Collection Femmes', '2026-05-30 12:11:51'),
(86, '::1', 'Inconnu', 'Inconnue', 'Détail produit', '2026-05-30 12:18:27'),
(87, '::1', 'Inconnu', 'Inconnue', 'Accueil', '2026-05-30 12:18:33');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `visites`
--
ALTER TABLE `visites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `visites`
--
ALTER TABLE `visites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
