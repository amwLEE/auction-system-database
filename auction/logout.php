<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

unset($_SESSION['logged_in']);
unset($_SESSION['account_type']);
unset($_SESSION['email']);
unset($_SESSION['userID']);

$success = false;
$log_success= false;

setcookie(session_name(), "", time() - 360);
session_destroy();


// Redirect to index
header("Location: index.php");

?>