CREATE TABLE `%SQL_PREFIX%projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `date` date NOT NULL,
  `url_demo` varchar(128) DEFAULT NULL,
  `url_project` varchar(128) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
