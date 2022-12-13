<?php require("database.php")?>
<?php require("sendEmail.php")?>

<?php
    $now = date("Y-m-d H:i:s", time());

    if ($lastDateTime = file_get_contents('check_auctions.txt')) {
        $query = "SELECT a.itemID, a.itemName, a.sellerID, a.reservePrice FROM Auction a WHERE a.endDateTime>'$lastDateTime' AND a.endDateTime<='$now'";
        $items = mysqli_query($connection, $query);
        while ($item = mysqli_fetch_assoc($items)) {
            $itemID = $item["itemID"];
            $itemName = $item["itemName"];
            $sellerID = $item["sellerID"];
            $reservePrice = $item["reservePrice"];

            $query = "SELECT u.userID, u.firstName, u.email, MAX(b.bidPrice) as bidPrice FROM Users u, Bid b WHERE u.userID=b.buyerID AND b.itemID=$itemID GROUP BY u.userID ORDER BY MAX(b.bidPrice) DESC";
            $buyers = mysqli_query($connection, $query);
            $count = 0;
            $sold = -1;
            $winner = 0;
            while ($buyer = mysqli_fetch_assoc($buyers)) {
                $buyerID = $buyer["userID"];
                $buyerName = $buyer["firstName"];
                $buyerEmail = $buyer["email"];
                $bidPrice = $buyer["bidPrice"];
                if ($count==0) {
                    if ($bidPrice>=$reservePrice) {
                        $sold = 1;
                        $won = 1;
                        $winner = $buyerID;
                    } else {
                        $sold = 0;
                        $won = 0;
                    }
                } else {
                    $won = 0;
                }
                emailBuyer($itemName, $buyerName, $buyerEmail, $won);
                $count++;
            }

            $query = "SELECT u.firstName, u.email FROM Users u WHERE u.userID=$sellerID";
            $sellers = mysqli_query($connection, $query);
            $seller = mysqli_fetch_assoc($sellers);
            $sellerName =$seller["firstName"];
            $sellerEmail =$seller["email"];
            emailSeller($itemName, $sellerName, $sellerEmail, $sold, $winner);
        }
        mysqli_close($connection);
    }
    
    file_put_contents('check_auctions.txt', $now);
?>