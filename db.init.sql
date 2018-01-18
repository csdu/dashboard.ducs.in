create database if not exists sankalan;
use sankalan;

create table user (
    ts int(10) not null,
    id char(21) not null primary key,
    name varchar(40) not null,
    email varchar(32) not null,
    -- picture varchar(92),
    org varchar(60),
    hash char(32) not null unique
);
