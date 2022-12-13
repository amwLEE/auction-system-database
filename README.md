# COMP0178 Database Fundamentals Project
## Group 10
Dylan Conceicao
Amanda Lee
Melody Leom
Valerie Song

## Overview
This is the source code for our group's auction system built using the WAMP/MAMP stack. The design of the database is done in a way that ensures Third Normal Form (3NF). More details about our auction system and design can be found in our design report ("COMP0178_group10_report).

## Capabilities
Our auction system has the following capabilities:
|Component|Description|
|---------|-----------|
|#1|Users can register with the system and create accounts. Users have roles of seller or buyer with different privileges.|
|#2|Sellers can create auctions for particular items, setting suitable conditions and features of the items including the item description, categorisation, starting price, reserve price and end date.|
|#3|Buyers can search the system for particular kinds of item being auctioned and can browse and visually re-arrange listings of items within categories.|
|#4|Buyers can bid for items and see the bids other users make as they are received. The system will manage the auction until the set end time and award the item to the highest bidder. The system should confirm to both the winner and seller of an auction its outcome.|
|#5|Buyers can watch auctions on items and receive emailed updates on bids on those items including notifications when they are outbid.|
|#6|Buyers can receive recommendations for items to bid on based on collaborative filtering (i.e., ‘you might want to bid on the sorts of things other people, who have also bid on the sorts of things you have previously bid on, are currently bidding on).|

## Demo video URL
A video demonstration of our auction system's capabilities can be found in the following link: https://youtu.be/Lc-_V5TVD50

## How to run
1. Open phpmyadmin database and import createdb.sql
2. Import illustrative_data.sql
3. Set up server using WAMP/MAMP/LAMP and interact with the auction website through localhost. User accounts are available in illustrative_data.sql

