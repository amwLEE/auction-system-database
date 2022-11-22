<?php


// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.

include 'database.php';

session_start();

if (isset($_POST['loginForm'])){

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password =  mysqli_real_escape_string($connection, $_POST['password']);

    if ($email == "" || $password == ""){
        $login_error = "Please fill in all required fields";
    }else{
        $query = "SELECT * FROM Users WHERE email='{$email}' AND userPassword = SHA('$password')";
        $result = mysqli_query($connection, $query);
        
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {

                    $account_type = $row["account_type"];
                    
                    if ($account_type == 0){
                        $_SESSION['account_type'] = 'buyer';
                    }else{
                        $_SESSION['account_type'] = 'seller';
                    }

                    // start session
                    $log_success = true;
                    $_SESSION['logged_in'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['userID'] = $row['userID'];

                    
                    

                }
              } else {
                $login_error = "Wrong email or password.";
              }
        
    }

}

$connection->close();

// session_start();
// $_SESSION['logged_in'] = true;
// $_SESSION['username'] = "test";
// $_SESSION['account_type'] = "buyer";

// echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');



?>