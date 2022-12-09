<?php include_once("header.php")?>
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
<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.

  // Perform a query to pull up auctions they might be interested in.
  $query = "SELECT itemID, COUNT(itemID) 
            FROM Bid 
            WHERE buyerID=$buyerID 
            GROUP BY itemID,bidTimeStamp 
            ORDER BY bidTimeStamp DESC";
  $result = mysqli_query($connection, $query);
  if (mysqli_num_rows($result) > 0){
    $myitems = implode_colName($result, 'itemID');

    $query = "SELECT buyerID, COUNT(buyerID)
              FROM Bid
              WHERE (buyerID<>$buyerID) AND (itemID IN ($myitems))
              GROUP BY buyerID
              ORDER BY 2 DESC";
    $result = mysqli_query($connection, $query);
    $myneighbours = implode_colName($result, 'buyerID');

    $query = "SELECT b.itemID, COUNT(b.itemID)
              FROM Bid b INNER JOIN Auction a ON a.itemID = b.itemID 
              WHERE (b.buyerID IN ($myneighbours)) 
                AND (b.itemID NOT IN ($myitems)) 
                AND a.endDateTime > NOW()
              GROUP BY b.itemID
              ORDER BY 2 DESC
              LIMIT 0,5";
    $result = mysqli_query($connection, $query);
    $recommendation = implode_colName($result, 'itemID');

    if ($recommendation) {
      echo "<br><h5>You might want to bid on the sorts of things other people, who have also bid on the sorts of things you have previously bid on, are currently bidding on.</h5>";
      $query = "SELECT * 
                FROM Auction a INNER JOIN Category c ON a.categoryID = c.categoryID
                WHERE itemID IN ($recommendation)  
                ORDER BY FIELD(itemID,$recommendation)";
      $result = mysqli_query($connection, $query);
      
      // Loop through results and print them out as list items.
      print_all_listings($connection, $result);
    }
  }
  
  echo "<br><h5>Check out these trending auction listings.</h5>";
  $query = "SELECT b.itemID, COUNT(b.itemID), MAX(b.bidTimeStamp)
            FROM Auction a INNER JOIN Bid b ON a.itemID=b.itemID 
            WHERE a.endDateTime>NOW()
            GROUP BY b.itemID
            ORDER BY 2 DESC, 3 DESC, a.endDateTime ASC
            LIMIT 0,5";      
  $result = mysqli_query($connection, $query);
  $recommendation = implode_colName($result, 'itemID');

  $query = "SELECT * FROM Auction a, Category c WHERE itemID IN ($recommendation) AND a.categoryID = c.categoryID ORDER BY FIELD(itemID,$recommendation)";
  $result = mysqli_query($connection, $query);
  
  // Loop through results and print them out as list items.
  print_all_listings($connection, $result);

  // Close the connection as soon as it's no longer needed
  mysqli_close($connection);
?>

<?php include_once("footer.php")?>