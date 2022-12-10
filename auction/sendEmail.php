<?php require("database.php")?>

<?php
   $senderName = "Group 10"; //sender's name
   $senderEmail = "group10.ucl@gmail.com"; //sender's email address
   $header = "From: ".$senderName." <".$senderEmail. ">\r\n"; //optional header fields

   function emailSeller($itemName, $recipientName, $recipientEmail, $sold, $winner){
      global $senderName, $header;
      $subject = "Auction Outcome for ".$itemName;

      if($sold == 1) {
         $remarks = "This item was sold to User#".$winner;
      } elseif($sold == 0) {
         $remarks = "This item was not sold because the reserve price was not met";
      } else {
         $remarks = "This item was not sold because there were no bids";
      }
      $message = "Hi ".$recipientName.",\r\n\r\n".
                  "Your auction for ".$itemName." has concluded. ".$remarks.".\r\n\r\n".
                  $senderName;
      
      mail($recipientEmail, $subject, $message, $header);
      return;
   }

   function emailBuyer($itemName, $recipientName, $recipientEmail, $won) {
      global $senderName, $header;
      $subject = "Auction Outcome for ".$itemName;
      
      if($won == true) { //Won
         $message = "Hi ".$recipientName.",\r\n\r\n".
                     "You have won the auction for ".$itemName.".\r\n\r\n".
                     $senderName;
      } else { //Loss
         $message = "Hi ".$recipientName.",\r\n\r\n".
                     "You were unsuccessful in the auction for ".$itemName.".\r\n\r\n".
                     $senderName;
      }
      
      mail($recipientEmail, $subject, $message, $header);
      return;
   }

   function emailWatcher($itemID, $newbidder, $bidPrice){
      global $connection;
      $query = "SELECT a.itemName, b.bidPrice, COUNT(b.itemID) AS numBids FROM Auction a, Bid b WHERE a.itemID=$itemID AND b.itemID=$itemID";
      $result = mysqli_query($connection, $query);
      $row = mysqli_fetch_assoc($result);
      $itemName =$row["itemName"];
      $numBids = $row["numBids"];

      global $senderName, $header;
      $subject = "Bid Update for ".$itemName;
      
      //Outbid
      $query = "SELECT u.userID, u.firstName, u.email, b.bidPrice FROM Users u, Bid b WHERE u.userID=b.buyerID AND u.userID<>$newbidder ORDER BY b.bidPrice DESC LIMIT 1,1";
      $result = mysqli_query($connection, $query);
      if ($row = mysqli_fetch_assoc($result)) {
         $recipientID = $row["userID"];
         $recipientName = $row["firstName"];
         $recipientEmail = $row["email"];
         $outbid = "Hi ".$recipientName.",\r\n\r\n".
                  "You are receiving this email because you recently placed a bid on ".$itemName." at the ".$senderName." auction site.\r\n".
                  "You have been outbid by another user at £".number_format((float)$bidPrice,2,'.','').". This item now has ".$numBids." bids in total.\r\n\r\n".
                  $senderName;
         mail($recipientEmail, $subject, $outbid, $header);
      }
      
      //Update
      $query = "SELECT u.firstName, u.email FROM Users u WHERE u.userID IN (SELECT w.userID FROM Watch w WHERE w.itemID=$itemID) AND u.userID<>$recipientID";
      $result = mysqli_query($connection, $query);
      while ($row = mysqli_fetch_assoc($result)) {
         $recipientName = $row["firstName"];
         $recipientEmail = $row["email"];
         $update = "Hi ".$recipientName.",\r\n\r\n".
                  "You are receiving this email because you added ".$itemName." to your watchlist at the ".$senderName." auction site.\r\n".
                  "There is a new bid for this item at £".number_format((float)$bidPrice,2,'.','').". This item now has ".$numBids." bids in total.\r\n\r\n".
                  $senderName;
         mail($recipientEmail, $subject, $update, $header);
      }

      mysqli_close($connection);
      return;
   }
?>