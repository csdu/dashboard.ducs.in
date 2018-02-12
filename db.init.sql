create database if not exists sankalan;
use sankalan;

CREATE TABLE `user` (
 `ts` int(10) NOT NULL,
 `id` char(21) NOT NULL,
 `name` varchar(40) NOT NULL,
 `email` varchar(32) NOT NULL,
 `mobile` char(10) NOT NULL,
 `org` varchar(80) DEFAULT NULL,
 `hash` char(32) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `hash` (`hash`)
);
