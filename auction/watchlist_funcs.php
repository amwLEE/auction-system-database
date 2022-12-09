<?php require("database.php")?>
<?php

if (!isset($_POST['functionname']) || !isset($_POST['itemID']) || !isset($_POST['userID'])) {
  return;
}

// Extract arguments from the POST variables:
 $itemID = $_POST['itemID'];
 $userID = $_POST['userID'];
 $data = "";

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $addWatchQuery = "INSERT INTO Watch(userID, itemID) VALUES('$userID', '$itemID')  ";
  
  $addWatch = mysqli_query($connection,$addWatchQuery);
  
   $data =  "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  $deleteWatchQuery = "DELETE FROM Watch WHERE userID = '{$userID}' and itemID = '{$itemID}'";
  $deleteWatch = mysqli_query($connection,$deleteWatchQuery);
  
  $data =  "success";

}

echo $data;

// Close the connection as soon as it's no longer needed
mysqli_close($connection);


?>