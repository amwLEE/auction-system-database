<?php
    include 'database.php';
   
    if (isset($_POST['submitBidForm'])){
        // Add data to Bid table in AuctionDB database
        $bidPrice = mysqli_real_escape_string($connection, $_POST['bidPrice']);
        // $bidTimeStamp = date("Y-m-d H:i:s", time());

        // Check that the required fields are not empty
        if ($bidPrice != ""){
            mysqli_query($connection,"INSERT INTO bid (bidPrice, bidTimeStamp)
            VALUES ($bidPrice,NOW())");

            // To update validation variable to a combination of itemID and buyerID and bidTimeStamp and bidPrice 
            $query = mysqli_query($connection, "SELECT * FROM bid WHERE bidTimeStamp=NOW()");
            if (mysqli_num_rows($query) == 1){

                $success = true;
            }
            else
                $error_msg = 'An error occurred and your bid was not submitted.';
        }
        else{
            $error_msg = "Please fill in all the required fields";
        }    
    }

?>