DROP TABLE IF EXISTS `coach`;

CREATE TABLE `coach` (
	`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`user_login` varchar(128) NULL DEFAULT NULL,
	`type` varchar(128) NULL DEFAULT NULL,
	`name` varchar(128) NOT NULL,
	`phy` integer(11) NOT NULL,
	`jun` integer(11) NULL DEFAULT NULL,
	`tch` integer(11) NOT NULL,
	`ada` integer(11) NULL DEFAULT NULL,
	`psy` integer(11) NOT NULL,
	`dis` integer(11) NOT NULL,
	`mot` integer(11) NULL DEFAULT NULL, 
	`age` integer(11) NOT NULL,
	`salaire` integer(11) NOT NULL,
	`prix` integer(11) NULL DEFAULT NULL
);