<?php


// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.

include 'database.php';

if (isset($_POST['loginForm'])){

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password =  mysqli_real_escape_string($connection, $_POST['password']);

    if ($email == "" || $password == ""){
        $login_error = "Please fill in all required fields";
    }else{
        $query = "SELECT * FROM Users WHERE email='{$email}' AND password = SHA('$password')";
        $result = mysqli_query($connection, $query);
        
            // something wrong here.
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "email: ". $row["email"].". First Name: ".$row['firstName'];

                }
              } else {
                echo "0 results";
              }
        
    }

}
else{
    $login_error =  'Log in unsuccesful';
}
$connection->close();

// session_start();
// $_SESSION['logged_in'] = true;
// $_SESSION['username'] = "test";
// $_SESSION['account_type'] = "buyer";

// echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');

// Redirect to index after 5 seconds
// header("refresh:5;url=index.php");

?>