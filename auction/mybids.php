<?php include_once("header.php")?>
<?php require("database.php");?>
<?php require("utilities.php")?>

<?php
  // Check user's credentials from session
  // Extract user ID from buyer accounts and deny access to all other account types
  if (isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 'buyer') {
      $buyerID = $_SESSION['userID'];
    } else {
      exit("<h5 class='access_denied'>Access denied: You do not have permission to view this page.</h5>");
    }
  } else {
    exit("<h5 class='access_denied'>Access denied: You do not have permission to view this page.</h5>");
  }
?>

<div class="container">
<h2 class="my-3">My bids</h2>

<?php
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.

  // Perform a query to pull up the auctions they've bidded on.
  $query = "SELECT a.itemID, a.itemName, a.itemDescription,a.startDateTime, a.endDateTime,a.categoryID, a.startingPrice, a.reservePrice,a.sellerID,b.buyerID,c.categoryName,c.categoryID, MAX(b.bidTimeStamp), MAX(b.bidPrice)
            FROM Auction a 
            INNER JOIN Bid b 
            ON a.itemID = b.itemID and b.buyerID = $buyerID 
            INNER JOIN Category c
            ON c.categoryID = a.categoryID
            GROUP BY a.itemID, a.itemName, a.itemDescription,a.startDateTime, a.endDateTime,a.categoryID, a.startingPrice, a.reservePrice,a.sellerID,b.buyerID,c.categoryName,c.categoryID
            ORDER BY MAX(b.bidTimeStamp) DESC";
  $result = mysqli_query($connection, $query);

  // Loop through results and print them out as list items.
  print_all_listings($connection, $result);

  // Close the connection as soon as it's no longer needed
  mysqli_close($connection);
?>

<?php include_once("footer.php")?>