<?php include_once("header.php")?>
<?php require("database.php");?>
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
            
            $title = $auctionWatch['itemName'];
            $description = $auctionWatch['itemDescription'];
            $category_name = $auctionWatch['categoryName'];
            $end_time = new DateTime($auctionWatch['endDateTime']);
            $starting_price = $auctionWatch['startingPrice'];
            $reserve_price = $auctionWatch['reservePrice'];
            $time_remaining = 3;
    
            echo('
            <li class="list-group-item d-flex justify-content-between">
              <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $itemID . '">' . $title . '</a></h5>' . $description . '<br/><mark style="background: lightblue;">' . $category_name . '</mark>' . ' ' . '<mark style="background: pink;" > £' . $starting_price . '</mark></div>
            </li>
          ');

        }
        mysqli_close($connection);
    }
      
        

?>


    <?php include_once("footer.php")?>