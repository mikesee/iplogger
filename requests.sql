--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `requestid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `country_code` text NOT NULL,
  `country_name` text NOT NULL,
  `region_code` text NOT NULL,
  `region_name` text NOT NULL,
  `city` text NOT NULL,
  `zip_code` text NOT NULL,
  `time_zone` text NOT NULL,
  `latitude` varchar(10) NOT NULL,
  `longitude` varchar(10) NOT NULL,
  `metro_code` varchar(10) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`requestid`),
  UNIQUE KEY `ip_3` (`ip`),
  KEY `date_added` (`date_added`),
  FULLTEXT KEY `ip` (`ip`,`country_code`,`country_name`),
  FULLTEXT KEY `country_name` (`country_name`),
  FULLTEXT KEY `ip_2` (`ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2375 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
