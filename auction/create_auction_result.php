<?php 
    include_once('header.php');
    require("database.php");
?>

<div class="container my-5">

    <?php

    $errors = array();


    if (isset($_POST['submit'])) {
        $sellerID = $_SESSION['userID'];
        $startDateTime = date('Y-m-d H:i:s');

        // Get the data from the form
        $title = mysqli_real_escape_string($connection, $_POST['auctionTitle']);
        $description = mysqli_real_escape_string($connection, $_POST['auctionDetails']);
        $categoryID = mysqli_real_escape_string($connection, $_POST['auctionCategory']);
        $startingPrice = mysqli_real_escape_string($connection, $_POST['auctionStartPrice']);
        $reservePrice = mysqli_real_escape_string($connection, $_POST['auctionReservePrice']);
        $endDateTime = mysqli_real_escape_string($connection, date('Y-m-d H:i:s', strtotime($_POST['auctionEndDate'])));
        
        // Check if data was properly submitted
        if (!isset($title, $description, $categoryID, $startingPrice, $reservePrice, $endDateTime)) {
            $errors[] = 'Could not get submitted data inputs properly, please try again.';
        }

        if (empty($sellerID)) {
            $errors[] = 'Issue with getting seller ID.';
        }
        
        // Ensure that none of the required inputs are empty
        if (empty($title) || empty($categoryID) || empty($startingPrice) || empty($endDateTime)) {
            $errors[] = 'Please fill in all required fields!';
        }

        // Ensure that the auction title is a string, and has max 64 bytes
        if (!is_string($title)) {
            $errors[] = 'Please ensure that your auction title is a string.';
        } else if (strlen($title) > 64) {
            $errors[] = 'Please shorten your auction title.';
        }

        // Ensure that the auction description is a string, and has max 255 bytes
        if (empty($description)) {
            $description = ""; // set it to empty
        } 
        if (!is_string($description)) {
            $errors[] = 'Please ensure that your auction description is a string.';
        } else if (strlen($description) > 4000) {
            $errors[] = 'Please shorten your auction description.';
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

        // Deal with image upload.
        // Source: https://www.w3schools.com/php/php_file_upload.asp
        if (!empty($_FILES["fileToUpload"]["name"])){
            $target_dir = "img/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
              $uploadOk = 1;
            } else {
              $errors[] = "File is not an image.";
              $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                $errors[] = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
        } else {
            $uploadOk = 0;
        }
        

        // Insert data into database if there are no errors with inputs.
        if (empty($errors)) {
            $sql = "INSERT INTO Auction (sellerID, itemName, itemDescription, categoryID, startDateTime, endDateTime, startingPrice, reservePrice)
            VALUES ('$sellerID', '$title','$description', '$categoryID', '$startDateTime', '$endDateTime', '$startingPrice', '$reservePrice')";
            
            if (mysqli_query($connection, $sql)) {
                $itemID = mysqli_insert_id($connection);
                $link_address = 'listing.php?item_id=' . $itemID;
                echo("<div class='text-center'>Auction successfully created! <a href=$link_address> View your new listing. </a></div>");
            } else {
                echo 'Error: ' . $sql . '<br>' . mysqli_error($connection);
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $insertImg = "INSERT INTO Images (itemID, itemImage) VALUES ('$itemID', '$target_file')";
                    $results = mysqli_query($connection, $insertImg);
                    if (!mysqli_query($connection, $insertImg)) {
                        echo 'Error: ' . $insertImg . '<br>' . mysqli_error($connection);
                    } 
                } else {
                    $errors[] =  "Sorry, there was an error uploading your file.";
                }
            } 

        // Guide users if there are errors with their input data.
        } else {
            foreach ($errors as $error) {
                echo '<p style="color:red;">' . $error . '</p>'; // display error messages
            }
        }
    }

    // Close the connection as soon as it's no longer needed
    mysqli_close($connection);
    
    ?>

</div>


<?php include_once("footer.php") ?>