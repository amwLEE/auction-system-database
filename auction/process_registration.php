<?php include_once("header.php")?>

<?php
    include 'database.php'; 


    if (isset($_POST['submit'])){
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $firstName = mysqli_real_escape_string($connection, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connection, $_POST['lastName']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $password2 = mysqli_real_escape_string($connection, $_POST['passwordConfirmation']);
    $success = false;


    // check if all fields are filled in 
    if ($email != "" && $firstName != "" && $lastName != "" && $password != "" && $password2 != "" ){

        if ($password != $password2){
            $error_msg = 'Your passwords did not match.';
        }
       
    }
    else{
        $error_msg = "Please fill in all required fields";
    }
 
    // insert into database
    mysqli_query($connection,"INSERT INTO Users (firstName, lastName, email, password)
     VALUES ('$firstName','$lastName','$email',SHA('$password'))");

     // verify the user's account was created
    $query = mysqli_query($connection, "SELECT * FROM users WHERE email='{$email}'");
    if (mysqli_num_rows($query) == 1){

        $success = true;
    }
    else
        $error_msg = 'An error occurred and your account was not created.';
        
    }
    mysqli_close($connection);
?>


