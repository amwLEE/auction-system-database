<?php 
  include_once("header.php");

  // Connect to database
  include 'database.php';
  
  // Get all the categories from category table
  $sql = "SELECT * FROM Category";
  $categories = mysqli_query($connection, $sql);
?>

<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="post" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything" name="keyword">
        </div>
      </div>
    </div>

    <!-- Filter by category -->
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat" name="cat">
          <!-- Drop down list where the options are fetched from Category table
              Source: https://www.geeksforgeeks.org/create-a-drop-down-list-that-options-fetched-from-a-mysql-database-in-php/ -->
          <option value="all">All categories</option>
            <?php
                while ($categoryName = mysqli_fetch_array($categories, MYSQLI_ASSOC)):;
            ?>
            <option value = "<?php echo $categoryName["categoryID"];?>">
            <?php echo $categoryName["categoryName"]; ?>
          </option>
          <?php
            endwhile;
          ?>
        </select>
      </div>
    </div>

    <!-- Order results -->
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option selected value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="endDate">Ending soonest</option>
          <option value="listDate">Newly listed</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" value="search" name="search" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>


<?php
  if (isset($_POST['search'])) {
    // Search bar. source: https://owlcation.com/stem/Simple-search-PHP-MySQL
    $keyword = mysqli_real_escape_string($connection, htmlspecialchars($_POST['keyword']));
    $category = $_POST['cat'];
    $ordering = $_POST['order_by']; 
  } else {
    // default values if search keyword, category and ordering are not specified
    $keyword = "";
    $category = "all";
    $ordering = 'endDate';  // Default ordering is set to Ending soonest
  }
  
  if (!isset($_POST['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_POST['page'];
  }

  echo($keyword);
  echo(" ");
  echo($category);
  echo(" ");
  echo($ordering);
  echo("\n");
  
  // Get listings corresponding to search keyword.
  // partial matching, i.e. any item name or description that contains the search keyword will be returned
  $searchQuery = "SELECT * from Auction           
                  WHERE itemName LIKE '%$keyword%'
                  OR itemDescription LIKE '%$keyword%'";  
  $searchResult = mysqli_query($connection, $searchQuery);
  $searchArr = array();
  if (mysqli_num_rows($searchResult) > 0){
    while ($row  = mysqli_fetch_assoc($searchResult)){
      $searchArr[] = $row['itemID'];
    }
  } else {
    echo("no search results\n");
  }
  $searchResults = implode(',', $searchArr);
  echo($searchResults);
  echo("\n");

  // Filter the search results by category
  if ($searchArr){
    if ($category == "all") {
      $filterQuery = "SELECT * FROM Auction WHERE itemID in ($searchResults)";
    } else {
      $filterQuery = "SELECT * 
                      FROM Auction 
                      WHERE itemID in ($searchResults)
                      AND categoryID=$category";
    }
  } 
  $filterResult = mysqli_query($connection, $filterQuery);
  $filterArr = array();
  if (mysqli_num_rows($filterResult) > 0){
    while ($row  = mysqli_fetch_assoc($filterResult)){
      $filterArr[] = $row['itemID'];
    }
  } else {
    echo("no search results \n");
  }
  $filterResults = implode(',', $filterArr);
  echo($filterResults);
  echo("\n");


  // Sort the filtered results by order.
  if ($filterArr){
    if ($ordering == "endDate"){            // ending soonest
      $orderQuery = "SELECT *
                     FROM Auction
                     WHERE itemID in ($filterResults)
                     ORDER BY endDateTime DESC";
    } elseif ($ordering == "listDate"){     // newly listed
      $orderQuery = "SELECT *
                     FROM Auction
                     WHERE itemID in ($filterResults)
                     ORDER BY startDateTime DESC";
    } elseif ($ordering == "priceHigh"){    // price high to low
      $orderQuery = "SELECT *
                     FROM Auction
                     WHERE itemID in ($filterResults)
                     ORDER BY startingPrice DESC";  // might have to change to highestBid
    } elseif ($ordering == "priceLow"){     // price low to high
      $orderQuery = "SELECT *
                     FROM Auction
                     WHERE itemID in ($filterResults)
                     ORDER BY endDateTime ASC";    // might have to change to highestBid
    }
  }
  $orderResult = mysqli_query($connection, $filterQuery);
  $orderArr = array();
  if (mysqli_num_rows($orderResult) > 0){
    while ($row  = mysqli_fetch_assoc($orderResult)){
      $orderArr[] = $row['itemID'];
    }
  } else {
  }
  $orderResults = implode(',', $orderArr);
  echo($orderResults);
  echo("\n");
  
  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  $num_results = mysqli_num_rows($orderResult); // TODO: Calculate me for real
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);

  while ($listing = mysqli_fetch_assoc($orderResult)){
    echo("entered while loop");
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $category = $listing['categoryName'];
    $end_time = new DateTime($listing['endDateTime']);

    $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id ORDER BY bidID DESC");
    if (mysqli_num_rows($mybids) > 0){
      $num_bids = mysqli_num_rows($mybids);
      $price = mysqli_fetch_row($mybids)[4];
    } else{
      $num_bids = 0;
      $price = 0;
    }

    $now = new DateTime();
    if ($now > $end_time) {
      if ($price > $listing['reservePrice']) {
        $status = 'Sold';
      } else {
        $status = 'Not sold';
      }
    } else {
      $status = 'In progress';
    }

    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $status);

  }
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">
<?php
  while ($listing = mysqli_fetch_assoc($orderResult)){
    echo("entered while loop");
    $item_id = intval($listing['itemID']);
    $title = $listing['itemName'];
    $desc = $listing['itemDescription'];
    $category = $listing['categoryName'];
    $end_time = new DateTime($listing['endDateTime']);

    $mybids = mysqli_query($connection, "SELECT * FROM Bid WHERE itemID=$item_id ORDER BY bidID DESC");
    if (mysqli_num_rows($mybids) > 0){
      $num_bids = mysqli_num_rows($mybids);
      $price = mysqli_fetch_row($mybids)[4];
    } else{
      $num_bids = 0;
      $price = 0;
    }

    $now = new DateTime();
    if ($now > $end_time) {
      if ($price > $listing['reservePrice']) {
        $status = 'Sold';
      } else {
        $status = 'Not sold';
      }
    } else {
      $status = 'In progress';
    }

    print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $category, $status);
    
  }
?>

</ul>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // // Copy any currently-set GET variables to the URL.
  // $querystring = "";
  // foreach ($_POST as $key => $value) {
  //   if ($key != "page") {
  //     $querystring .= "$key=$value&amp;";
  //   }
  // }
  
  // $high_page_boost = max(3 - $curr_page, 0);
  // $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  // $low_page = max(1, $curr_page - 2 - $low_page_boost);
  // $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  // if ($curr_page != 1) {
  //   echo('
  //   <li class="page-item">
  //     <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
  //       <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
  //       <span class="sr-only">Previous</span>
  //     </a>
  //   </li>');
  // }
    
  // for ($i = $low_page; $i <= $high_page; $i++) {
  //   if ($i == $curr_page) {
  //     // Highlight the link
  //     echo('
  //   <li class="page-item active">');
  //   }
  //   else {
  //     // Non-highlighted link
  //     echo('
  //   <li class="page-item">');
  //   }
    
  //   // Do this in any case
  //   echo('
  //     <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
  //   </li>');
  // }
  
  // if ($curr_page != $max_page) {
  //   echo('
  //   <li class="page-item">
  //     <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
  //       <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
  //       <span class="sr-only">Next</span>
  //     </a>
  //   </li>');
  // }
?>

  </ul>
</nav>


</div>



<?php include_once("footer.php")?>