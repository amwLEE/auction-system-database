<?php
include 'database.php';

$errors = array();

// following code only executes if 'submit' button is pressed - avoids some errors
if (isset($_POST['submit']))
{
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $firstName = mysqli_real_escape_string($connection, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($connection, $_POST['lastName']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $password2 = mysqli_real_escape_string($connection, $_POST['passwordConfirmation']);
    $account_type = mysqli_real_escape_string($connection, $_POST['accountType']);

    // source: https://codeshack.io/secure-registration-system-php-mysql/#registeringuserswithphpmysql
    // Check if the data was submitted properly on submit using isset() function
    if (!isset($email, $firstName, $lastName, $password, $password2))
    {
        $errors[] = 'Could not get submitted data inputs properly, please try again.';
    }

    // Make sure none of the submitted registration values are not empty.
    if (empty($email) || empty($firstName) || empty($lastName) || empty($password) || empty($password2))
    {
        $errors[] = 'Please fill in all required fields!';
    }

    // Ensure valid email address has been entered.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email))
    {
        $errors[] = 'Invalid email format entered, please enter a valid email address!';
    }

    // Ensure both passwords match (applies only when both fields are not blank)
    if ($password !== $password2 && !empty($password || $password2))
    {
        $errors[] = 'Your passwords did not match.';
    }

    // Check if email has already been registered in the database
    if (empty($errors))
    {
        $query = mysqli_query($connection, "SELECT * FROM Users WHERE email='{$email}'");

        // Email already exists in database
        if (mysqli_num_rows($query) > 0)
        {
            $errors[] = 'Email is already in use, please register using a different email.';
        }
        else
        {
            // Email does not already exist in database, insert new user account
            mysqli_query($connection, "INSERT INTO Users (firstName, lastName, email, userPassword, account_type)
        		VALUES ('$firstName','$lastName','$email', SHA('$password'), '$account_type')");
            $query = mysqli_query($connection, "SELECT * FROM Users WHERE email='{$email}'");
            if (mysqli_num_rows($query) == 1)
            {
                $success = true;
            }
            else
            {
                $errors[] = 'An unknown error occurred and your account was not created.';
            }
        }
    }

    if (!empty($errors))
    {
        $success = false;
    }
}
?>
