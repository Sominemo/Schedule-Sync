CREATE TABLE IF NOT EXISTS `api_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` varchar(50) NOT NULL DEFAULT '0',
  `clock` int(11) NOT NULL DEFAULT '0',
  `link` longtext NOT NULL,
  `params` longtext NOT NULL,
  `result` longtext NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;