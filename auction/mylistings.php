<?php 
  include_once("header.php");
  require("database.php");
  require("utilities.php");

  $_SESSION['pageType'] = 'listings';
?>



<?php
  // Check user's credentials from session
  // Extract user ID from seller accounts and deny access to all other account types
  if (isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 'seller') {
      $sellerID = $_SESSION['userID'];
    } else {
      exit("<h5 class='access_denied'>Access denied: You do not have permission to view this page.</h5>");
    }
  } else {
    exit("<h5 class='access_denied'>Access denied: You do not have permission to view this page.</h5>");
  }
?>

<div class="container">
<h2 class="my-3">My listings</h2>

<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.

  // Perform a query to pull up their auctions.
  $query = "SELECT * 
            FROM Auction a INNER JOIN Category c ON c.categoryID = a.categoryID 
            WHERE sellerID = $sellerID 
            ORDER BY itemID DESC";
  $result = mysqli_query($connection, $query);
  
  // Loop through results and print them out as list items.
  print_all_listings($connection, $result);

  // Close the connection as soon as it's no longer needed
  mysqli_close($connection);
?>

<?php include_once("footer.php")?>