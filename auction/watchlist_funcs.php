<?php include_once("header.php")?>
<?php require("database.php")?>

<?php

if (!isset($_POST['functionname']) || !isset($_POST['itemID']) || !isset($_POST['userID'])) {
  return;
}

// Extract arguments from the POST variables:
 $itemID = $_POST['itemID'];
 $userID = $_POST['userID'];


if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $query = "INSERT INTO Watch(userID, itemID) VALUES('$userID', '$itemID')  ";

  //$query = "UPDATE Auction SET watchList = 1 WHERE itemid = $item_id";
  
  $insert = mysqli_query($connection,$query);
  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  $query = "DELETE FROM Watch WHERE userID = '{$userID}' and itemID = '{$itemID}'";
  $delete = mysqli_query($connection,$query);

  
  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo json_encode($res);

// Close the connection as soon as it's no longer needed
mysqli_close($connection);



?>