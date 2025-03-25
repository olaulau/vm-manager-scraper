DROP TABLE IF EXISTS `coach`;

CREATE TABLE `coach` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_login` varchar(128) DEFAULT NULL,
  `type` varchar(128) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `phy` int(11) NOT NULL,
  `jun` int(11) DEFAULT NULL,
  `tch` int(11) NOT NULL,
  `ada` int(11) DEFAULT NULL,
  `psy` int(11) NOT NULL,
  `dis` int(11) NOT NULL,
  `mot` int(11) DEFAULT NULL,
  `age` int(11) NOT NULL,
  `salaire` int(11) NOT NULL,
  `prix` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coach___type` (`type`),
  KEY `coach___user_login` (`user_login`)
);
