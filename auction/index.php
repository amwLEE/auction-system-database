<?php include("login_result.php")?>

<?php
  // For now, index.php just redirects to browse.php, but you can change this
  // if you like.
  if ((isset($_SESSION['account_type'])) && ($_SESSION['account_type']==2)) {
    header("Location: admin.php"); // admin landing page
  } elseif ((isset($_SESSION['account_type'])) && ($_SESSION['account_type']==1)) {
    header("Location: mylistings.php"); // seller landing page
  } else {
    header("Location: browse.php"); // default landing page
  }
?>