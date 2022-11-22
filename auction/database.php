<?php
  // Enable error reporting for mysqli before attempting to make a connection
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  // Open a new connection to the MySQL server
  $connection = mysqli_connect('localhost', 'adbadmin', 'Group10', 'AuctionDB')
                or die('Connection error: ' . mysqli_connect_error());

  // Set the desired charset after establishing a connection
  mysqli_set_charset($connection, 'utf8mb4');
?>