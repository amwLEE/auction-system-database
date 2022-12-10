<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<?php
  $url = $_SERVER['REQUEST_URI'];
  if (!strpos($url, "listing.php?item_id=")) {
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit;
  }

  // Check user's credentials (cookie/session).
  if (isset($_SESSION['logged_in'])==1) {
    $account_type = $_SESSION['account_type'];
    $userID = $_SESSION['userID'];
  } else {
    $account_type = 'buyer';
  }

  

  // Get info from the URL:
  $item_id = $_GET['item_id'] or die("<h1>404 Not Found</h1>The page that you have requested could not be found.");

  // TODO: Use item_id to make a query to the database.
  $query = "SELECT * FROM Auction WHERE itemID=$item_id";
  $result = mysqli_query($connection, $query);
  if (mysqli_num_rows($result) == 0) {
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit;
  }
  $auction = mysqli_fetch_assoc($result);

  $category_id = $auction['categoryID'];
  $query = "SELECT * FROM Category WHERE categoryID='$category_id'";
  $result = mysqli_query($connection, $query);
  $category = mysqli_fetch_assoc($result);

  $query = "SELECT * FROM Bid WHERE itemID=$item_id ORDER BY bidID DESC";
  $result = mysqli_query($connection, $query);

  $title = $auction['itemName'];
  $description = $auction['itemDescription'];
  $category_name = $category['categoryName'];
  $end_time = new DateTime($auction['endDateTime']);
  $starting_price = $auction['startingPrice'];
  $reserve_price = $auction['reservePrice'];
  $num_bids = mysqli_num_rows($result);
  if (mysqli_num_rows($result) > 0){
    $current_price = mysqli_fetch_row($result)[4];
  } else {
    $current_price = $starting_price-0.01;
  }

  // TODO: Note: Auctions that have ended may pull a different set of data,
  //       like whether the auction ended in a sale or was cancelled due
  //       to lack of high-enough bids. Or maybe not.
  // Calculate time to auction end:
  $now = new DateTime();
  
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }
  
  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  if ((isset($_SESSION['logged_in'])) && ($_SESSION["logged_in"]==1 )){
    $queryWatch = "SELECT * FROM Watch WHERE userID = $userID and itemID = $item_id";
    $resultWatch = mysqli_query($connection, $queryWatch);
    $has_session = true;

    if (mysqli_num_rows($resultWatch) == 0 ){
      $watching = false;
    } else { 
      $watching = true;
    }

  } else {
    $has_session = false;
  }


?>


<div class="container">

    <div class="row">
        <!-- Row #1 with auction title + watch button -->
        <div class="col-sm-8">
            <!-- Left col -->
            <h2 class="my-3"><?php echo($title); ?></h2>
        </div>
        <div class="col-sm-4 align-self-center">
            <!-- Right col -->
            <?php
            /* The following watchlist functionality uses JavaScript, but could
             just as easily use PHP as in other places in the code */
              if (($account_type == 'buyer')):
            ?>
            <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?>>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to
                    watchlist</button>
            </div>
            <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?>>
                <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove
                    watch</button>
            </div>
            <?php endif /* Print nothing otherwise */ ?>
        </div>
    </div>

    <div class="row">
      <!-- Row #2 with auction description + bidding info -->
      <div class="col-sm-8">
        <!-- Left col with item info -->
          <?php
            // Get item status
            mysqli_data_seek($result, 0);
            if ($now > $end_time) {
              if ($num_bids == 0) {
                $status = '<mark style="background: red">Not sold</mark>';
              } elseif ($current_price < $auction['reservePrice']) {
                $status = '<mark style="background: red">Not sold</mark>';
              } else {
                $status = '<mark style="background: green">Sold</mark>';
              }
            } else {
              $status = '<mark style="background: orange">In progress</mark>';
            }
          ?>
        <div class="itemDescription">
            <?php echo($description); ?>
            <?php echo "<div><mark style='background: lightblue'>$category_name</mark> $status</div>"; ?>
        </div>

        <div>
          <?php
            // Get uploaded image of item if there is one
            $query = "SELECT * FROM Images WHERE itemID=$item_id";
            $imgresult = mysqli_query($connection, $query);
            if (mysqli_num_rows($imgresult) == 0) {
              $image = "img/no_image.png";
            } else {
              while ($row = mysqli_fetch_assoc($imgresult)) {
                $image = $row['itemImage'];
              }
            }
          ?>
            <img src= <?php echo $image ?> height=500 width=600>
        </div>
          
          


      </div>

        <div class="col-sm-4">
          <!-- Right col with bidding info -->
          <p>
              <?php if ($now > $end_time): ?>
              This auction ended <?php echo(date_format($end_time, 'j M Y H:i')) ?>
              <!-- TODO: Print the result of the auction here? -->
              <?php else: ?>
              Auction ends <?php echo(date_format($end_time, 'j M Y H:i') . $time_remaining) ?>
          </p>

          <?php if ($account_type == 'buyer'): ?>
          <p class="lead"><?php echo 'Starting price: £' . number_format($starting_price, 2); ?></p>
          <p class="lead">
          <?php
            if ($num_bids>0) {
              echo 'Current bid: £';
              echo(number_format($current_price, 2));
            }
          ?>


            <!-- Bidding form -->
            <form method="POST">
              <div class="message">
                <?php
                  include 'place_bid.php';                    
                  global $error_msg;
                  if (isset($success) && ($success == true)){
                    echo '<p style="color:green;">Your bid has been submitted successfully! This page will automatically refresh in 3 seconds...<p>';
                    echo "<meta http-equiv='refresh' content='3'>";
                  }else{
                    echo '<p style="color:red;">'.$error_msg.'</p>'; //display error message
                  }
                ?>
              </div>
              <div class="input-group">
                  <div class="input-group-prepend">
                      <span class="input-group-text">£</span>
                  </div>
                  <input type="number" class="form-control" id="bid" name="bidPrice"
                      placeholder="<?=number_format($current_price+0.01, 2)?>" min="<?=$current_price+0.01?>"
                      step="0.01" onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)">
              </div>
              <small id="bidPriceHelp" class="form-text text-muted"><span class="text-danger">*
                      Required.</span></small>
              <button type="submit" class="btn btn-primary form-control" name="submitBidForm"
                  value="submitBidForm">Place bid</button>
            </form>
            <?php endif ?>
            <?php endif ?>

            <?php if ($account_type == 'seller'): ?>
            <p class="lead"><?php echo 'Starting price: £' . number_format($starting_price, 2); ?></p>
            <p class="lead"><?php echo 'Reserve price: £' . number_format($reserve_price, 2); ?></p>
            <p class="lead">
            <?php
              if ($num_bids>0) {
                echo 'Current bid: £' . number_format($current_price, 2);
              } else {
                echo 'Current bid: £' . number_format(0, 2);
              }
            ?>
            </p>
            <?php endif ?>

            <br>
            <h6>Bid History</h6>
            <p>Total number of bids: <?=$num_bids?></p>
            <?php if ($num_bids > 0): ?>
            <table border="1">
                <thead>
                  <tr>
                    <th>Bid ID</th>
                    <th>User ID</th>
                    <th>UTC Timestamp</th>
                    <th>Bid Price</th>
                  </tr>
                </thead>

                <tbody>
                  <?php mysqli_data_seek($result, 0); ?>
                  <?php while ($bid = mysqli_fetch_assoc($result)): ?>
                  <tr>
                      <td><?php echo $bid['bidID']; ?></td>
                      <td><?php echo $bid['buyerID']; ?></td>
                      <td><?php echo $bid['bidTimeStamp']; ?></td>
                      <td><?php echo "£" . $bid['bidPrice']; ?></td>
                  </tr>
                  <?php endwhile ?>
                </tbody>
            </table>
            <br>
            <?php endif ?>

            <h6>Remarks</h6>
            <?php
              mysqli_data_seek($result, 0);
              if ($now > $end_time) {
                if ($num_bids == 0) {
                  echo "<span style='color:red;'>This item was not sold because there were no bids</span>";
                } elseif ($current_price < $auction['reservePrice']) {
                  echo "<span style='color:red;'>This item was not sold because reserve price not met</span>";
                } else {
                  $winner = mysqli_fetch_row($result)[2];
                  echo "This item was sold to User#$winner";
                }
              } else {
                echo "This auction is still in progress";
              }
              // Close the connection as soon as it's no longer needed
              mysqli_close($connection);
            ?>

      </div> <!-- End of right col with bidding info -->

    </div> <!-- End of row #2 -->


    <?php include_once("footer.php")?>


    <script>
    // JavaScript functions: addToWatchlist and removeFromWatchlist.

    function addToWatchlist(button) {
        console.log("These print statements are helpful for debugging btw");
        // This performs an asynchronous call to a PHP function using POST method.
        // Sends item ID and userID as an argument to that function.

        $.ajax('watchlist_funcs.php', {
            type: "POST",
            data: {
                functionname: 'add_to_watchlist',
                itemID: <?php echo $item_id ?>,
                userID: <?php echo $userID?>
            },

            success: function(obj, textstatus) {

                console.log(obj);

                // Callback function for when call is successful and returns object
                if (obj == "success") {
                    $("#watch_nowatch").hide();
                    $("#watch_watching").show();
                } else {
                    var mydiv = document.getElementById("watch_nowatch");
                    mydiv.appendChild(document.createElement("br"));
                    mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
                }
            },

            error: function(obj, textstatus) {
                console.log("Error");
            }
        }); // End of AJAX call

    } // End of addToWatchlist func

    function removeFromWatchlist(button) {
        // This performs an asynchronous call to a PHP function using POST method.
        // Sends item ID as an argument to that function.
        $.ajax('watchlist_funcs.php', {
            type: "POST",
            data: {
                functionname: 'remove_from_watchlist',
                itemID: <?php echo $item_id ?>,
                userID: <?php echo $userID?>
            },

            success: function(obj, textstatus) {
                // Callback function for when call is successful and returns obj

                if (obj == "success") {
                    $("#watch_watching").hide();
                    $("#watch_nowatch").show();
                } else {
                    var mydiv = document.getElementById("watch_watching");
                    mydiv.appendChild(document.createElement("br"));
                    mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
                }
            },

            error: function(obj, textstatus) {
                console.log("Error");
            }
        }); // End of AJAX call

    } // End of addToWatchlist func
    </script>