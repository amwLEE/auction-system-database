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

CREATE TABLE Category
(
    categoryID INT AUTO_INCREMENT PRIMARY KEY,
    categoryName VARCHAR(64) NOT NULL
)
ENGINE = InnoDB;

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