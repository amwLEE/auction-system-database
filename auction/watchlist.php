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
            if (mysqli_num_rows($bidQueryresult) > 0){
              $current_price = mysqli_fetch_row($bidQueryresult)[4];
            }else{
              $current_price = $starting_price;
            }
            

            print_listing_li($itemID, $title, $description, intval($current_price), $num_bids, $end_time, $category_name, "In progress");

        }


        // if a user made bid then display your bid vs current bid.
        // if a user gets outbid - notify them
        // if auction has ended and you 
        mysqli_close($connection);
    }
      
        

?>


    <?php include_once("footer.php")?>