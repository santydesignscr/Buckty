SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_api`
--
DROP TABLE IF EXISTS `buckty_api`;
CREATE TABLE `buckty_api` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `authorization_key` varchar(255) NOT NULL,
  `user_key` varchar(255) NOT NULL,
  `absolute_key` varchar(255) NOT NULL,
  `blocked` int(10) DEFAULT '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Struttura della tabella `buckty_api_logs`
--
DROP TABLE IF EXISTS  `buckty_api_logs`;
CREATE TABLE `buckty_api_logs` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `absolute_key` varchar(255) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `access_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_files`
--
DROP TABLE IF EXISTS `buckty_files`;
CREATE TABLE `buckty_files` (
  `ID` int(55) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `trashed` int(55) NOT NULL DEFAULT '0',
  `is_shared` int(55) DEFAULT '0',
  `protected` int(55) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

--
-- Struttura della tabella `buckty_files_data`
--
DROP TABLE IF EXISTS `buckty_files_data`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_folders`
--
DROP TABLE IF EXISTS `buckty_folders`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_groups`
--
DROP TABLE IF EXISTS `buckty_groups`;
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
DROP TABLE IF EXISTS `buckty_language`;
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
DROP TABLE IF EXISTS `buckty_language_data`;
CREATE TABLE `buckty_language_data` (
  `id` int(55) NOT NULL AUTO_INCREMENT,
  `lang_id` int(55) NOT NULL,
  `key_name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=603 ;

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
(104, 23, 'Something is loading', 'Caricamento in corso'),
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
(120, 23, 'Folder created', 'Folder Created'),
(121, 23, 'My Files', 'Miei File'),
(122, 23, 'Dropbox Files', 'I files nel dropbox'),
(123, 23, 'Email Address', 'Email address'),
(124, 23, 'Connect to google drive', 'Connetti con google drive'),
(125, 23, 'Connect to dropbox', 'Connetti con dropbox'),
(126, 23, 'New Passowrd', 'Nuovo Passowrd'),
(127, 23, 'New Passoword', 'Nuovo Passoword'),
(128, 23, 'Confirm Password', 'Conferma Password'),
(129, 23, 'Update', 'Aggiorna'),
(130, 23, 'Reset', 'Resetta'),
(131, 23, 'Recovery Options', 'Recovery options'),
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
(150, 23, 'Item was starred', 'Hai messo preferito a un file'),
(151, 23, 'Item was un starred', 'File rimosso dai preferiti'),
(152, 23, 'User account was created', 'Account è stato creato'),
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
(170, 23, 'Please login again', 'Please Login Again'),
(171, 23, 'Reset link sent', 'Link per risettare la password è stato inviato su sua email. '),
(172, 23, 'User email exist', 'Utente con quest''email esiste già.'),
(173, 23, 'User email not exist', 'Utente con email fornita , not esiste nel nostro database'),
(174, 23, 'Create', 'Crea'),
(181, 23, 'Folder name', 'Nome della cartella'),
(182, 23, 'Select the file folders', 'Seleziona files/cartelle'),
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
(401, 27, 'Folder created', 'Folder Created'),
(402, 27, 'My Files', 'My Files'),
(403, 27, 'Dropbox Files', 'Dropbox Files'),
(404, 27, 'Email Address', 'Email address'),
(405, 27, 'Connect to google drive', 'Connect to google drive'),
(406, 27, 'Connect to dropbox', 'Connect to dropbox'),
(407, 27, 'New Passowrd', 'New Passowrd'),
(408, 27, 'New Passoword', 'New Passoword'),
(409, 27, 'Confirm Password', 'Confirm Password'),
(410, 27, 'Update', 'Update'),
(411, 27, 'Reset', 'Reset'),
(412, 27, 'Recovery Options', 'Recovery options'),
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
(435, 27, 'Please login again', 'Please Login Again'),
(436, 27, 'Reset link sent', 'Reset link sent'),
(437, 27, 'User email exist', 'User email exist'),
(438, 27, 'User email not exist', 'User email not exist'),
(439, 27, 'Create', 'Create'),
(440, 27, 'Folder name', 'Folder name'),
(441, 27, 'Select the file folders', 'Select the file folders'),
(442, 27, 'dropbox', 'Dropbox Files'),
(443, 27, 'drive', 'Drive Files'),
(444, 27, '', ''),
(445, 0, 'file_shared_via_email', 'file_shared_via_email'),
(446, 27, 'email_share_subject', 'The file was shared with you'),
(447, 0, 'email_share_subject', 'The file was shared with you'),
(448, 27, 'you have unread notes', 'You have %notes% notifications'),
(449, 0, 'Save_to_folder', 'Add to folder'),
(450, 27, 'Save to folder', 'Save to folder'),
(451, 27, 'Api Access', 'Api Access'),
(452, 27, 'Name desc', 'Name Desc'),
(453, 27, 'Name asc', 'Name Asc'),
(454, 27, 'Arsh singh', 'Arsh singh'),
(455, 27, 'Change avatar', 'Change avatar'),
(456, 23, 'you have unread notes', 'Hai %notes% notifiche non lette'),
(457, 23, 'Save to folder', 'Save to folder'),
(458, 23, 'Api Access', 'Api Access'),
(459, 23, 'Name desc', 'Ordine alfabetico'),
(460, 23, 'Name asc', 'Ordine an-alfabetico'),
(461, 23, 'Arsh singh', 'Arsh singh'),
(462, 23, 'Change avatar', 'Cambia immagine'),
(463, 23, 'Change', 'Cambia'),
(464, 23, 'New', 'Nuovo'),
(465, 23, 'Close', 'Chiudi'),
(466, 23, 'Select files', 'Seleziona file'),
(467, 23, 'Are you sure', 'Sei sicuro'),
(468, 23, 'Yes', 'Si'),
(469, 23, 'No', 'No'),
(470, 23, 'Add people', 'Aggiungi persone'),
(471, 23, 'Email', 'Invia'),
(472, 23, 'Add pass', 'Metti password'),
(473, 23, 'Update password', 'Aggiorna password'),
(474, 23, 'Username or email', 'Nome utente o email'),
(475, 23, 'Can edit', 'Può modificare'),
(476, 23, 'Can view', 'Può vedere'),
(477, 23, 'Email addresses', 'Indrizzi email'),
(478, 23, 'Your message', 'Il tuo messaggio'),
(479, 23, 'Send', 'Invia'),
(480, 23, 'Permission', 'Permessi'),
(481, 23, 'Copied to clipboard', 'Link copiato'),
(482, 23, 'items are being trashed', 'file stanno per rimuovere , verrano disconessi anche gli utenti assocciati a questo file.'),
(483, 23, 'No users were found', 'Nessun utente è stato trovato'),
(484, 23, 'Already Shared With this user', 'Già condiviso con quest''utente'),
(485, 23, 'Invalid Folder', 'Cartella Invalida'),
(486, 23, 'Access Forbidden', 'Accesso non amesso'),
(487, 23, 'NOT FOUND', '404 Pagina non è stata trovata'),
(488, 23, 'File too large', 'File molto grande'),
(489, 23, 'Not enough space', 'Nessun spazio disponibile'),
(490, 23, 'Remember me', 'Tieni me loggato'),
(491, 23, 'Password', 'Password'),
(492, 23, 'Password confirm', 'Conferma password'),
(493, 23, 'Reset password', 'Aggiorna Password'),
(494, 23, 'items were removed', 'File è stato rimosso'),
(495, 23, 'item Was Restored', 'File è stato rispinto'),
(496, 23, 'Item was renamed', 'File è stato rinominato'),
(497, 23, 'items were removed permanently', 'I file sono stati rimossi'),
(498, 23, 'User was unlinked from item', 'Utente è stato rimosso dal file'),
(499, 23, 'No unread notifications', 'Non ci sono notifiche '),
(500, 23, 'Notification removed', 'Notifica è stata rimossa'),
(501, 23, 'Item was copied', 'File è stato copiato'),
(502, 23, 'Email addresses cant be empty', 'Indrizzi email non possono essere vuoti'),
(503, 23, 'link was sent', 'link è stato inviato'),
(504, 23, 'The password was updated', 'La password è stata modificata'),
(505, 23, 'Permission was changed', 'Permessi sono stati cambiati'),
(506, 23, 'invalid folder or file', 'File o cartella invalida'),
(507, 0, 'invalid_file', 'invalid_file'),
(508, 23, 'invalid file', 'File invalido'),
(509, 23, 'File not available', 'File non disponibile'),
(510, 23, 'file was saved to root', 'File sono stati salvati nella cartella root'),
(511, 23, 'Files were saved to root folder', 'File sono stati salvati nella cartella root'),
(512, 23, 'access was removed', 'accesso è stato rimosso'),
(513, 23, 'Folder was empty', 'Cartella era vuota'),
(514, 23, 'folder or files were uploaded to', 'Cartelle o files sono stati caricati su'),
(515, 23, 'was uploaded to', 'stato caricato su'),
(516, 23, 'Login again', 'Accedi di nuovo'),
(517, 23, 'Invalid api', 'Api invalido'),
(518, 23, 'was disabled', 'era stato disabilitato'),
(519, 23, 'Login again to google drive', 'Accedi di nuovo su google drive'),
(520, 27, 'Change', 'Change'),
(521, 27, 'New', 'New'),
(522, 27, 'Close', 'Close'),
(523, 27, 'Select files', 'Select files'),
(524, 27, 'Are you sure', 'Are you sure'),
(525, 27, 'Yes', 'Yes'),
(526, 27, 'No', 'No'),
(527, 27, 'Add people', 'Add people'),
(528, 27, 'Email', 'Email'),
(529, 27, 'Add pass', 'Add pass'),
(530, 27, 'Update password', 'Update password'),
(531, 27, 'Username or email', 'Username or email'),
(532, 27, 'Can edit', 'Can edit'),
(533, 27, 'Can view', 'Can view'),
(534, 27, 'Email addresses', 'Email addresses'),
(535, 27, 'Your message', 'Your message'),
(536, 27, 'Send', 'Send'),
(537, 27, 'Permission', 'Permission'),
(538, 27, 'Copied to clipboard', 'Copied to clipboard'),
(539, 27, 'items are being trashed', 'items are being trashed'),
(540, 27, 'No users were found', 'No users were found'),
(541, 27, 'Already Shared With this user', 'Already Shared With this user'),
(542, 27, 'Invalid Folder', 'Invalid Folder'),
(543, 27, 'Access Forbidden', 'Access Forbidden'),
(544, 27, 'NOT FOUND', 'NOT FOUND'),
(545, 27, 'File too large', 'File too large'),
(546, 27, 'Not enough space', 'Not enough space'),
(547, 27, 'Remember me', 'Remember me'),
(548, 27, 'Password', 'Password'),
(549, 27, 'Password confirm', 'Password confirm'),
(550, 27, 'Reset password', 'Reset password'),
(551, 27, 'items were removed', 'items were removed'),
(552, 27, 'item Was Restored', 'item Was Restored'),
(553, 27, 'Item was renamed', 'Item was renamed'),
(554, 27, 'items were removed permanently', 'items were removed permanently'),
(555, 27, 'User was unlinked from item', 'User was unlinked from item'),
(556, 27, 'No unread notifications', 'No unread notifications'),
(557, 27, 'Notification removed', 'Notification removed'),
(558, 27, 'Item was copied', 'Item was copied'),
(559, 27, 'Email addresses cant be empty', 'Email addresses cant be empty'),
(560, 27, 'link was sent', 'link was sent'),
(561, 27, 'The password was updated', 'The password was updated'),
(562, 27, 'Permission was changed', 'Permission was changed'),
(563, 27, 'invalid folder or file', 'invalid folder or file'),
(564, 27, 'invalid file', 'invalid file'),
(565, 27, 'File not available', 'File not available'),
(566, 27, 'file was saved to root', 'file was saved to root'),
(567, 27, 'Files were saved to root folder', 'Files were saved to root folder'),
(568, 27, 'access was removed', 'access was removed'),
(569, 27, 'Folder was empty', 'Folder was empty'),
(570, 27, 'folder or files were uploaded to', 'folder or files were uploaded to'),
(571, 27, 'was uploaded to', 'was uploaded to'),
(572, 27, 'Login again', 'Login again'),
(573, 27, 'Invalid api', 'Invalid api'),
(574, 27, 'was disabled', 'was disabled'),
(575, 27, 'Login again to google drive', 'Login again to google drive'),
(576, 27, 'Registration subject', 'Registration successful'),
(577, 27, 'Reset password subject', 'Reset password'),
(578, 27, 'Memory used label', 'USED'),
(579, 27, 'item was remove permanently', 'item was remove permanently'),
(580, 23, 'Registration subject', 'Registration subject'),
(581, 23, 'Reset password subject', 'Reset password subject'),
(582, 23, 'Memory used label', 'Memory used label'),
(583, 23, 'item was remove permanently', 'File è stato cancellato'),
(584, 27, 'item was moved', 'item was moved'),
(585, 27, 'item was removed permanently', 'item was removed permanently'),
(586, 27, 'Shared folder', 'Shared folder'),
(587, 27, 'Shared file', 'Shared file'),
(588, 27, 'Your account was activated', 'Your account was activated'),
(589, 27, 'Account cant be activated', 'Account cant be activated'),
(590, 27, 'welcome to site', 'WELCOME TO BUCKTY'),
(591, 27, 'Account verification subject', 'Verify your account'),
(592, 27, 'Username already exsits', 'Username already exsits'),
(593, 27, 'Email already exsits', 'Email already exsits'),
(594, 27, 'invalid email address', 'invalid email address'),
(595, 27, 'invalid username', 'invalid username'),
(596, 27, 'invalid password', 'invalid password'),
(597, 27, 'Password and confirm password does not match', 'Password and confirm password does not match'),
(598, 27, 'Your email address', 'Your email address'),
(599, 27, 'Your password was changed', 'Your password was changed'),
(600, 27, 'Wrong details provided', 'Wrong details provided'),
(601, 27, 'Accept terms', 'Accept terms & conditions'),
(602, 27, 'items were moved', 'items were moved');

--
-- Struttura della tabella `buckty_notifications`
--
DROP TABLE IF EXISTS `buckty_notifications` ;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_pages`
--
DROP TABLE IF EXISTS `buckty_pages` ;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_perms`
--
DROP TABLE IF EXISTS `buckty_perms` ;
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
DROP TABLE IF EXISTS `buckty_perm_to_group` ;
CREATE TABLE `buckty_perm_to_group` (
  `perm_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`perm_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_perm_to_user`
--
DROP TABLE IF EXISTS `buckty_perm_to_user`  ;
CREATE TABLE `buckty_perm_to_user` (
  `perm_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`perm_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_pms`
--
DROP TABLE IF EXISTS `buckty_pms`;
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
DROP TABLE IF EXISTS `buckty_relations`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_settings`
--
DROP TABLE IF EXISTS `buckty_settings`;
CREATE TABLE `buckty_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35;

--
-- Dump dei dati per la tabella `buckty_settings`
--

INSERT INTO `buckty_settings` (`id`, `name`, `value`) VALUES
(1, 'site_url', '%SITE_URL%'),
(2, 'site_name', '%SITE_NAME%'),
(3, 'admin_email', '%SITE_ADMIN_EMAIL%'),
(6, 'social', 'Yes'),
(11, 'site_keywords', '%SITE_KEYWORDS%'),
(12, 'site_description', '%SITE_DESCRIPTION%'),
(13, 'upload_limit', '%SITE_UPLOAD_LIMIT%'),
(14, 'allowed_extensions', '%SITE_ALLOWED_EXTENSIONS%'),
(19, 'language_keys', 'a:175:{i:0;s:5:"Login";i:1;s:8:"Register";i:2;s:44:"We have sent an email with confirmation link";i:3;s:6:"Upload";i:4;s:20:"Something went wrong";i:5;s:19:"Invalid Folder name";i:6;s:8:"Settings";i:7;s:6:"Logout";i:8;s:12:"Edit Aavatar";i:9;s:11:"Admin Panel";i:10;s:13:"Create Folder";i:11;s:12:"Upload Files";i:12;s:9:"Clear All";i:13;s:6:"Reload";i:14;s:4:"Sort";i:15;s:4:"View";i:16;s:12:"Delete Files";i:17;s:10:"Move Files";i:18;s:4:"Size";i:19;s:6:"Shared";i:20;s:9:"All Files";i:21;s:6:"Recent";i:22;s:5:"Trash";i:23;s:28:"No files or folders in trash";i:24;s:7:"Starred";i:25;s:13:"Notifications";i:26;s:20:"Something is loading";i:27;s:10:"Select All";i:28;s:8:"Add star";i:29;s:11:"Remove Star";i:30;s:6:"Rename";i:31;s:6:"Delete";i:32;s:8:"Download";i:33;s:4:"Move";i:34;s:7:"Details";i:35;s:9:"Copy Link";i:36;s:10:"Dropbox it";i:37;s:12:"Google Drive";i:38;s:5:"Share";i:39;s:6:"Cancel";i:40;s:4:"Done";i:41;s:15:"Item was shared";i:42;s:14:"Folder created";i:43;s:8:"My Files";i:44;s:13:"Dropbox Files";i:45;s:13:"Email Address";i:46;s:23:"Connect to google drive";i:47;s:18:"Connect to dropbox";i:48;s:12:"New Passowrd";i:49;s:16:"Confirm Password";i:50;s:6:"Update";i:51;s:5:"Reset";i:52;s:16:"Recovery Options";i:53;s:12:"Active Since";i:54;s:10:"Disconnect";i:55;s:13:"Creation Date";i:56;s:4:"Date";i:57;s:17:"Size Bigger First";i:58;s:16:"Size Small First";i:59;s:7:"Preview";i:60;s:4:"More";i:61;s:4:"Copy";i:62;s:4:"Star";i:63;s:16:"Item was starred";i:64;s:19:"Item was un starred";i:65;s:24:"User account was created";i:66;s:5:"Clear";i:67;s:5:"Abort";i:68;s:16:"Search for files";i:69;s:11:"Edit_Avatar";i:70;s:8:"Username";i:71;s:12:"New Password";i:72;s:20:"User already updated";i:73;s:22:"Password Confirm Error";i:74;s:16:"User was updated";i:75;s:21:"Uploaded Successfully";i:76;s:15:"Reset link sent";i:77;s:16:"User email exist";i:78;s:20:"User email not exist";i:79;s:6:"Create";i:80;s:11:"Folder name";i:81;s:23:"Select the file folders";i:82;s:7:"dropbox";i:83;s:5:"drive";i:84;s:21:"file_shared_via_email";i:85;s:19:"email_share_subject";i:86;s:21:"you have unread notes";i:87;s:14:"Save to folder";i:88;s:10:"Api Access";i:89;s:9:"Name desc";i:90;s:8:"Name asc";i:91;s:13:"Change avatar";i:92;s:6:"Change";i:93;s:3:"New";i:94;s:5:"Close";i:95;s:12:"Select files";i:96;s:12:"Are you sure";i:97;s:3:"Yes";i:98;s:2:"No";i:99;s:10:"Add people";i:100;s:5:"Email";i:101;s:8:"Add pass";i:102;s:15:"Update password";i:103;s:17:"Username or email";i:104;s:8:"Can edit";i:105;s:8:"Can view";i:106;s:15:"Email addresses";i:107;s:12:"Your message";i:108;s:4:"Send";i:109;s:10:"Permission";i:110;s:19:"Copied to clipboard";i:111;s:23:"items are being trashed";i:112;s:19:"No users were found";i:113;s:29:"Already Shared With this user";i:114;s:14:"Invalid Folder";i:115;s:16:"Access Forbidden";i:116;s:9:"NOT FOUND";i:117;s:14:"File too large";i:118;s:16:"Not enough space";i:119;s:11:"Remember me";i:120;s:16:"Recovery options";i:121;s:8:"Password";i:122;s:13:"Email address";i:123;s:16:"Password confirm";i:124;s:14:"Reset password";i:125;s:14:"Folder Created";i:126;s:18:"items were removed";i:127;s:17:"item Was Restored";i:128;s:16:"Item was renamed";i:129;s:30:"items were removed permanently";i:130;s:27:"User was unlinked from item";i:131;s:23:"No unread notifications";i:132;s:20:"Notification removed";i:133;s:15:"Item was copied";i:134;s:29:"Email addresses cant be empty";i:135;s:13:"link was sent";i:136;s:24:"The password was updated";i:137;s:22:"Permission was changed";i:138;s:22:"invalid folder or file";i:139;s:12:"invalid_file";i:140;s:12:"invalid file";i:141;s:18:"File not available";i:142;s:22:"file was saved to root";i:143;s:31:"Files were saved to root folder";i:144;s:18:"access was removed";i:145;s:16:"Folder was empty";i:146;s:32:"folder or files were uploaded to";i:147;s:15:"was uploaded to";i:148;s:11:"Login again";i:149;s:11:"Invalid api";i:150;s:18:"Please Login Again";i:151;s:12:"was disabled";i:152;s:27:"Login again to google drive";i:153;s:20:"Registration subject";i:154;s:22:"Reset password subject";i:155;s:17:"Memory used label";i:156;s:14:"item was moved";i:157;s:28:"item was removed permanently";i:158;s:13:"Shared folder";i:159;s:11:"Shared file";i:160;s:26:"Your account was activated";i:161;s:25:"Account cant be activated";i:162;s:15:"welcome to site";i:163;s:28:"Account verification subject";i:164;s:23:"Username already exsits";i:165;s:20:"Email already exsits";i:166;s:21:"invalid email address";i:167;s:16:"invalid username";i:168;s:16:"invalid password";i:169;s:44:"Password and confirm password does not match";i:170;s:18:"Your email address";i:171;s:25:"Your password was changed";i:172;s:22:"Wrong details provided";i:173;s:12:"Accept terms";i:174;s:16:"items were moved";}'),
(20, 'max_file_size', '%SITE_MAX_FILE_SIZE%'),
(21, 'email_activation', '0'),
(26, 'register_active', '1'),
(27, 'site_home_tagline', '%SITE_HOME_TAGLINE%'),
(28, 'site_home_description', '%SITE_HOME_DESCRIPTION%'),
(29, 'smtp_host', '%SITE_SMTP_HOST%'),
(30, 'smtp_port', '%SITE_SMTP_PORT%'),
(31, 'smtp_user', '%SITE_SMTP_USER%'),
(32, 'smtp_password', '%SITE_SMTP_PASSWORD%'),
(33, 'disqus', '%SITE_DISQUS_SHORTNAME%'),
(34, 'blacklist_extensions', '%SITE_BLACKLIST_EXTENSIONS%');

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_users`
--
DROP TABLE IF EXISTS `buckty_users` ;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Struttura della tabella `buckty_user_to_group`
--
DROP TABLE IF EXISTS `buckty_user_to_group` ;
CREATE TABLE `buckty_user_to_group` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `buckty_user_variables`
--
DROP TABLE IF EXISTS `buckty_user_variables` ;
CREATE TABLE `buckty_user_variables` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `data_key` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
