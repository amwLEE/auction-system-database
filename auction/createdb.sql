CREATE DATABASE Auction
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE Auction;

-- Create table for users

DROP TABLE IF EXISTS User;
CREATE TABLE Users
(
    userID INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(64) NOT NULL,
    lastName VARCHAR(64) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(20) NOT NULL,
    CHECK (email LIKE '%_@__%.__%')
)
ENGINE = InnoDB;

-- Create table for category

DROP TABLE IF EXISTS Category;
CREATE TABLE Category 
(
    categoryID INT AUTO_INCREMENT PRIMARY KEY,
    categoryName VARCHAR(64) NOT NULL
)
ENGINE = InnoDB;

-- Create table for auction
DROP TABLE IF EXISTS Auction;
CREATE TABLE Auction 
(
    itemID INT AUTO_INCREMENT PRIMARY KEY,
    itemName VARCHAR(64) NOT NULL,
    itemDescription VARCHAR(255) NOT NULL,
    sellerID INT,
    categoryID INT,
    startDateTime TIMESTAMP NOT NULL,
    endDateTime TIMESTAMP NOT NULL,
    startingPrice DECIMAL(12,2) NOT NULL,
    reservePrice DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (sellerID) REFERENCES Users(userID),
    FOREIGN KEY (categoryID) REFERENCES category(categoryID)
)
ENGINE = InnoDB;

-- Create table for bid
DROP TABLE IF EXISTS Bid;
CREATE TABLE Bid 
(
    bidID INT AUTO_INCREMENT PRIMARY KEY,
    itemID INT,
    buyerID INT,
    timestamp TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    bidPrice DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (itemID) REFERENCES Auction(itemID),
    FOREIGN KEY (buyerID) REFERENCES Users(userID)
)
ENGINE = InnoDB;