<?php
    include_once 'header.php';
    include_once 'database.php'; 
    require("utilities.php");
?>

<div class="container">
    <h2 class="my-3">Bid Creation Form</h2>
    <!-- Create Bid Registration Form -->
    <form action="bid_form.php" method="POST"> 
        <div class= "message">
            <?php
            include 'place_bid.php';                    
                global $error_msg;
                if (isset($success) && ($success == true) ){
                echo '<p style="color:green;">Your bid has been submitted successfully!<p>';
                }else{
                echo '<p style="color:red;">'.$error_msg.'</p>'; //display error message
                }
            ?>
        </div>
        
        <!-- Table Parameters: bidID, itemID, buyerID, bidTimeStamp, bidPrice -->
        <div class="form-group row">
            <!-- Bid Price -->
            <label for="bidPrice" class="col-sm-2 col-form-label text-right">Bid Price ($):</label>
                <div class="col-sm-3">
                    <input type="number" class="form-control" id="bidPrice" name="bidPrice" placeholder="0.00" min="0" step="0.01">
                    <!-- Should we set the minimum value to the current bid price or 0? -->
                    <small id="bidPriceHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
                </div>
        </div>

        <div class="form-group">
            <button type="submit" name="submitBidForm" value="submitBidForm" class="btn btn-success form-control">Submit Bid</button>
        </div>
    </form>
</div>            

<?php include_once("footer.php")?>


