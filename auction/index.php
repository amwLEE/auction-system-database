<?php include("login_result.php")?>

<?php
  // For now, index.php just redirects to browse.php, but you can change this
  // if you like.
  if (($_SESSION['logged_in']) && ($_SESSION['account_type']=='seller')) {
    header("Location: mylistings.php"); // seller landing page
  } else {
    header("Location: browse.php"); // default landing page
  }
?>