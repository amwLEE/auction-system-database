<?php include_once("header.php")?>
<?php require("database.php");?>
<?php require("utilities.php")?>
<?php include("watchlist_funcs.php")?>


<div class="container">

    <h2 class="my-3">Watchlist Items</h2>

    <?php
    $userID = $_SESSION['userID'];    
    $getUserQuery = "SELECT * FROM Watch where userID = '{$userID}'";

    $result = mysqli_query($connection, $getUserQuery);

    if (mysqli_num_rows($result) == 0 ){
        echo "No items in watchlist";
    }else{
        while($watchlist = mysqli_fetch_assoc($result)){

            $itemID = $watchlist['itemID'];

            // retrieving items on watchlist
            $query= "SELECT * FROM Auction a, Category c WHERE c.categoryID=a.categoryID AND itemID = '{$itemID}'";
            $watchresult = mysqli_query($connection, $query);
    
            $auctionWatch = mysqli_fetch_assoc($watchresult);


            $bidQuery = "SELECT * FROM Bid WHERE itemID=$itemID ORDER BY bidID DESC";
            $bidQueryresult = mysqli_query($connection, $bidQuery);
            
            $title = $auctionWatch['itemName'];
            $description = $auctionWatch['itemDescription'];
            $category_name = $auctionWatch['categoryName'];
            $end_time = new DateTime($auctionWatch['endDateTime']);
            $starting_price = $auctionWatch['startingPrice'];
            $reserve_price = $auctionWatch['reservePrice'];


            $num_bids = mysqli_num_rows($bidQueryresult);

            // Setting price of item
            if (mysqli_num_rows($bidQueryresult) > 0){
              $current_price = mysqli_fetch_row($bidQueryresult)[4];
            }else{
              $current_price = $starting_price;
            }

            // Check whether auction has ended, and display auction status accordingly
            $now = new DateTime();
            
            if ($now > $end_time) {
                if ($num_bids == 0 or ($current_price <$reserve_price)) {
                  $status = "Not sold";
                //   '<mark style="background: red">Not sold</mark>';
                } else {
                  $status = "Sold";
                //   '<mark style="background: green">Sold</mark>';
                }
              } else {
                $status = "In progress";
                // '<mark style="background: orange">In progress</mark>';
              }



            // Check whether user has made a bid on a particular item
            $userBid = "SELECT * FROM Bid WHERE buyerID = '{$userID}' and itemID = '{$itemID}' ORDER BY bidID DESC ";
            $userBidResult = mysqli_query($connection, $userBid);
            
            // Display users bid compared to current price, if user has made a bid
            if (mysqli_num_rows($userBidResult) > 0){
                $userBidDetails = mysqli_fetch_assoc($userBidResult);
                $userBidPrice = $userBidDetails['bidPrice'];


                if ($now > $end_time) {
                    $time_remaining = 'This auction has ended';
                  }
                  else {
                    // Get interval:
                    $time_to_end = date_diff($now, $end_time);
                    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
                  }

                echo(
                    '<li class="list-group-item d-flex justify-content-between">
                    <div class="p-2 mr-5">
                      <h5><a href="listing.php?item_id=' . $itemID . '">' . $title . '</a></h5>' .
                      $description . '<br/>' .
                      '<mark style="background: lightblue;">' . $category_name . '</mark>&nbsp;' . $status . '</mark>' .
                    '</div>

                    <div>
                        
                        <span style="font-size: 1.5em">Your Bid: £' . number_format($userBidPrice,2) . '</span><br/>' .
                    '</div>
                
                    <div class="text-center text-nowrap">
                      <span style="font-size: 1.5em">Current Bid: £' . number_format($current_price, 2) . '</span><br/>' .
                      $num_bids . ' bids'. '<br/>' .
                      $time_remaining .
                    '</div>
                  </li>'
                );

            }else{
                print_listing_li($itemID, $title, $description, $current_price, $num_bids, $end_time, $category_name, $status);

            }


        }

        mysqli_close($connection);
    }
      
        

?>


    <?php include_once("footer.php")?>