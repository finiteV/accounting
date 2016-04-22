CREATE TABLE IF NOT EXISTS `activation` (
  `message` char(100) NOT NULL,
  `info` char(160) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`message`),
  KEY `info` (`info`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `books` (
  `userid` char(50) NOT NULL,
  `book` char(100) NOT NULL,
  `category` char(50) NOT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`book`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `chapters` (
  `chapter` text NOT NULL,
  `chaptername` char(80) NOT NULL,
  `section` char(80) NOT NULL,
  `book` char(100) NOT NULL,
  `chapterid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`chapterid`),
  KEY `section` (`section`),
  KEY `chaptername` (`chaptername`,`section`),
  KEY `book` (`book`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `email` (
  `userid` char(50) NOT NULL,
  `email_addr` char(100) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invitation` (
  `invcode` char(60) NOT NULL,
  `userid` char(50) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`invcode`),
  KEY `userid` (`userid`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ip` (
  `userid` char(50) NOT NULL,
  `ipaddr` text NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `questions` (
  `userid` char(50) NOT NULL,
  `question` char(100) NOT NULL,
  `answer` char(100) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `reasons` (
  `reasonid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` char(20) NOT NULL,
  `reason` text,
  `date` date NOT NULL,
  PRIMARY KEY (`reasonid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `record` (
  `recordid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` char(20) NOT NULL,
  `income` double(9,2) NOT NULL,
  `outcome` double(9,2) NOT NULL,
  `date` date NOT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`recordid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sections` (
  `section` char(80) NOT NULL,
  `book` char(100) NOT NULL,
  `sectionid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sectionid`),
  KEY `book` (`book`),
  KEY `section` (`section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `userid` char(50) NOT NULL,
  `pwd` char(60) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `visitors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(50) COLLATE utf8_unicode_ci NOT NULL,
  `port` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `lvisitt` bigint(20) NOT NULL,
  `freq` int(10) unsigned NOT NULL,
  `info` char(200) COLLATE utf8_unicode_ci NOT NULL,
  `url` char(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
