<?php 
  include_once("header.php");
  require("database.php");
  require("utilities.php");

  if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
  } else {
    // To handle the case where no user is logged in
    $userID = 0;
  }
  $pageType = 'listings'
?>


<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
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
              // Get all the category names from category table
              $query = "SELECT * FROM Category";
              $result = mysqli_query($connection, $query);
              while ($categoryName = mysqli_fetch_assoc($result)):;
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
          <option value="endDate">Ending soonest</option>
          <option value="listDate">Newly listed</option>
          <option value="priceLow">Price (low to high)</option>
          <option value="priceHigh">Price (high to low)</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" name="search" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>


<?php

  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    $keyword = '';
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    $category = 'all';
  }
  else {
    $category = $_GET['cat'];
  }
  
  if (!isset($_GET['order_by'])) {
    $ordering = 'endDate';
  }
  else {
    $ordering = $_GET['order_by'];
  }
  
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }
  
  // Get listings corresponding to search keyword.
  // partial matching, i.e. any item name or description that contains the search keyword will be returned
  $query = "SELECT itemID from Auction           
            WHERE endDateTime>NOW()
            AND (itemName LIKE '%$keyword%'
            OR itemDescription LIKE '%$keyword%')";  
  $result = mysqli_query($connection, $query);
  if (mysqli_num_rows($result) > 0){
    $searchResults = implode_colName($result, 'itemID');
  } else {
    $searchResults = "";	
  }

  // Filter the search results by category.
  if ($category == "all") {
    $query = "SELECT itemID FROM Auction WHERE itemID in ($searchResults)";
  } else {
    $query = "SELECT itemID 
              FROM Auction 
              WHERE itemID in ($searchResults)
              AND categoryID=$category";
  }

  if ($searchResults){
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) > 0){
      $filteredResults = implode_colName($result, 'itemID');
    } else {
      $filteredResults = "";	
    }
  } else {
    $filteredResults = "";
  }

  if ($filteredResults){
    $result = mysqli_query($connection, $query);
  }

  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  $num_results = mysqli_num_rows($result); 
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

<ul class="list-group">
<?php
  if ($num_results > 0) {
    // ending soonest
    if ($ordering == "endDate"){            
      $query = "SELECT * 
                FROM Auction a INNER JOIN Category c on a.categoryID = c.categoryID 
                WHERE a.itemID in ($filteredResults) 
                ORDER BY a.endDateTime ASC
                LIMIT ".(($curr_page-1)*$results_per_page).", $results_per_page";
      echo "<h5>Listings sorted by soonest end date:</h5>";

    // newly listed
    } elseif ($ordering == "listDate"){     
      $query = "SELECT * 
                FROM Auction a INNER JOIN Category c on a.categoryID = c.categoryID 
                WHERE a.itemID in ($filteredResults) 
                ORDER BY startDateTime DESC
                LIMIT ".(($curr_page-1)*$results_per_page).", $results_per_page";
      echo "<h5>Listings sorted by newly listed:</h5>";
    
    // bid price low to high, if item has no bids then starting price is used for comparison
    } elseif ($ordering == "priceLow"){    
      $query = "SELECT a.itemID, a.itemName, a.itemDescription, c.categoryName, a.endDateTime, a.startingPrice, IFNULL(bidPrice, startingPrice) AS bidPrice 
                FROM Auction a 
                LEFT JOIN (SELECT itemID, MAX(bidPrice) AS bidPrice FROM Bid GROUP BY itemID) b 
                  on a.itemID = b.itemID
                JOIN Category c on a.categoryID = c.categoryID
                WHERE a.itemID in ($filteredResults)
                ORDER BY bidPrice ASC
                LIMIT ".(($curr_page-1)*$results_per_page).", $results_per_page";
      echo "<h5>Listings sorted by lowest to highest price:</h5>";

    // bid price high to low, if item has no bids then starting price is used for comparison
    } elseif ($ordering == "priceHigh"){ 
      $query = "SELECT a.itemID, a.itemName, a.itemDescription, c.categoryName, a.endDateTime, a.startingPrice, IFNULL(bidPrice, startingPrice) AS bidPrice 
                FROM Auction a 
                LEFT JOIN (SELECT itemID, MAX(bidPrice) AS bidPrice FROM Bid GROUP BY itemID) b 
                  on a.itemID = b.itemID
                JOIN Category c on a.categoryID = c.categoryID
                WHERE a.itemID in ($filteredResults)
                ORDER BY bidPrice DESC
                LIMIT ".(($curr_page-1)*$results_per_page).", $results_per_page";
      echo "<h5>Listings sorted by highest to lowest price:</h5>";
    }
    $result = mysqli_query($connection, $query);
    print_all_listings($connection, $result, $userID, $pageType);

  } else {
    echo "<br><h5>No listings match your search, please try again with a different query.</h5>";
  }
  // Close the connection as soon as it's no longer needed
  mysqli_close($connection);
?>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>



</div>



<?php include_once("footer.php")?>