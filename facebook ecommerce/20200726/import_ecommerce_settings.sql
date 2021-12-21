-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Lun 09 Mars 2020 à 07:33
-- Version du serveur :  5.7.29-0ubuntu0.18.04.1
-- Version de PHP :  7.2.24-0ubuntu0.18.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `xerowoo`
--

-- --------------------------------------------------------


/**
* Menu 
*/
INSERT INTO `menu` (`id`, `name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`) VALUES (NULL, 'Ecommerce', 'fas fa-cash-register', 'ecommerce', '20', '270', '0', '0', '0', '0', '0', 'Ecommerce');

/** 
* Add Product Card Type 
*/
ALTER TABLE `messenger_bot` CHANGE `template_type` `template_type` ENUM('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','media','product card') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text';

--
-- Structure de la table `wc_connect_credentials`
--

CREATE TABLE `wc_connect_credentials` (
  `id` int(11) NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_currency` varchar(45) DEFAULT NULL,
  `consumer_key` varchar(50) NOT NULL,
  `consumer_secret` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_connect_credentials`
--
ALTER TABLE `wc_connect_credentials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_connect_credentials`
--
ALTER TABLE `wc_connect_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



--
-- Structure de la table `wc_cron_jobs`
--

CREATE TABLE `wc_cron_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cron_type` varchar(255) DEFAULT NULL,
  `cron_finished` int(11) NOT NULL DEFAULT '0',
  `cron_extra` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_cron_jobs`
--
ALTER TABLE `wc_cron_jobs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_cron_jobs`
--
ALTER TABLE `wc_cron_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Structure de la table `wc_woo_cart`
--

CREATE TABLE `wc_woo_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `sender_id` varchar(255) DEFAULT NULL,
  `cart_content` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_woo_cart`
--
ALTER TABLE `wc_woo_cart`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_woo_cart`
--
ALTER TABLE `wc_woo_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Structure de la table `wc_woo_categories`
--

CREATE TABLE `wc_woo_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `category_parent` int(11) NOT NULL DEFAULT '0',
  `category_slug` mediumtext,
  `category_name` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_woo_categories`
--
ALTER TABLE `wc_woo_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_woo_categories`
--
ALTER TABLE `wc_woo_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1191;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Structure de la table `wc_woo_attributes`
--

CREATE TABLE `wc_woo_attributes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL DEFAULT '0',
  `name` mediumtext,
  `slug` mediumtext,
  `type` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `wc_woo_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Structure de la table `wc_woo_attribute_terms`
--

CREATE TABLE `wc_woo_attribute_terms` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL DEFAULT '0',
  `name` mediumtext,
  `slug` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- AUTO_INCREMENT pour la table `wc_woo_attribute_terms`
--
ALTER TABLE `wc_woo_attribute_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;






--
-- Structure de la table `wc_woo_customers`
--

CREATE TABLE `wc_woo_customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` varchar(155) NOT NULL,
  `b_first_name` varchar(155) DEFAULT NULL,
  `b_last_name` varchar(155) DEFAULT NULL,
  `b_address` mediumtext,
  `b_city` varchar(155) DEFAULT NULL,
  `b_state` varchar(155) DEFAULT NULL,
  `b_postcode` varchar(55) DEFAULT NULL,
  `b_country` varchar(55) DEFAULT NULL,
  `b_email` varchar(255) DEFAULT NULL,
  `b_phone` varchar(155) DEFAULT NULL,
  `s_b_same` int(11) NOT NULL DEFAULT '1',
  `s_first_name` varchar(155) DEFAULT NULL,
  `s_last_name` varchar(155) DEFAULT NULL,
  `s_address` mediumtext,
  `s_city` varchar(155) DEFAULT NULL,
  `s_state` varchar(155) DEFAULT NULL,
  `s_postcode` varchar(55) DEFAULT NULL,
  `s_country` varchar(155) DEFAULT NULL,
  `s_email` varchar(255) DEFAULT NULL,
  `s_phone` varchar(155) DEFAULT NULL,
  `is_finished` int(11) NOT NULL DEFAULT '0',
  `current_step` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_woo_customers`
--
ALTER TABLE `wc_woo_customers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_woo_customers`
--
ALTER TABLE `wc_woo_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Structure de la table `wc_woo_orders`
--

CREATE TABLE `wc_woo_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `order_data` longtext,
  `order_status` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_woo_orders`
--
ALTER TABLE `wc_woo_orders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_woo_orders`
--
ALTER TABLE `wc_woo_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Structure de la table `wc_woo_products`
--

CREATE TABLE `wc_woo_products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_url` text,
  `product_name` text,
  `product_images` mediumtext,
  `product_category` text,
  `product_attributes` text,
  `product_price` varchar(255) NOT NULL,
  `product_currency` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_woo_products`
--
ALTER TABLE `wc_woo_products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_woo_products`
--
ALTER TABLE `wc_woo_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1025757;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Structure de la table `wc_woo_settings`
--

CREATE TABLE `wc_woo_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_settings` longtext,
  `selected_payment` varchar(255) DEFAULT NULL,
  `shipping_settings` longtext,
  `selected_shipping` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `wc_woo_settings`
--
ALTER TABLE `wc_woo_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `wc_woo_settings`
--
ALTER TABLE `wc_woo_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

