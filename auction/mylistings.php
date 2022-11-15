<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php

  $query = "SELECT firstName, lastName FROM Users";
  $result = mysqli_query($connection, $query);

  echo "Current users registered on our database:<br>";
  while ($row = mysqli_fetch_assoc($result)){
    echo $row['firstName'] . " " . $row['lastName'], "; ";
  }

  mysqli_close($connection);
?>


<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up their auctions.
  
  // TODO: Loop through results and print them out as list items.
  
?>

<?php include_once("footer.php")?>