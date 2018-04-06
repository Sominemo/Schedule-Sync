CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(255) CHARACTER SET utf8 NOT NULL,
  `surname` char(255) CHARACTER SET utf8 NOT NULL,
  `login` char(255) CHARACTER SET utf8 NOT NULL,
  `password` text CHARACTER SET utf8 NOT NULL,
  `visit` char(255) CHARACTER SET utf8 NOT NULL,
  `avatar` text CHARACTER SET utf8 NOT NULL,
  `regtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;