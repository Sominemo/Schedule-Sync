CREATE TABLE IF NOT EXISTS `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` char(225) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `ua` varchar(1000) NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;