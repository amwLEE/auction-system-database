<?php include_once("header.php")?>
<?php require("database.php");?>
<?php require("utilities.php")?>

<div class="container">
<br>

<?php
if (!(isset($_SESSION['logged_in']) && ($_SESSION['account_type']==0))) {
  exit("<span style='color:red;'>Access denied: You do not have permission to view this page.</span>");
}
?>

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  // TODO: Check user's credentials (cookie/session).
  $buyerID = $_SESSION['userID'];

  // TODO: Perform a query to pull up auctions they might be interested in.
  $query = "SELECT * FROM Bid WHERE buyerID=$buyerID";
  $result = mysqli_query($connection, $query);
  $arr = array();
  if (mysqli_num_rows($result) == 0){
    echo "You have not bid on any items so far, check out these trending auction listings.";
    $query = "SELECT b.itemID, COUNT(b.itemID)
              FROM Auction a, Bid b
              WHERE a.itemID=b.itemID AND a.endDateTime>NOW()
              GROUP BY b.itemID
              ORDER BY COUNT(b.itemID) DESC, b.bidTimeStamp DESC, a.endDateTime ASC
              LIMIT 0,5";
    $result = mysqli_query($connection, $query);
    while ($listing = mysqli_fetch_assoc($result)){
      $arr[] = $listing['itemID'];
    }
  } else {
    // This section is hard coded for now
    echo "Based on your bid history, you may be interested in the following auction listings.";
    $query = "SELECT * FROM Auction WHERE endDateTime>NOW() LIMIT 0,5";
    $result = mysqli_query($connection, $query);
    while ($listing = mysqli_fetch_assoc($result)){
      $arr[] = $listing['itemID'];
    }
  }
  $recommendation = implode(',', $arr);

  $query = "SELECT * FROM Auction WHERE itemID IN ($recommendation) ORDER BY FIELD(itemID,$recommendation)";
  $result = mysqli_query($connection, $query);
  
  // TODO: Loop through results and print them out as list items.
  while ($listing = mysqli_fetch_assoc($result)){
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $end_time = new DateTime($listing['endDateTime']);
    
    $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id");
    if (mysqli_num_rows($mybids) > 0){
      $num_bids = mysqli_num_rows($mybids);
      $price = mysqli_fetch_row($mybids)[4];
    } else{
      $num_bids = 0;
      $price = 0;
    }
    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time);
  }
?>

<?php include_once("footer.php")?>