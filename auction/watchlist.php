<?php 
  include_once("header.php");
  require("database.php");
  require("utilities.php");
  include("watchlist_funcs.php"); 

  $userID = check_user_type('buyer');
  $pageType = 'listings';
?>


<div class="container">
  <h2 class="my-3">Watchlist Items</h2>

  <?php
    // Get all items on user's watchlist
    $query = "SELECT * FROM Watch where userID = $userID";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 0 ){
      echo "No items in watchlist";
    } else { 
      $query = "SELECT * FROM 
                Watch w INNER JOIN Auction a ON w.itemID = a.itemID 
                INNER JOIN Category c ON a.categoryID = c.categoryID 
                WHERE w.userID = $userID 
                ORDER BY w.itemID DESC";
      $result = mysqli_query($connection, $query);
      print_all_listings($connection, $result, $userID, $pageType);
    }
    
    // Close the connection as soon as it's no longer needed
    mysqli_close($connection);    

        
?>


    <?php include_once("footer.php")?>