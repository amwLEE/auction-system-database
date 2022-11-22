<?php
  // Start new or resume existing session
  session_start();

  // The index page is the URL or local file that automatically loads when a web browser starts and when the browser's 'home' button is pressed
  // Check user's credentials from session
  // Redirect to mylistings.php for seller accounts, or browse.php for buyer accounts and non-logged-in users
  if (isset($_SESSION['account_type'])) {
    if ($_SESSION['account_type'] == 'seller') {
      header("Location: mylistings.php");
    } elseif ($_SESSION['account_type'] == 'buyer') {
      header("Location: browse.php");
    }
  } else {
    header("Location: browse.php");
  }
?>