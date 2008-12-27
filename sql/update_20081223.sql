CREATE TABLE `starshine_test`.`bataille` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_royaume` TINYINT UNSIGNED NOT NULL ,
`x` MEDIUMINT UNSIGNED NOT NULL ,
`y` MEDIUMINT UNSIGNED NOT NULL ,
`nom` VARCHAR( 100 ) NOT NULL ,
`description` TEXT NOT NULL ,
`etat` TINYINT UNSIGNED NOT NULL ,
`debut` INT UNSIGNED NOT NULL ,
`fin` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE `starshine_test`.`bataille_groupe` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_bataille` INT UNSIGNED NOT NULL ,
`id_groupe` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE `starshine_test`.`bataille_groupe_repere` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_repere` INT UNSIGNED NOT NULL ,
`id_groupe` INT UNSIGNED NOT NULL ,
`accepter` TINYINT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE `starshine_test`.`bataille_repere` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_bataille` INT UNSIGNED NOT NULL ,
`id_type` TINYINT UNSIGNED NOT NULL ,
`x` MEDIUMINT UNSIGNED NOT NULL ,
`y` MEDIUMINT UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
);

CREATE TABLE `starshine_test`.`bataille_repere_type` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`nom` VARCHAR( 100 ) NOT NULL ,
`description` TEXT NOT NULL ,
`ajout_groupe` TINYINT UNSIGNED NOT NULL ,
`image` VARCHAR( 200 ) NOT NULL ,
PRIMARY KEY ( `id` )
);