<?php include_once("header.php")?>
<?php require("database.php");?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.

  // TODO: Check user's credentials (cookie/session).
  if (!(($_SESSION['logged_in']) && ($_SESSION['account_type']==1))) {
    header("Location: index.php");
    exit;
  }
  $sellerID = $_SESSION['userID'];

  // TODO: Perform a query to pull up their auctions.
  $mylistings = mysqli_query($connection, "SELECT * FROM Auction WHERE sellerID=$sellerID ORDER BY itemID DESC");

  // TODO: Loop through results and print them out as list items.
  while ($listing = mysqli_fetch_assoc($mylistings)){
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $end_time = new DateTime($listing['endDateTime']);
    
    $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id ORDER BY bidID DESC");
    if (mysqli_num_rows($mybids) > 0){
      $num_bids = mysqli_num_rows($mybids);
      $price = mysqli_fetch_row($mybids)[4];
    } else{
      $num_bids = 0;
      $price = 0;
    }
    
    $now = new DateTime();
    if ($now > $end_time) {
      if ($price > $listing['reservePrice']) {
        echo "<mark>Sold</mark>";
      } else {
        echo "<mark>Not sold</mark>";
      }
    } else {
      echo "<mark>In progress</mark>";
    }

    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time);
  }
?>

<?php include_once("footer.php")?>