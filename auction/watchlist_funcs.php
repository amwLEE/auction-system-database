<?php include_once("header.php")?>
<?php require("database.php")?>

<?php

echo '<h1>  Watchlist </h1>';


if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = $_POST['arguments'];


if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  //$query = "UPDATE Bid SET bidPrice = $bidPrice WHERE itemid = $itemID   ";

  //$query = "UPDATE Auction SET watchList = 1 WHERE itemid = $item_id";

//  $update = mysqli_query($connection,$query);

  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  //$query = "UPDATE Auction SET watchList = 0 WHERE itemid = $item_id";
  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo json_encode($res);

?>