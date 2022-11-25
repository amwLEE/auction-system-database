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
  $query = "SELECT *, MAX(b.bidTimeStamp), MAX(b.bidPrice)
            FROM Auction a, Bid b, Category c
            WHERE b.buyerID=$buyerID AND a.itemID=b.itemID AND c.categoryID=a.categoryID
            GROUP BY b.itemID
            ORDER BY MAX(b.bidTimeStamp) DESC";
  $mylistings = mysqli_query($connection, $query);

  // Loop through results and print them out as list items.
  while ($listing = mysqli_fetch_assoc($mylistings)){
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $end_time = new DateTime($listing['endDateTime']);
    $category = $listing['categoryName'];
    $myprice = $listing['MAX(b.bidPrice)'];
    
    $query = "SELECT * FROM Bid WHERE itemID=$item_id ORDER BY bidID DESC";
    $mybids = mysqli_query($connection, $query);



    if (mysqli_num_rows($mybids) > 0){
      $num_bids = mysqli_num_rows($mybids);
      $price = mysqli_fetch_row($mybids)[4];
    } else{
      $num_bids = 0;
      $price = 0;
    }
    
    $now = new DateTime();
    if ($now > $end_time) {
      if ($myprice > $listing['reservePrice']) {
        $status = 'Won';
      } else {
        $status = 'Loss';
      }
    } else {
      $status = 'In progress';
    }

    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $status);
  }

  // Close the connection as soon as it's no longer needed
  mysqli_close($connection);
?>

<?php include_once("footer.php")?>