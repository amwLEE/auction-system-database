DROP DATABASE Auction;

CREATE DATABASE Auction
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

GRANT SELECT, UPDATE, INSERT, DELETE
    ON Auction.*
    TO 'root'@'localhost'
    IDENTIFIED BY '';

USE Auction;

CREATE TABLE Users
(
    userID INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(64) NOT NULL,
    lastName VARCHAR(64) NOT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(20) NOT NULL,
    CHECK (email LIKE '%_@__%.__%')
)
ENGINE = InnoDB;

INSERT INTO Users (firstName, lastName, email, username, password)
    VALUES ('Amanda', 'Lee', 'amanda.lee.22@ucl.ac.uk', 'amwLEE', '123');