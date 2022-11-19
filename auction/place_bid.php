<?php
// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.
include 'database.php';
   
    if (isset($_POST['submitBidForm'])){
        // Add data to Bid table in AuctionDB database
        $itemID = $_GET['item_id'];
        $bidPrice = mysqli_real_escape_string($connection, $_POST['bidPrice']);

        // Check that the required fields are not empty
        if ($bidPrice == "") {
            $error_msg = "Please fill in all the required fields";
        } elseif (!(isset($_SESSION['logged_in'])==1)) {
            $error_msg = 'Please register/login a buyer account to place bid';
        } else {
            $buyerID = $_SESSION['userID'];
            mysqli_query($connection,"INSERT INTO bid (itemID, buyerID, bidTimeStamp, bidPrice)
            VALUES ($itemID, $buyerID, NOW(), $bidPrice)");

            // To update validation variable to a combination of itemID and buyerID and bidTimeStamp and bidPrice 
            $query = mysqli_query($connection, "SELECT * FROM bid WHERE buyerID=$buyerID AND bidTimeStamp=NOW()");
            if (mysqli_num_rows($query) == 1 ){
                $success = true;
            } else {
                $error_msg = 'An error occurred and your bid was not submitted';
            }
        }    
    }
?>