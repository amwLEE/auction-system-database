<?php
require("database.php");

// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($interval) {

    if ($interval->days == 0 && $interval->h == 0) {
      // Less than one hour remaining: print mins + seconds:
      $time_remaining = $interval->format('%im %Ss');
    }
    else if ($interval->days == 0) {
      // Less than one day remaining: print hrs + mins:
      $time_remaining = $interval->format('%hh %im');
    }
    else {
      // At least one day remaining: print days + hrs:
      $time_remaining = $interval->format('%ad %hh');
    }

  return $time_remaining;

}


// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
// Prints user bid too if user has made a bid
function print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $status, $userID, $connection)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  }
  else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }
  
  if (in_array($status, ['In progress'])) {
    $mark_status = '<mark style="background: orange;">';
  } elseif (in_array($status, ['Won', 'Sold'])) {
    $mark_status = '<mark style="background: green;">';
  } elseif (in_array($status, ['Loss', 'Not sold'])) {
    $mark_status = '<mark style="background: red;">';
  } else {
    $mark_status = '<mark style="background: grey;">';
  }

  // Check whether user has made a bid on this particular item
  $query = "SELECT * 
            FROM Bid WHERE buyerID = '{$userID}' 
            AND itemID = '{$item_id}' 
            ORDER BY bidID DESC ";

  $userBidResult = mysqli_query($connection, $query);
  
  if (mysqli_num_rows($userBidResult) > 0){
    $userBidDetails = mysqli_fetch_assoc($userBidResult);
    $userBidPrice = $userBidDetails['bidPrice'];
  
    // If user has made a bid on this item, display user's bid besides current bid
    echo(
      '<li class="list-group-item d-flex justify-content-between">
      <div class="p-2 mr-5">
        <h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' .
        $desc_shortened . '<br/>' .
        '<mark style="background: lightblue;">' . $category . '</mark>&nbsp;' .
        $mark_status . $status . '</mark>' .
      '</div>
      
      <div>
      <span style="font-size: 1.5em">Your Bid: £' . number_format($userBidPrice,2) . '</span><br/>' .
      '</div>

      <div class="text-center text-nowrap">
        <span style="font-size: 1.5em">Current Bid: £' . number_format($price, 2) . '</span><br/>' .
        $num_bids . $bid . '<br/>' .
        $time_remaining .
      '</div>
      </li>'
    );

  // User has not made a bid on the item, so just display current bid
  } else if ($num_bids > 0) {
    echo(
      '<li class="list-group-item d-flex justify-content-between">
      <div class="p-2 mr-5">
        <h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' .
        $desc_shortened . '<br/>' .
        '<mark style="background: lightblue;">' . $category . '</mark>&nbsp;' .
        $mark_status . $status . '</mark>' .
      '</div>

      <div class="text-center text-nowrap">
        <span style="font-size: 1.5em">Current Bid: £' . number_format($price, 2) . '</span><br/>' .
        $num_bids . $bid . '<br/>' .
        $time_remaining .
      '</div>
      </li>'
    );
  // There are no bids on this item - display starting price
  } else {
    echo(
      '<li class="list-group-item d-flex justify-content-between">
      <div class="p-2 mr-5">
        <h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' .
        $desc_shortened . '<br/>' .
        '<mark style="background: lightblue;">' . $category . '</mark>&nbsp;' .
        $mark_status . $status . '</mark>' .
      '</div>

      <div class="text-center text-nowrap">
        <span style="font-size: 1.5em">Starting price: £' . number_format($price, 2) . '</span><br/>' .
        $num_bids . $bid . '<br/>' .
        $time_remaining .
      '</div>
      </li>'
    );
  }
}


// print_all_listings:
// This function loops through all listings returned from a query and prints them out
function print_all_listings($connection, $result, $userID, $pageType) {
  while ($listing = mysqli_fetch_assoc($result)){
    // get listing details
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $category = $listing['categoryName'];
    $end_time = new DateTime($listing['endDateTime']);
    
    // get the latest bid (highest bid price)
    $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id ORDER BY bidID DESC");
    if (mysqli_num_rows($mybids) > 0){
      $num_bids = mysqli_num_rows($mybids);
      $price = mysqli_fetch_row($mybids)[4];
    } else {
      $num_bids = 0;
      $price = $listing['startingPrice'];
    }

    // print out status of listing
    $now = new DateTime();
    if ($now > $end_time) {
      if ($price > $listing['reservePrice']) {
        if ($pageType == 'bids') {
          $status = 'Won';
        } else {
          $status = 'Sold';
        }
      } else {
        if ($pageType == 'bids') {
          $status = 'Loss';
        } else {
          $status = 'Not sold';
        }
      }
    } else {
      $status = 'In progress';
    }
    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $status, $userID, $connection);
  }
}

// implode_arr:
// implodes the values in a specific column from the results of a query
// returns a single string containing all the values in the specified column, separated by commas
function implode_colName($result, $colName) {
  $arr = array();
  while ($listing = mysqli_fetch_assoc($result)){
    $arr[] = $listing[$colName];
  }
  $imploded = implode(',', $arr);
  return $imploded;
}

// Check user's credentials from session
// Extract user ID and deny access to all other account types
function check_user_type($type) {
  if (isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == $type) {
      $userID = $_SESSION['userID'];
    } else {
      $userID = 0;
      exit("<h5 class='access_denied'>Access denied: You do not have permission to view this page.</h5>");
    }
  } else {
    $userID = 0;
    exit("<h5 class='access_denied'>Access denied: You do not have permission to view this page.</h5>");
  }
  return $userID;
}


?>