<?php
    include 'database.php'; 

    // session_start(); 
    // if (isset($_SESSION['logged_in'])) 
    //     header("Location: index.php");

    if (isset($_POST['submit'])){

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $firstName = mysqli_real_escape_string($connection, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connection, $_POST['lastName']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $password2 = mysqli_real_escape_string($connection, $_POST['passwordConfirmation']);
    $account_type = mysqli_real_escape_string($connection,$_POST['accountType']);

    // check if all fields are filled in 
    if ($email != "" && $firstName != "" && $lastName != "" && $password != "" && $password2 != "" ){

    

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Invalid email format";

            if ($password !== $password2){ 
                $error_msg = 'Your passwords did not match.';
            }
        }
       else{
            $query = mysqli_query($connection, "SELECT * FROM Users WHERE email='{$email}'");
            if (mysqli_num_rows($query) == 1){
            
                $error_msg = 'That email is already registered.';
            }


            mysqli_query($connection,"INSERT INTO Users (firstName, lastName, email, userPassword,account_type)
            VALUES ('$firstName','$lastName','$email',SHA('$password')),'$account_type')");

            $query = mysqli_query($connection, "SELECT * FROM Users WHERE email='{$email}'");
            if (mysqli_num_rows($query) == 1){
            
                $success = true;
            }
            else
                $error_msg = 'An error occurred and your account was not created.';

        }
       
    }
    else{
        $error_msg = "Please fill in all required fields";
    }
    

}


    
?>


