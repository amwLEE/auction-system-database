<?php
  // Start new or resume existing session
  session_start();

  // index.php redirects to mylistings.php for seller accounts, or browse.php for buyer accounts and non-logged-in users.
  if (($_SESSION['logged_in'] == true) && ($_SESSION['account_type'] == 'seller')) {
    header("Location: mylistings.php");
  } else {
    header("Location: browse.php");
  }
?>