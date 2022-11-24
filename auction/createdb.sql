DROP DATABASE IF EXISTS AuctionDB;

CREATE DATABASE AuctionDB
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE AuctionDB;

-- Create admin account for database
DROP USER IF EXISTS 'adbadmin'@'localhost';
FLUSH PRIVILEGES;

CREATE USER 'adbadmin'@'localhost'
	IDENTIFIED BY 'Group10'; -- This is the password

GRANT ALL PRIVILEGES
    ON AuctionDB.*
    TO 'adbadmin'@'localhost';


-- Create table for users
DROP TABLE IF EXISTS Users;

CREATE TABLE Users
(
    userID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(64) NOT NULL,
    lastName VARCHAR(64) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    userPassword VARCHAR(100) NOT NULL,
    account_type BOOLEAN NOT NULL,
    CHECK (email LIKE '%_@__%.__%')
)
ENGINE = InnoDB;


-- Create table for category
DROP TABLE IF EXISTS Category;

CREATE TABLE Category 
(
    categoryID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    categoryName VARCHAR(64) NOT NULL
)
ENGINE = InnoDB;


-- Create table for auction
DROP TABLE IF EXISTS Auction;

CREATE TABLE Auction 
(
    itemID INT NOT NULL  AUTO_INCREMENT PRIMARY KEY,
    itemName VARCHAR(64) NOT NULL,
    itemDescription VARCHAR(255) NOT NULL,
    sellerID INT NOT NULL ,
    categoryID INT NOT NULL,
    startDateTime TIMESTAMP NOT NULL,
    endDateTime TIMESTAMP NOT NULL,
    startingPrice DECIMAL(12,2) NOT NULL,
    reservePrice DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (sellerID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (categoryID) REFERENCES Category(categoryID) ON DELETE CASCADE
)
ENGINE = InnoDB;


-- Create table for bid
DROP TABLE IF EXISTS Bid;

CREATE TABLE Bid 
(
<<<<<<< HEAD
    bidID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    itemID INT NOT NULL ,
    buyerID INT NOT NULL ,
    bidTimeStamp TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
=======
    bidID INT AUTO_INCREMENT PRIMARY KEY,
    itemID INT NOT NULL,
    buyerID INT NOT NULL,
    bidTimeStamp TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
>>>>>>> 38f81e9b615fd4464caa627c6ff221fe8a45f3de
    bidPrice DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (itemID) REFERENCES Auction(itemID),
    FOREIGN KEY (buyerID) REFERENCES Users(userID)
)
ENGINE = InnoDB;