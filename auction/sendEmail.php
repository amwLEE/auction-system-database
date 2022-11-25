<?php require("database.php")?>


<?php

    #name
    #email
    #recipient
    #mail body
    #subject
    #header


    #mail
    // $query = "SELECT * FROM Bid b, Users u WHERE buyerID = $userID AND b.buyerID = u.userID";

    // $result = mysqli_query($connection, $query);

    // $users = mysqli_fetch_assoc($result);

    $to = "vswq126@gmail.com";
    $subject = "Bidding outcome";

    $message = "test";
    $header = "From: group10db@gmail.com";

    $retval = mail($to,$subject,$message,$header);

    if($retval == true ) {
        echo "Message sent successfully...";
     }else {
        echo "Message could not be sent...";
     }

     function winner($userEmail,$header,$itemName){
        $subject = "Auction Result";
        $message = "You have won the auction for item _____itemname";

        $retval = mail($userEmail,$subject,$message,$header);

        if($retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }

        return $retval;
     }

     function lose($userEmail,$header,$itemName){
        $subject = "Auction Result";
        $message = "You were unsuccesful in the auction for item _____itemname";

        $retval = mail($userEmail,$subject,$message,$header);

        if($retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }

        return "lose";
     }

     function outbid($userEmail,$header,$itemName,$bidPrice){
        $subject = "Outbidded in auction";
        $message = "You have been outbidded for item ____ the new price is ____";

        $retval = mail($userEmail,$subject,$message,$header);

        if($retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }

         return $retval;

     }

     function bidUpdate($userEmail,$header,$itemName,$bidPrice){
        $subject = "Bid Update for item ______";
        $message = "The new price is... There are currently x bids.";

        $retval = mail($userEmail,$subject,$message,$header);

        if($retval == true ) {
            echo "Message sent successfully...";
         }else {
            echo "Message could not be sent...";
         }
         return $retval;
         
     }





?>