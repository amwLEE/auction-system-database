<?php include_once("header.php")?>
<?php require("database.php");?>
<?php include("watchlist_funcs.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Watchlist Items</h2>



<?php
    $userID = $_SESSION['userID'];    
    $query = "SELECT * FROM Watch where userID = '{$userID}'";

    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 0 ){
        echo "No items in watchlist";
    }
        $watchlist = mysqli_fetch_assoc($result);
        $itemID = $watchlist['itemID'];


        // retrieving items on watchlist
        $query= "SELECT * FROM Auction WHERE itemID = '{$itemID}'";
        $result = mysqli_query($connection, $query);

        $auction = mysqli_fetch_assoc($result);
        
        $title = $auction['itemName'];
        $description = $auction['itemDescription'];
        $category_name = $category['categoryName'];
        $end_time = new DateTime($auction['endDateTime']);
        $starting_price = $auction['startingPrice'];
        $reserve_price = $auction['reservePrice'];




?>

<div class="container">

    <div class="row">
        <!-- Row #1 with auction title + watch button -->
        <div class="col-sm-8">
            <!-- Left col -->
            <h2 class="my-3"><?php echo($title); ?></h2>
        </div>
        <div class="col-sm-4 align-self-center">
</div>



<?php include_once("footer.php")?>