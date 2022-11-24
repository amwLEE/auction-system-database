<?php include_once("header.php") ?>

<div class="container my-5">

    <?php

    include 'database.php';

    $errors = array();

    if (isset($_POST['submit'])) {
        $title = mysqli_real_escape_string($connection, $_POST['auctionTitle']);
        $description = mysqli_real_escape_string($connection, $_POST['auctionDetails']);
        $categoryID = mysqli_real_escape_string($connection, $_POST['auctionCategory']);
        $startingPrice = mysqli_real_escape_string($connection, $_POST['auctionStartPrice']);
        $reservePrice = mysqli_real_escape_string($connection, $_POST['auctionReservePrice']);
        $endDateTime = mysqli_real_escape_string($connection, date('Y-m-d H:i:s', strtotime($_POST['auctionEndDate'])));

        $startDateTime = date('Y-m-d H:i:s');

        // check if data was properly submitted
        if (!isset($title, $description, $categoryID, $startingPrice, $reservePrice, $endDateTime)) {
            $errors[] = 'Could not get submitted data inputs properly, please try again.';
        }
        
        // Ensure that none of the required inputs are empty
        if (empty($title) || empty($description) || empty($categoryID) || empty($startingPrice) || empty($endDateTime)) {
            $errors[] = 'Please fill in all required fields!';
        }

        // Ensure that the auction title is a string, and has max 64 characters
        if (!is_string($title)) {
            $errors[] = 'Please ensure that your auction title is a string.';
        } else if (strlen($title) > 64) {
            $errors[] = 'Please make sure your auction title is less than 64 characters.';
        }

        // Ensure that the auction description is a string, and has max 255 characters
        if (!is_string($description)) {
            $errors[] = 'Please ensure that your auction description is a string.';
        } else if (strlen($description) > 64) {
            $errors[] = 'Please make sure your auction description is less than 255 characters.';
        }
        
        // If seller does not specify reserve price, automatically set it to starting price to prevent NULL values
        if (empty($reservePrice)) {
            $reservePrice = $startingPrice;
        } else if ($reservePrice < $startingPrice) {
            $errors[] = 'Please ensure that reserve price is not lower than starting price.';
        }

        // Ensure that auction end date is later than start date.
        if (!empty($endDateTime) && $endDateTime < $startDateTime) {
            $errors[] = 'Please ensure that auction end date and time is later than the current date and time.';
        }

        // Insert data into database if there are no errors with inputs.
        if (empty($errors)) {
            $sql = "INSERT INTO Auction (itemName, itemDescription, categoryID, startDateTime, endDateTime, startingPrice, reservePrice)
            VALUES ('$title','$description', '$categoryID', '$startDateTime', '$endDateTime', '$startingPrice', '$reservePrice')";
            
            if (mysqli_query($connection, $sql)) {
                echo ('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($connection);
            }

        // Guide users if there are errors with their input data.
        } else {
            foreach ($errors as $error) {
                echo '<p style="color:red;">' . $error . '</p>'; // display error messages
            }
        }
    }

    ?>

</div>


<?php include_once("footer.php") ?>