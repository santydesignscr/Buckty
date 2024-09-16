-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Gen 22, 2016 alle 16:13
-- Versione del server: 5.5.34
-- PHP Version: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ultimate_board`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_api`
--

CREATE TABLE `buckty_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `active` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dump dei dati per la tabella `buckty_api`
--

INSERT INTO `buckty_api` (`id`, `provider`, `value`, `active`) VALUES
(1, 'facebook', '{"api_key":"fbkey","api_sec":"fbsecret"}', 'No'),
(2, 'twitter', '{"con_key":"twitterkey","con_sec":"twittersecret"}', 'No'),
(3, 'google', '{"client_id":"Client id","client_secret":"Client secret","redirect_uri":"http:\\/\\/example.com\\/actions\\/login-google.php","dev_key":"dev key"}', 'No');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_files`
--

CREATE TABLE `buckty_files` (
  `ID` int(55) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `trashed` int(55) NOT NULL DEFAULT '0',
  `is_shared` int(55) DEFAULT '0',
  `protected` int(55) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=135 ;

--
-- Dump dei dati per la tabella `buckty_files`
--

INSERT INTO `buckty_files` (`ID`, `file_name`, `hash`, `trashed`, `is_shared`, `protected`, `password`) VALUES
(126, 'default.jpg', 'ENOcUZw6at0If1S', 0, 0, 0, NULL),
(127, 'Hawaii-Beach-Wallpaper-HD.jpg', 'dBbxUwWM3cKw4m9', 0, 0, 0, NULL),
(128, '0efbf6ae20e4f69d54786809c66e39f8.jpg', 'q9rX8wJNFXYPiYa', 0, 1, 0, NULL),
(129, 'Aazma.mp3', 'bTQE6kuuGBcQLOD', 0, 0, 0, NULL),
(130, '3cd202a46e2c3c993ecc4ec0b4ca11d0.jpg', 'dU98zZ4lDU1LrHu', 0, 1, 0, NULL),
(131, 'f698df3ad2ef8206ddb5249036327c29.jpg', 'jwHnyy75f4Gx205', 0, 1, 0, NULL),
(132, '20051210-w50s.flv', 'oGFJHJ0wP7nNPV3', 0, 0, 0, NULL),
(133, 'screenshot-1.png', 'qbCvUZqelvXTh8w', 0, 0, 0, NULL),
(134, 'options-customizer.js', 'nydXHCXYvWAuKKi', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_files_data`
--

CREATE TABLE `buckty_files_data` (
  `key_id` int(55) NOT NULL AUTO_INCREMENT,
  `file_id` int(55) NOT NULL,
  `file_author` int(55) NOT NULL,
  `file_mime` varchar(255) NOT NULL,
  `file_date` datetime NOT NULL,
  `file_size` int(255) DEFAULT '0',
  `image_width` varchar(55) DEFAULT '0',
  `image_height` varchar(55) DEFAULT '0',
  `file_preview` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_ext` varchar(55) NOT NULL,
  PRIMARY KEY (`key_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=135 ;

--
-- Dump dei dati per la tabella `buckty_files_data`
--

INSERT INTO `buckty_files_data` (`key_id`, `file_id`, `file_author`, `file_mime`, `file_date`, `file_size`, `image_width`, `image_height`, `file_preview`, `file_path`, `file_type`, `file_ext`) VALUES
(126, 126, 6, 'image/jpeg', '2016-01-22 10:07:43', 201881, '1112', '1112', 'd55e2f996ed64c0c88d9d815afaa2ffb.jpg', '/NifB4vP40s/201601/', 'jpg', 'jpg'),
(127, 127, 6, 'image/jpeg', '2016-01-22 10:33:12', 298047, '1920', '1080', '54bab33c3fa10358a3f1d63f97925228.jpg', '/NifB4vP40s/201601/', 'jpg', 'jpg'),
(128, 128, 31, 'image/jpeg', '2016-01-22 11:10:45', 22007, '300', '300', 'e107eb2d204b81db2fa916a721c40b16.jpg', '/8JSV9o6bYr/201601/', 'jpg', 'jpg'),
(129, 129, 6, 'audio/mpeg', '2016-01-22 11:19:53', 3672737, '0', '0', 'vW4NjWcjgeRUX6K.mp3', '/NifB4vP40s/201601/', 'mp3', 'mp3'),
(130, 130, 31, 'image/jpeg', '2016-01-22 12:21:10', 113716, '460', '543', 'bdbc99e477ef9b279b3042ff1d84ab0c.jpg', '/8JSV9o6bYr/201601/', 'jpg', 'jpg'),
(131, 131, 31, 'image/jpeg', '2016-01-22 13:57:31', 495445, '1900', '1900', '34e716ae1c2f6c2a56c3b9b3fea2639a.jpg', '/8JSV9o6bYr/201601/', 'jpg', 'jpg'),
(132, 132, 6, 'video/x-flv', '2016-01-22 14:24:21', 1048577, '0', '0', '0B6McjIYjw7RdS01pMTlJQmFKSTA.flv', '/NifB4vP40s/201601/', 'flv', 'flv'),
(133, 133, 6, 'image/png', '2016-01-22 14:26:04', 28421, '300', '97', '0B6McjIYjw7Rdd2trV3JfZ2RwQUU.png', '/NifB4vP40s/201601/', 'png', 'png'),
(134, 134, 6, 'application/javascript', '2016-01-22 14:56:31', 1048577, '0', '0', '0B6McjIYjw7Rdcmw2M2psTmRTMUk.js', '/NifB4vP40s/201601/', 'js', 'js');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_folders`
--

CREATE TABLE `buckty_folders` (
  `folder_id` int(55) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(255) NOT NULL,
  `folder_hash` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `folder_author` int(55) NOT NULL,
  `trashed` int(55) DEFAULT '0',
  `is_shared` int(55) DEFAULT '0',
  `protected` int(55) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`folder_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=189 ;

--
-- Dump dei dati per la tabella `buckty_folders`
--

INSERT INTO `buckty_folders` (`folder_id`, `folder_name`, `folder_hash`, `date`, `folder_author`, `trashed`, `is_shared`, `protected`, `password`) VALUES
(167, 'Folders', '0', '2016-01-21 22:01:00', 31, 0, 0, 1, NULL),
(169, 'myfolder', 'LYPl9YTPwRmvklm', '2016-01-22 01:10:10', 31, 0, 0, 0, NULL),
(183, 'ddddd', 'IhVRE6S7WxaZnOa', '2016-01-22 01:44:23', 31, 0, 0, 0, NULL),
(187, 'Photos', '1aJyzM2U2A5uKIj', '2016-01-22 10:07:19', 6, 0, 1, 0, NULL),
(188, 'Sharing folder', 'VpuRU5YHK0mTBse', '2016-01-22 11:12:40', 31, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_groups`
--

CREATE TABLE `buckty_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dump dei dati per la tabella `buckty_groups`
--

INSERT INTO `buckty_groups` (`id`, `name`, `definition`) VALUES
(1, 'Admin', 'Super Admin Group'),
(3, 'Default', 'Default Access Group');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_language`
--

CREATE TABLE `buckty_language` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `lang_title` varchar(255) NOT NULL,
  `lang_slug` varchar(255) NOT NULL,
  `default` int(55) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dump dei dati per la tabella `buckty_language`
--

INSERT INTO `buckty_language` (`id`, `lang_title`, `lang_slug`, `default`) VALUES
(23, 'Italian', 'it', 0),
(26, 'Spanish', 'sp', 0),
(27, 'English', 'en', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_language_data`
--

CREATE TABLE `buckty_language_data` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `lang_id` int(55) NOT NULL,
  `key_name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=449 ;

--
-- Dump dei dati per la tabella `buckty_language_data`
--

INSERT INTO `buckty_language_data` (`id`, `lang_id`, `key_name`, `value`) VALUES
(16, 23, 'Login', 'Entra'),
(17, 23, 'Register', 'Registra'),
(18, 23, 'Your account was banned', 'Il tuo account è stato bannato'),
(19, 23, 'We have sent an email with confirmation link', 'Abbiamo inviato l''email con link di conferma email.'),
(20, 23, 'Upload', 'Carica'),
(21, 23, 'helo', 'Ciao'),
(22, 23, 'Something went wrong', 'Qualcosa è andato storto'),
(23, 23, 'Invalid Folder name', 'Nome della cartella non valida'),
(84, 23, 'Settings', 'Impostazioni'),
(85, 23, 'Logout', 'Esci'),
(86, 23, 'Edit Aavatar', 'Modifica Aavatar'),
(87, 23, 'Admin Panel', 'Panello Admin'),
(88, 23, 'Create Folder', 'Crea cartella'),
(89, 23, 'Upload Files', 'Carica i file'),
(90, 23, 'Clear All', 'Cancella Tutto'),
(91, 23, 'Reload', 'Ricarica'),
(92, 23, 'Sort', 'Ordina'),
(93, 23, 'View', 'Visualizza Per'),
(94, 23, 'Delete Files', 'Rimuovi i file'),
(95, 23, 'Move Files', 'Muovi i file'),
(96, 23, 'Size', 'Dimensione'),
(97, 23, 'Shared', 'Condiviso'),
(98, 23, 'All Files', 'Tutti file'),
(99, 23, 'Recent', 'Recenti'),
(100, 23, 'Trash', 'Cestino'),
(101, 23, 'No files or folders in trash', 'Nessun file / cartella trovato in cestino'),
(102, 23, 'Starred', 'Preferiti'),
(103, 23, 'Notifications', 'Notifiche'),
(104, 23, 'Something is loading', 'Qualcosa sta caricando'),
(105, 23, 'Select All', 'Seleziona tutto'),
(106, 23, 'Add star', 'Preferito'),
(107, 23, 'Remove Star', 'Rimuovi Preferito'),
(108, 23, 'Rename', 'Rinomina'),
(109, 23, 'Delete', 'Elimina'),
(110, 23, 'Download', 'Scarica'),
(111, 23, 'Move', 'Muovi'),
(112, 23, 'Details', 'Detagli'),
(113, 23, 'Copy Link', 'Copia il link'),
(114, 23, 'Dropbox it', 'Dropbox'),
(115, 23, 'Google Drive', 'Google Drive'),
(116, 23, 'Share', 'Condividi'),
(117, 23, 'Cancel', 'Anulla'),
(118, 23, 'Done', 'Fatto'),
(119, 23, 'Item was shared', 'Contenuto Condiviso'),
(120, 23, 'Folder created', 'Cartella è stata creata'),
(121, 23, 'My Files', 'Miei File'),
(122, 23, 'Dropbox Files', 'I files nel dropbox'),
(123, 23, 'Email Address', 'Indrizzo Email'),
(124, 23, 'Connect to google drive', 'Connetti con google drive'),
(125, 23, 'Connect to dropbox', 'Connetti con dropbox'),
(126, 23, 'New Passowrd', 'Nuovo Passowrd'),
(127, 23, 'New Passoword', 'Nuovo Passoword'),
(128, 23, 'Confirm Password', 'Conferma Password'),
(129, 23, 'Update', 'Aggiorna'),
(130, 23, 'Reset', 'Resetta'),
(131, 23, 'Recovery Options', 'Opzioni recovery'),
(132, 23, 'Active Since', 'Attivo da'),
(133, 23, 'Disconnect', 'Disconnetti'),
(134, 23, 'Creation Date', 'Data di creazione'),
(135, 23, 'Date', 'Data'),
(136, 23, 'Size Bigger First', 'File - Grandi prima'),
(137, 23, 'Size Small First', 'File - Piccoli prima'),
(138, 23, 'Preview', 'Vedi'),
(139, 23, 'More', 'Altro'),
(140, 23, 'Copy', 'Copia'),
(141, 23, 'Star', 'Preferito'),
(150, 23, 'Item was starred', 'Item was starred'),
(151, 23, 'Item was un starred', 'Item was un starred'),
(152, 23, 'User account was created', 'User account was created'),
(153, 23, 'Clear', 'Cancella'),
(154, 23, 'Abort', 'Rimuovi'),
(155, 23, 'Search for files', 'Cerca i file'),
(158, 0, 'Edit_Avatar', 'Edit_Avatar'),
(160, 23, 'Username', 'Nome Utente'),
(161, 23, 'New Password', 'Nuova Password'),
(163, 23, 'User already updated', 'Utente Aggiornato'),
(166, 23, 'Password Confirm Error', 'Password e Conferma Password non sono uguali'),
(168, 23, 'User was updated', 'Utente Aggiornato'),
(169, 23, 'Uploaded Successfully', 'Caricato'),
(170, 23, 'Please login again', 'Fai login di nuovo'),
(171, 23, 'Reset link sent', 'Link per risettare la password è stato inviato su sua email. '),
(172, 23, 'User email exist', 'Utente con quest''email esiste già.'),
(173, 23, 'User email not exist', 'Utente con email fornita , not esiste nel nostro database'),
(174, 23, 'Create', 'Crea'),
(181, 23, 'Folder name', 'Nome della cartella'),
(182, 23, 'Select the file folders', 'Seleziona files/cartelle da muovere'),
(268, 23, 'dropbox', 'dropbox'),
(269, 23, 'drive', 'drive'),
(357, 27, 'Login', 'Login'),
(358, 27, 'Register', 'Register'),
(359, 27, 'Your account was banned', 'Your account was banned'),
(360, 27, 'We have sent an email with confirmation link', 'We have sent an email with confirmation link'),
(361, 27, 'Upload', 'Upload'),
(362, 27, 'helo', 'helo'),
(363, 27, 'Something went wrong', 'Something went wrong'),
(364, 27, 'Invalid Folder name', 'Invalid Folder name'),
(365, 27, 'Settings', 'Settings'),
(366, 27, 'Logout', 'Logout'),
(367, 27, 'Edit Aavatar', 'Avatar'),
(368, 27, 'Admin Panel', 'Admin Panel'),
(369, 27, 'Create Folder', 'Create Folder'),
(370, 27, 'Upload Files', 'Upload Files'),
(371, 27, 'Clear All', 'Clear All'),
(372, 27, 'Reload', 'Reload'),
(373, 27, 'Sort', 'Sort'),
(374, 27, 'View', 'View'),
(375, 27, 'Delete Files', 'Delete Files'),
(376, 27, 'Move Files', 'Move Files'),
(377, 27, 'Size', 'Size'),
(378, 27, 'Shared', 'Shared with me'),
(379, 27, 'All Files', 'All Files'),
(380, 27, 'Recent', 'Recent'),
(381, 27, 'Trash', 'Trash'),
(382, 27, 'No files or folders in trash', 'No files or folders in trash'),
(383, 27, 'Starred', 'Starred'),
(384, 27, 'Notifications', 'Notifications'),
(385, 27, 'Something is loading', 'Loading'),
(386, 27, 'Select All', 'Select All'),
(387, 27, 'Add star', 'Add star'),
(388, 27, 'Remove Star', 'Remove Star'),
(389, 27, 'Rename', 'Rename'),
(390, 27, 'Delete', 'Delete'),
(391, 27, 'Download', 'Download'),
(392, 27, 'Move', 'Move'),
(393, 27, 'Details', 'Details'),
(394, 27, 'Copy Link', 'Copy Link'),
(395, 27, 'Dropbox it', 'Dropbox it'),
(396, 27, 'Google Drive', 'Google Drive'),
(397, 27, 'Share', 'Share'),
(398, 27, 'Cancel', 'Cancel'),
(399, 27, 'Done', 'Done'),
(400, 27, 'Item was shared', 'Item was shared'),
(401, 27, 'Folder created', 'Folder created'),
(402, 27, 'My Files', 'My Files'),
(403, 27, 'Dropbox Files', 'Dropbox Files'),
(404, 27, 'Email Address', 'Email Address'),
(405, 27, 'Connect to google drive', 'Connect to google drive'),
(406, 27, 'Connect to dropbox', 'Connect to dropbox'),
(407, 27, 'New Passowrd', 'New Passowrd'),
(408, 27, 'New Passoword', 'New Passoword'),
(409, 27, 'Confirm Password', 'Confirm Password'),
(410, 27, 'Update', 'Update'),
(411, 27, 'Reset', 'Reset'),
(412, 27, 'Recovery Options', 'Recovery Options'),
(413, 27, 'Active Since', 'Active Since'),
(414, 27, 'Disconnect', 'Disconnect'),
(415, 27, 'Creation Date', 'Creation Date'),
(416, 27, 'Date', 'Date'),
(417, 27, 'Size Bigger First', 'Size Bigger First'),
(418, 27, 'Size Small First', 'Size Small First'),
(419, 27, 'Preview', 'Preview'),
(420, 27, 'More', 'More'),
(421, 27, 'Copy', 'Copy'),
(422, 27, 'Star', 'Star'),
(423, 27, 'Item was starred', 'Item was starred'),
(424, 27, 'Item was un starred', 'Item was un starred'),
(425, 27, 'User account was created', 'User account was created'),
(426, 27, 'Clear', 'Clear'),
(427, 27, 'Abort', 'Abort'),
(428, 27, 'Search for files', 'Search for files'),
(429, 27, 'Username', 'Username'),
(430, 27, 'New Password', 'New Password'),
(431, 27, 'User already updated', 'User already updated'),
(432, 27, 'Password Confirm Error', 'Password Confirm Error'),
(433, 27, 'User was updated', 'User was updated'),
(434, 27, 'Uploaded Successfully', 'Uploaded Successfully'),
(435, 27, 'Please login again', 'Please login again'),
(436, 27, 'Reset link sent', 'Reset link sent'),
(437, 27, 'User email exist', 'User email exist'),
(438, 27, 'User email not exist', 'User email not exist'),
(439, 27, 'Create', 'Create'),
(440, 27, 'Folder name', 'Folder name'),
(441, 27, 'Select the file folders', 'Select the file folders'),
(442, 27, 'dropbox', 'dropbox'),
(443, 27, 'drive', 'drive'),
(444, 27, '', ''),
(445, 0, 'file_shared_via_email', 'file_shared_via_email'),
(446, 27, 'email_share_subject', 'The file was shared with you'),
(447, 0, 'email_share_subject', 'The file was shared with you'),
(448, 27, 'you have unread notes', 'You have %notes% notifications');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_notifications`
--

CREATE TABLE `buckty_notifications` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `content_id` int(55) DEFAULT NULL,
  `content_type` varchar(200) DEFAULT NULL,
  `body` varchar(255) NOT NULL,
  `is_from` int(55) DEFAULT NULL,
  `is_to` int(55) NOT NULL,
  `is_read` int(55) DEFAULT '0',
  `is_notified` int(10) DEFAULT '0',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100 ;

--
-- Dump dei dati per la tabella `buckty_notifications`
--

INSERT INTO `buckty_notifications` (`id`, `content_id`, `content_type`, `body`, `is_from`, `is_to`, `is_read`, `is_notified`, `created_at`) VALUES
(98, 128, 'file', 'Shared file with you', 31, 6, 1, 1, '2016-01-22 14:01:49'),
(99, 134, 'google', 'Google drive file was saved to root', NULL, 6, 1, 1, '2016-01-22 14:01:31');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_pages`
--

CREATE TABLE `buckty_pages` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `page_slug` varchar(255) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `page_body` text NOT NULL,
  `page_position` int(55) DEFAULT '0',
  `page_status` int(55) DEFAULT '1',
  `in_sitemap` int(55) DEFAULT '1',
  `in_footer` int(55) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dump dei dati per la tabella `buckty_pages`
--

INSERT INTO `buckty_pages` (`id`, `page_slug`, `page_name`, `page_body`, `page_position`, `page_status`, `in_sitemap`, `in_footer`) VALUES
(5, 'privacy_policy', 'Privacy & Policy', '<p><strong>Lorem ipsum</strong> dolor sit amet, consectetur adipiscing elit. Nam eleifend semper ligula, non porta nunc dapibus varius. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum luctus rutrum dui ac ultrices. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Cras ac luctus arcu. Integer ac congue ante, nec egestas orci. Fusce molestie auctor dui, quis volutpat lacus. Donec enim ipsum, dictum id nunc at, fermentum tincidunt diam. Sed elementum, lacus et bibendum rutrum, ipsum leo rutrum mauris, non convallis lectus orci in ipsum.</p>\n\n<blockquote>\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut vitae ipsum ac sapien elementum pharetra eget non est. Nulla vitae volutpat mauris. Proin et dui elementum, porttitor purus quis, feugiat metus. Integer volutpat leo a neque tristique, eu pellentesque lectus egestas. Nulla sagittis quam ipsum, vel volutpat nunc dapibus tempor. Fusce vitae dolor eget enim luctus vestibulum ut vitae metus. Curabitur orci libero, rhoncus nec velit a, convallis feugiat sem. Aenean nec metus quis libero tempus condimentum imperdiet in elit. Vestibulum tempor commodo rutrum. Pellentesque quis sollicitudin ante. Cras turpis sem, cursus sed pretium tempor, venenatis at risus.</p>\n</blockquote>\n\n<p>Vivamus risus odio, blandit sed eleifend ultricies, rhoncus in felis. Cras diam lacus, mollis vulputate lorem id, suscipit venenatis nulla. Nam facilisis, eros vel gravida convallis, orci diam gravida ex, et mollis orci magna in ligula. Sed ac tempor ante. Aliquam dignissim metus a quam hendrerit, a efficitur dolor lobortis. Mauris magna augue, vestibulum et erat rhoncus, tempor egestas tellus. Ut lobortis id erat ut posuere. Cras feugiat venenatis lorem. Vivamus elementum blandit arcu, sed lacinia urna dictum at. Praesent vehicula nibh id ligula aliquam, eu laoreet nisi luctus. Morbi nulla eros, commodo at vulputate eu, molestie quis orci.</p>\n\n<p><s><em>Proin tincidunt elementum ligula, at tincidunt sem egestas at. Cras pretium maximus quam sit amet maximus. Curabitur tincidunt lacus sed neque euismod accumsan. </em></s>Sed dictum orci in felis sollicitudin consequat. Duis euismod sodales ante et posuere. Nunc nisl arcu, imperdiet sit amet tellus vitae, consectetur tristique nisl. Curabitur eget hendrerit felis, ut pharetra orci. Etiam faucibus sollicitudin nisl vitae faucibus. Aenean vitae lacus quis purus ultricies venenatis in id nisl. In tristique libero vitae massa fringilla, vitae vulputate diam sagittis. In porta, turpis a egestas ultrices, elit odio malesuada dolor, eu vehicula sem massa sed neque. Curabitur a diam pretium massa ornare auctor vel sit amet purus. Proin luctus neque lacinia felis ultrices sagittis. Nam malesuada laoreet egestas.</p>\n\n<p> </p>\n\n<ol>\n <li>hello</li>\n <li>list</li>\n <li>numbers</li>\n <li>num</li>\n</ol>\n\n<p>Aenean dui lectus, ultricies vel volutpat sed, tristique vel tortor. Sed ac interdum sapien. Praesent ut sagittis sem, non porttitor velit. Vestibulum ultrices dui at est tristique, sed gravida arcu bibendum. Donec ut gravida justo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>\n\n<p> </p>\n\n<p>Nullam sed tellus gravida ligula convallis semper ac ac ex. Pellentesque elementum, mi quis dictum pellentesque, orci lacus ultricies nunc, et tempor lorem ante quis dui. Etiam id tellus mattis, sollicitudin turpis eget, tincidunt erat. Quisque fermentum arcu mollis erat posuere dignissim. Aenean sapien ligula, scelerisque ut sem sed, volutpat posuere mi. Sed libero lacus, malesuada et eros sit amet, consequat accumsan justo. Etiam consequat semper justo ut laoreet. Pellentesque ac erat nec nisi suscipit congue vel eget nibh. Mauris faucibus rhoncus ipsum id egestas. Donec convallis mi iaculis, pellentesque lacus at, sodales dui.</p>\n', 0, 1, 1, 1),
(6, 'terms_conditions', 'Terms & Conditions', '<p><img alt="" src="http://ibusiness.themeple.co/wp-content/uploads/2013/02/pagebuilder.gif"></p>\n\n<p> </p>\n\n<h1>Privacy & Policy</h1>\n\n<p><strong>Lorem ipsum</strong> dolor sit amet, consectetur adipiscing elit. Nam eleifend semper ligula, non porta nunc dapibus varius. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum luctus rutrum dui ac ultrices. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Cras ac luctus arcu. Integer ac congue ante, nec egestas orci. Fusce molestie auctor dui, quis volutpat lacus. Donec enim ipsum, dictum id nunc at, fermentum tincidunt diam. Sed elementum, lacus et bibendum rutrum, ipsum leo rutrum mauris, non convallis lectus orci in ipsum.</p>\n\n<blockquote>\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut vitae ipsum ac sapien elementum pharetra eget non est. Nulla vitae volutpat mauris. Proin et dui elementum, porttitor purus quis, feugiat metus. Integer volutpat leo a neque tristique, eu pellentesque lectus egestas. Nulla sagittis quam ipsum, vel volutpat nunc dapibus tempor. Fusce vitae dolor eget enim luctus vestibulum ut vitae metus. Curabitur orci libero, rhoncus nec velit a, convallis feugiat sem. Aenean nec metus quis libero tempus condimentum imperdiet in elit. Vestibulum tempor commodo rutrum. Pellentesque quis sollicitudin ante. Cras turpis sem, cursus sed pretium tempor, venenatis at risus.</p>\n</blockquote>\n\n<p>Vivamus risus odio, blandit sed eleifend ultricies, rhoncus in felis. Cras diam lacus, mollis vulputate lorem id, suscipit venenatis nulla. Nam facilisis, eros vel gravida convallis, orci diam gravida ex, et mollis orci magna in ligula. Sed ac tempor ante. Aliquam dignissim metus a quam hendrerit, a efficitur dolor lobortis. Mauris magna augue, vestibulum et erat rhoncus, tempor egestas tellus. Ut lobortis id erat ut posuere. Cras feugiat venenatis lorem. Vivamus elementum blandit arcu, sed lacinia urna dictum at. Praesent vehicula nibh id ligula aliquam, eu laoreet nisi luctus. Morbi nulla eros, commodo at vulputate eu, molestie quis orci.</p>\n\n<p><s><em>Proin tincidunt elementum ligula, at tincidunt sem egestas at. Cras pretium maximus quam sit amet maximus. Curabitur tincidunt lacus sed neque euismod accumsan. </em></s>Sed dictum orci in felis sollicitudin consequat. Duis euismod sodales ante et posuere. Nunc nisl arcu, imperdiet sit amet tellus vitae, consectetur tristique nisl. Curabitur eget hendrerit felis, ut pharetra orci. Etiam faucibus sollicitudin nisl vitae faucibus. Aenean vitae lacus quis purus ultricies venenatis in id nisl. In tristique libero vitae massa fringilla, vitae vulputate diam sagittis. In porta, turpis a egestas ultrices, elit odio malesuada dolor, eu vehicula sem massa sed neque. Curabitur a diam pretium massa ornare auctor vel sit amet purus. Proin luctus neque lacinia felis ultrices sagittis. Nam malesuada laoreet egestas.</p>\n\n<p> </p>\n\n<ol>\n <li>hello</li>\n <li>list</li>\n <li>numbers</li>\n <li>num</li>\n</ol>\n\n<p>Aenean dui lectus, ultricies vel volutpat sed, tristique vel tortor. Sed ac interdum sapien. Praesent ut sagittis sem, non porttitor velit. Vestibulum ultrices dui at est tristique, sed gravida arcu bibendum. Donec ut gravida justo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>\n\n<p> </p>\n\n<p>Nullam sed tellus gravida ligula convallis semper ac ac ex. Pellentesque elementum, mi quis dictum pellentesque, orci lacus ultricies nunc, et tempor lorem ante quis dui. Etiam id tellus mattis, sollicitudin turpis eget, tincidunt erat. Quisque fermentum arcu mollis erat posuere dignissim. Aenean sapien ligula, scelerisque ut sem sed, volutpat posuere mi. Sed libero lacus, malesuada et eros sit amet, consequat accumsan justo. Etiam consequat semper justo ut laoreet. Pellentesque ac erat nec nisi suscipit congue vel eget nibh. Mauris faucibus rhoncus ipsum id egestas. Donec convallis mi iaculis, pellentesque lacus at, sodales dui.</p>\n', 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_perms`
--

CREATE TABLE `buckty_perms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `definition` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_perm_to_group`
--

CREATE TABLE `buckty_perm_to_group` (
  `perm_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`perm_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_perm_to_user`
--

CREATE TABLE `buckty_perm_to_user` (
  `perm_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`perm_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_plugins`
--

CREATE TABLE `buckty_plugins` (
  `plugin_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_system_name` varchar(255) NOT NULL,
  `plugin_name` varchar(255) NOT NULL,
  `plugin_uri` varchar(120) DEFAULT NULL,
  `plugin_version` varchar(30) NOT NULL,
  `plugin_description` text,
  `plugin_author` varchar(120) DEFAULT NULL,
  `plugin_author_uri` varchar(120) DEFAULT NULL,
  `plugin_data` longtext,
  PRIMARY KEY (`plugin_id`),
  UNIQUE KEY `plugin_index` (`plugin_system_name`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dump dei dati per la tabella `buckty_plugins`
--

INSERT INTO `buckty_plugins` (`plugin_id`, `plugin_system_name`, `plugin_name`, `plugin_uri`, `plugin_version`, `plugin_description`, `plugin_author`, `plugin_author_uri`, `plugin_data`) VALUES
(1, 'cimarkdown', '', '', '', '', '', '', NULL),
(2, 'helloworld', '', '', '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_pms`
--

CREATE TABLE `buckty_pms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) unsigned NOT NULL,
  `receiver_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `date_sent` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `full_index` (`id`,`sender_id`,`receiver_id`,`date_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_relations`
--

CREATE TABLE `buckty_relations` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `author_id` int(55) NOT NULL,
  `content_id` int(55) NOT NULL,
  `content_parent` varchar(255) NOT NULL,
  `content_type` varchar(255) NOT NULL,
  `permission` int(1) NOT NULL DEFAULT '1',
  `shared` int(55) NOT NULL DEFAULT '0',
  `starred` int(55) NOT NULL DEFAULT '0',
  `owner` int(55) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3011 ;

--
-- Dump dei dati per la tabella `buckty_relations`
--

INSERT INTO `buckty_relations` (`id`, `author_id`, `content_id`, `content_parent`, `content_type`, `permission`, `shared`, `starred`, `owner`) VALUES
(2992, 6, 187, '0', 'folder', 1, 0, 0, 1),
(2993, 31, 187, '0', 'folder', 1, 1, 0, 0),
(2994, 6, 126, '1aJyzM2U2A5uKIj', 'file', 1, 0, 0, 1),
(2995, 31, 126, '1aJyzM2U2A5uKIj', 'file', 2, 1, 0, 0),
(2996, 6, 127, '0', 'file', 1, 0, 0, 1),
(2997, 31, 128, '0', 'file', 1, 0, 0, 1),
(2999, 31, 188, '0', 'folder', 1, 0, 0, 1),
(3000, 6, 188, '0', 'folder', 1, 1, 0, 0),
(3001, 6, 129, '0', 'file', 1, 0, 0, 1),
(3002, 31, 130, '0', 'file', 1, 0, 0, 1),
(3003, 6, 130, '0', 'file', 1, 1, 0, 0),
(3004, 31, 131, '0', 'file', 1, 0, 0, 1),
(3005, 6, 131, '0', 'file', 1, 1, 0, 0),
(3007, 6, 132, '0', 'file', 1, 0, 0, 1),
(3008, 6, 133, '0', 'file', 1, 0, 0, 1),
(3009, 6, 128, '0', 'file', 1, 1, 0, 0),
(3010, 6, 134, '0', 'file', 1, 0, 0, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_settings`
--

CREATE TABLE `buckty_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dump dei dati per la tabella `buckty_settings`
--

INSERT INTO `buckty_settings` (`id`, `name`, `value`) VALUES
(1, 'site_url', 'https://bucktytest.com/'),
(2, 'site_name', 'Buckty'),
(3, 'admin_email', 'arshsiinghmultani@gmail.com'),
(4, 'activation', 'No'),
(6, 'social', 'Yes'),
(8, 'enable_recaptcha', 'No'),
(9, 'recaptcha_key', '6Lc1owMTAAAAANWt1ed60hy-ER5cU7v_lB8xYJ35'),
(10, 'recaptcha_secret', '6Lc1owMTAAAAAH_k1SbooR5Vk8HY5SG9jyGz84qG'),
(11, 'site_keywords', 'Buckty,upload files,anywhere,dropbox,drive'),
(12, 'site_description', 'Upload Anything & Share Everything'),
(13, 'upload_limit', '4600000000'),
(14, 'allowed_extensions', 'a:1:{i:0;s:1:"*";}'),
(19, 'language_keys', 'a:92:{i:0;s:5:"Login";i:1;s:8:"Register";i:2;s:23:"Your account was banned";i:3;s:44:"We have sent an email with confirmation link";i:4;s:6:"Upload";i:5;s:4:"helo";i:6;s:20:"Something went wrong";i:7;s:19:"Invalid Folder name";i:8;s:8:"Settings";i:9;s:6:"Logout";i:10;s:12:"Edit Aavatar";i:11;s:11:"Admin Panel";i:12;s:13:"Create Folder";i:13;s:12:"Upload Files";i:14;s:9:"Clear All";i:15;s:6:"Reload";i:16;s:4:"Sort";i:17;s:4:"View";i:18;s:12:"Delete Files";i:19;s:10:"Move Files";i:20;s:4:"Size";i:21;s:6:"Shared";i:22;s:9:"All Files";i:23;s:6:"Recent";i:24;s:5:"Trash";i:25;s:28:"No files or folders in trash";i:26;s:7:"Starred";i:27;s:13:"Notifications";i:28;s:20:"Something is loading";i:29;s:10:"Select All";i:30;s:8:"Add star";i:31;s:11:"Remove Star";i:32;s:6:"Rename";i:33;s:6:"Delete";i:34;s:8:"Download";i:35;s:4:"Move";i:36;s:7:"Details";i:37;s:9:"Copy Link";i:38;s:10:"Dropbox it";i:39;s:12:"Google Drive";i:40;s:5:"Share";i:41;s:6:"Cancel";i:42;s:4:"Done";i:43;s:15:"Item was shared";i:44;s:14:"Folder created";i:45;s:8:"My Files";i:46;s:13:"Dropbox Files";i:47;s:13:"Email Address";i:48;s:23:"Connect to google drive";i:49;s:18:"Connect to dropbox";i:50;s:12:"New Passowrd";i:51;s:13:"New Passoword";i:52;s:16:"Confirm Password";i:53;s:6:"Update";i:54;s:5:"Reset";i:55;s:16:"Recovery Options";i:56;s:12:"Active Since";i:57;s:10:"Disconnect";i:58;s:13:"Creation Date";i:59;s:4:"Date";i:60;s:17:"Size Bigger First";i:61;s:16:"Size Small First";i:62;s:7:"Preview";i:63;s:4:"More";i:64;s:4:"Copy";i:65;s:4:"Star";i:66;s:16:"Item was starred";i:67;s:19:"Item was un starred";i:68;s:24:"User account was created";i:69;s:5:"Clear";i:70;s:5:"Abort";i:71;s:16:"Search for files";i:72;s:11:"Edit_Avatar";i:73;s:8:"Username";i:74;s:12:"New Password";i:75;s:20:"User already updated";i:76;s:22:"Password Confirm Error";i:77;s:16:"User was updated";i:78;s:21:"Uploaded Successfully";i:79;s:18:"Please login again";i:80;s:15:"Reset link sent";i:81;s:16:"User email exist";i:82;s:20:"User email not exist";i:83;s:6:"Create";i:84;s:11:"Folder name";i:85;s:23:"Select the file folders";i:86;s:7:"dropbox";i:87;s:5:"drive";i:88;s:0:"";i:89;s:21:"file_shared_via_email";i:90;s:19:"email_share_subject";i:91;s:21:"you have unread notes";}'),
(20, 'max_file_size', '100000000'),
(21, 'email_activation', '0'),
(22, 'facebook', 'a:3:{s:2:"id";s:15:"734484063352431";s:6:"secret";s:32:"2b81e28aeccb28e8f2b16ecfd6975f1b";s:10:"activation";s:1:"1";}'),
(23, 'google', 'a:4:{s:2:"id";s:73:"1041279575731-gdcniaopg02rtptamcc1ptaebi81kc6k.apps.googleusercontent.com";s:6:"secret";s:24:"jmScHVbjB0mqjwOnJ3oWkwmQ";s:10:"activation";s:1:"1";s:16:"drive_activation";s:1:"1";}'),
(24, 'twitter', 'a:3:{s:2:"id";s:25:"zeSLbDGIBIdrFaRGdoWta5HZD";s:6:"secret";s:50:"nMtX3KpwLLRMI9fciVvKkEuZPcpi3erXzULNx0qcvBxs8JdJ7H";s:10:"activation";s:1:"1";}'),
(25, 'dropbox', 'a:4:{s:2:"id";s:15:"8y7gzx6fvts8kpv";s:6:"secret";s:15:"1gpxiimpwjjyukn";s:7:"appname";s:6:"Buckty";s:10:"activation";s:1:"1";}'),
(26, 'register_active', '1'),
(27, 'site_home_tagline', 'Upload Anything & Share Everything'),
(28, 'site_home_description', 'Get your all files at one platform. Images / Videos / any other files.'),
(29, 'smtp_host', 'ssl://mail.smtp2go.com'),
(30, 'smtp_port', '8465'),
(31, 'smtp_user', 'no-reply@buckty.com'),
(32, 'smtp_password', 'arsh4ever'),
(33, 'disqus', 'a:1:{s:9:"shortname";s:6:"buckty";}');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_users`
--

CREATE TABLE `buckty_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `sp_identity` varchar(255) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `last_login_attempt` datetime DEFAULT NULL,
  `forgot_exp` text,
  `remember_time` datetime DEFAULT NULL,
  `remember_exp` text,
  `verification_code` text,
  `totp_secret` varchar(16) DEFAULT NULL,
  `ip_address` text,
  `login_attempts` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Dump dei dati per la tabella `buckty_users`
--

INSERT INTO `buckty_users` (`id`, `email`, `pass`, `name`, `sp_identity`, `banned`, `last_login`, `last_activity`, `last_login_attempt`, `forgot_exp`, `remember_time`, `remember_exp`, `verification_code`, `totp_secret`, `ip_address`, `login_attempts`) VALUES
(6, 'punjabirola@gmail.com', '57b1e54ce0d7b1ec3b3e08e59e5c8ccc84356700cbfa21dbe77e9143aea25ef9', 'arshmultani', '0', 0, '2016-01-22 09:58:56', '2016-01-22 09:58:56', '2016-01-22 09:00:00', NULL, '2016-01-23 00:00:00', '3MEubwJlTf47Fa2v', '', NULL, '127.0.0.1', NULL),
(27, 'singhmultaniarsh1995@gmail.com', '9ccdece225ca073dbea31e860fa6d6a984e241db21bdc2ef9729dd246b0ee1fc', 'multanisingh', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 0),
(28, 'meranaam@gmail.com', 'b49c380da6e9ca1425e7c79ab751c582d778d27278874d2fd559feb7e4563de6', 'meranamlakhan', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 0),
(29, 'maniga@gmail.com', '4a59979fd1c28bcf2295c8dc7342eb1e62c79359c90a9566186d4115fb1b9332', 'maniga', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 0),
(31, 'arshsinghmultani@gmail.com', '8421056511da832c0a28b4687fb6155dbfc45a81c142bcb991eea9758801d36b', 'arshakshi', NULL, 0, '2016-01-22 09:59:31', '2016-01-22 09:59:31', '2016-01-22 09:00:00', NULL, '2016-01-11 00:00:00', 'bOgwHUWB2KREnArJ', 'Pjy43vwjmoecVSd68nKrAL5IrZ6q10U5IEz1bpAGlNsTDh9iy2tMkfN0JJhS7LHuo27nxiqCPfc4XguwetFWd9kUG8QZWYOvXHBC', NULL, '127.0.0.1', NULL),
(37, 'adminyeah@gmail.com', '9a425ff2ab731c61104e3224205a5ff4ccc99840bd4376b5f9e0ebdae8cd6d57', 'mynewadministrator', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(41, '1137883510@buckty.com', 'fa3772667a09fb30b5c5a75c5d1d2519a2b37fa6701feb6b5c089fadacef97b2', 'imarshmultani3', '1108408098', 0, '2016-01-17 22:12:30', '2016-01-17 22:12:30', '2016-01-17 22:00:00', NULL, '2016-01-20 00:00:00', 'kanxpuJ54FLofgIM', NULL, NULL, '127.0.0.1', NULL),
(42, '658850186@buckty.com', '32691692d974592bb33f7872cdf351897dc2db96708cfa8494715f838790aca9', 'arshmultani2', '900730546643546', 0, '2016-01-20 23:54:08', '2016-01-20 23:54:08', '2016-01-20 23:00:00', NULL, '2016-01-23 00:00:00', 'hgt6RCVA8KkPYdUm', NULL, NULL, '127.0.0.1', NULL),
(43, 'meenakshi.php.developer@gmail.com', '47b810739480d8d982085cf4fb34e673c61cb71a531887ebd9ce7616ab309e5a', 'meenakshigoel2', '103035274402696480042', 0, '2016-01-20 15:40:36', '2016-01-20 15:40:36', '2016-01-20 15:00:00', NULL, '2016-01-23 00:00:00', 'Xo2SVWUaOtzjbuBs', NULL, NULL, '127.0.0.1', NULL),
(45, 'imarshmultani@gmail.com', '492d7ef63d4a05f1a026fd57c862c0e810e17d321dec747eebc361d1bfeb8068', 'imarshmultani', NULL, 0, '2016-01-17 22:33:23', '2016-01-17 22:33:23', '2016-01-17 22:00:00', NULL, NULL, NULL, '', NULL, '127.0.0.1', NULL),
(46, 'lollololololol@gmail.com', '315d44b31422a6532ad005a61d4fd14faac55649cf72d47abd8da9f984fe066c', 'latenightpart', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_user_to_group`
--

CREATE TABLE `buckty_user_to_group` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `buckty_user_to_group`
--

INSERT INTO `buckty_user_to_group` (`user_id`, `group_id`) VALUES
(6, 1),
(6, 3),
(32, 3),
(33, 3),
(34, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(45, 3),
(46, 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_user_variables`
--

CREATE TABLE `buckty_user_variables` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;

--
-- Dump dei dati per la tabella `buckty_user_variables`
--

INSERT INTO `buckty_user_variables` (`id`, `user_id`, `data_key`, `value`) VALUES
(1, 2, 'profile_pic', 'a:2:{s:5:"large";s:96:"http://localhost/ultimate_board//uploads/profile_pics/large/db178724ecfcef7c4c2b81738f2ef509.jpg";s:6:"medium";s:96:"http://localhost/ultimate_board/uploads/profile_pics/medium/db178724ecfcef7c4c2b81738f2ef509.jpg";}'),
(3, 4, 'user_hash', 'fhtBS06t2N'),
(6, 6, 'profile_pic', 'a:2:{s:5:"large";s:82:"/application/views/uploads/profile_pics/large/f698df3ad2ef8206ddb5249036327c29.jpg";s:6:"medium";s:82:"application/views/uploads/profile_pics/medium/f698df3ad2ef8206ddb5249036327c29.jpg";}'),
(7, 6, 'user_hash', 'NifB4vP40s'),
(8, 7, 'profile_pic', 'a:2:{s:5:"large";s:40:"http://buckty.com/assets/img/default.jpg";s:6:"medium";s:40:"http://buckty.com/assets/img/default.jpg";}'),
(9, 7, 'user_hash', 'qjM668uQ7B'),
(10, 8, 'profile_pic', 'a:2:{s:5:"large";s:71:"https://graph.facebook.com/900730546643546/picture?width=150&height=150";s:6:"medium";s:71:"https://graph.facebook.com/900730546643546/picture?width=150&height=150";}'),
(11, 8, 'user_hash', 'Q93XVQYm8Y'),
(12, 9, 'profile_pic', 'a:2:{s:5:"large";s:92:"https://lh3.googleusercontent.com/-HHbTvt9dM18/AAAAAAAAAAI/AAAAAAAABLY/m5fIcq9jbHY/photo.jpg";s:6:"medium";s:92:"https://lh3.googleusercontent.com/-HHbTvt9dM18/AAAAAAAAAAI/AAAAAAAABLY/m5fIcq9jbHY/photo.jpg";}'),
(13, 9, 'user_hash', 'hyJeOYn6FU'),
(14, 10, 'profile_pic', 'a:2:{s:5:"large";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";s:6:"medium";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";}'),
(15, 10, 'user_hash', '9oomi7tDG7'),
(18, 12, 'profile_pic', 'a:2:{s:5:"large";s:92:"https://lh3.googleusercontent.com/-HHbTvt9dM18/AAAAAAAAAAI/AAAAAAAABLY/m5fIcq9jbHY/photo.jpg";s:6:"medium";s:92:"https://lh3.googleusercontent.com/-HHbTvt9dM18/AAAAAAAAAAI/AAAAAAAABLY/m5fIcq9jbHY/photo.jpg";}'),
(19, 12, 'user_hash', '3zSMoZnScE'),
(24, 22, 'user_hash', 'Z77TynRofG'),
(25, 22, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(26, 23, 'user_hash', 'tud7MljkRM'),
(27, 23, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(28, 24, 'user_hash', 'KbS85COwV7'),
(29, 24, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(32, 26, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(33, 26, 'user_hash', '2mO2v9OtWC'),
(34, 27, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(35, 27, 'user_hash', 'lKKFshvXZD'),
(36, 28, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(37, 28, 'user_hash', '14KdtiWxa8'),
(43, 29, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(44, 29, 'user_hash', 'WBNv4stgUx'),
(47, 26, 'dropbox_token', 'FSGzZDzfoGEAAAAAAAAAeHXaXPbPCpOvZsLMnoC-98QZ6FMZOa71qVGSe9dmfmQ0'),
(62, 31, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(63, 31, 'user_hash', '8JSV9o6bYr'),
(64, 31, 'gdrive_token', '{"access_token":"ya29.YwJ3o8GVHFS3ilOFPzxEJb_o_tpHDE9MZeJVMl6Xcb5a1m9k2bi8_tMAQmvvmcmJvTYr","token_type":"Bearer","expires_in":3600,"created":1452255138}'),
(65, 31, 'dropbox_token', '1_c8D1H-QtAAAAAAAAAApuZdMRoGPZzemkWEJfED-PgezIlN0rONDPTbm92wpGi2'),
(67, 6, 'dropbox_token', '1_c8D1H-QtAAAAAAAAAAqCncu13HV2zs_HUMBxQXFW5nHChcyd9eNCjj9JA-jI7n'),
(81, 6, 'gdrive_token', '{"access_token":"ya29.cQIY6PO1K21j2sfI9qTGIRMYKq4I4xKmWL4hBoxejj7bIUQPiBHoM1P54Q0IHeCIDwUiJA","token_type":"Bearer","expires_in":3598,"created":1453469025}'),
(84, 37, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(85, 37, 'user_hash', 'fwKG0pmVVV'),
(86, 38, 'profile_pic', 'a:2:{s:5:"large";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";s:6:"medium";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";}'),
(87, 38, 'user_hash', 'DzQkfAx5lz'),
(88, 39, 'profile_pic', 'a:2:{s:5:"large";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";s:6:"medium";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";}'),
(89, 39, 'user_hash', 'nVpHdHzXSg'),
(90, 40, 'profile_pic', 'a:2:{s:5:"large";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";s:6:"medium";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";}'),
(91, 40, 'user_hash', 'KcWN9yDkAC'),
(92, 41, 'profile_pic', 'a:2:{s:5:"large";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";s:6:"medium";s:74:"http://pbs.twimg.com/profile_images/656131152860676096/E01S2hAl_normal.jpg";}'),
(93, 41, 'user_hash', 'Sf0Z3VE8Dm'),
(94, 42, 'profile_pic', 'a:2:{s:5:"large";s:71:"https://graph.facebook.com/900730546643546/picture?width=150&height=150";s:6:"medium";s:71:"https://graph.facebook.com/900730546643546/picture?width=150&height=150";}'),
(95, 42, 'user_hash', '2XtQNCCdqO'),
(96, 42, 'dropbox_token', '1_c8D1H-QtAAAAAAAAAAwN8aLURNTLAKK0RBPgX7l5P_JDPEzNSkQV-YZQRZrKHq'),
(97, 42, 'gdrive_token', '{"access_token":"ya29.bAIVwcp-zTR-MAuOcmCHsuGdUx7qixlif3Oc9p-1tZtA4tKccyUG_e8vvYQ9PKA6OLtw","token_type":"Bearer","expires_in":3600,"created":1453065350}'),
(98, 43, 'profile_pic', 'a:2:{s:5:"large";s:92:"https://lh3.googleusercontent.com/-4lS46NhOUWQ/AAAAAAAAAAI/AAAAAAAAABU/HGr3mcQg5Rw/photo.jpg";s:6:"medium";s:92:"https://lh3.googleusercontent.com/-4lS46NhOUWQ/AAAAAAAAAAI/AAAAAAAAABU/HGr3mcQg5Rw/photo.jpg";}'),
(99, 43, 'user_hash', 'NaNxeWSDqi'),
(100, 43, 'gdrive_token', '{"access_token":"ya29.bAKOP4C2guQSt-gTWSWIL-cTj-icmHsPfoAWDg0cczz0uT-7AF7TSsqUKT74AARH0LtX","token_type":"Bearer","expires_in":3600,"created":1453065754}'),
(103, 45, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(104, 45, 'user_hash', 'YvpXjzPTdx'),
(105, 46, 'profile_pic', 'a:2:{s:5:"large";s:50:"application/views/uploads/site_uploads/default.jpg";s:6:"medium";s:50:"application/views/uploads/site_uploads/default.jpg";}'),
(106, 46, 'user_hash', '3MBXyt2Vmt');
