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
<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.

  // Perform a query to pull up auctions they might be interested in.
  $query = "SELECT itemID, COUNT(itemID) FROM Bid WHERE buyerID=$buyerID GROUP BY itemID ORDER BY bidTimeStamp DESC";
  $result = mysqli_query($connection, $query);
  $arr = array();
  if (mysqli_num_rows($result) != 0){
    $myitems = array();
    while ($x = mysqli_fetch_assoc($result)){
      $myitems[] = $x['itemID'];
    }
    $myitems = implode(',', $myitems);

    $query = "SELECT buyerID, COUNT(buyerID)
              FROM Bid
              WHERE (buyerID<>$buyerID) AND (itemID IN ($myitems))
              GROUP BY buyerID
              ORDER BY COUNT(buyerID) DESC";
    $result = mysqli_query($connection, $query);
    $myneighbours = array();
    while ($y = mysqli_fetch_assoc($result)){
      $myneighbours[] = $y['buyerID'];
    }
    $myneighbours = implode(',', $myneighbours);

    $query = "SELECT itemID, COUNT(itemID)
              FROM Bid b
              WHERE (buyerID IN ($myneighbours)) AND (itemID NOT IN ($myitems)) AND (SELECT endDateTime FROM Auction a WHERE a.itemID=b.itemID)>NOW()
              GROUP BY itemID
              ORDER BY COUNT(itemID) DESC
              LIMIT 0,5";
    $result = mysqli_query($connection, $query);
    while ($z = mysqli_fetch_assoc($result)){
      $arr[] = $z['itemID'];
    }
    $recommendation = implode(',', $arr);
    if ($arr) {
      echo "<br><h5>You might want to bid on the sorts of things other people, who have also bid on the sorts of things you have previously bid on, are currently bidding on.</h5>";
      $query = "SELECT * FROM Auction a, Category c WHERE itemID IN ($recommendation) AND a.categoryID = c.categoryID ORDER BY FIELD(itemID,$recommendation)";
      $result = mysqli_query($connection, $query);
      
      // Loop through results and print them out as list items.
      while ($listing = mysqli_fetch_assoc($result)){
        $item_id = intval($listing['itemID']);
        $title = $listing['itemName'];
        $desc = $listing['itemDescription'];
        $category = $listing['categoryName'];
        $end_time = new DateTime($listing['endDateTime']);
        
        $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id");
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
            $status = 'Sold';
          } else {
            $status = 'Not sold';
          }
        } else {
          $status = 'In progress';
        }

        print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $status);
      }
    }
  }
  
  echo "<br><h5>Check out these trending auction listings.</h5>";
  $query = "SELECT b.itemID, COUNT(b.itemID), MAX(b.bidTimeStamp)
            FROM Auction a, Bid b
            WHERE a.itemID=b.itemID AND a.endDateTime>NOW()
            GROUP BY b.itemID
            ORDER BY COUNT(b.itemID) DESC, MAX(b.bidTimeStamp) DESC, a.endDateTime ASC
            LIMIT 0,5";
  $result = mysqli_query($connection, $query);
  $arr = array();
  while ($listing = mysqli_fetch_assoc($result)){
    $arr[] = $listing['itemID'];
  }
  $recommendation = implode(',', $arr);

  $query = "SELECT * FROM Auction a, Category c WHERE itemID IN ($recommendation) AND a.categoryID = c.categoryID ORDER BY FIELD(itemID,$recommendation)";
  $result = mysqli_query($connection, $query);
  
  // Loop through results and print them out as list items.
  while ($listing = mysqli_fetch_assoc($result)){
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $category = $listing['categoryName'];
    $end_time = new DateTime($listing['endDateTime']);
    
    $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id");
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
        $status = 'Sold';
      } else {
        $status = 'Not sold';
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