create database if not exists sankalan;
use sankalan;

CREATE TABLE `user` (
 `id` INT AUTO_INCREMENT NOT NULL, -- uid
 `ts` INT(10) NOT NULL, -- timestamp
 `gid` CHAR(21) NOT NULL, -- google id
 `name` VARCHAR(40) NOT NULL,
 `email` VARCHAR(40) NOT NULL,
 `mobile` CHAR(10) NOT NULL,
 `org` VARCHAR(80) DEFAULT NULL,
 `accomodation` char(1) DEFAULT '0',
 `hash` CHAR(32) NOT NULL, -- user's public identifier
 PRIMARY KEY (`id`),
 UNIQUE KEY `gid` (`gid`),
 UNIQUE KEY `hash` (`hash`)
);

CREATE TABLE `admin` (
 `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
 `uname` VARCHAR(30) NOT NULL,
 `pass` VARCHAR(64) NOT NULL,
 `privilage` INT NOT NULL DEFAULT 0
);
