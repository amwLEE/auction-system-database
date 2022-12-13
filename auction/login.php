<?php include_once("header.php")?>


<div class="container">
    <h2 class="my-3">Login </h2>
    <form method="POST" action="login.php">
        <div class="message">
                <?php

        include 'login_result.php';
            global $login_error;

            if (isset($log_success) && ($log_success == true) ){
            echo '<p style="color:green;">You have successfully logged in! You will be redirected shortly.<p>';
                //Redirect to index after 5 seconds
                header("refresh:3;url=index.php");
            }else{
            echo '<p style="color:red;">'.$login_error.'</p>'; //display error message
            }
        ?>
            </div>

        <!-- no form validation yet -->
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
            </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
        </div>
        <div class="form-group row">
            <button type="submit" name="loginForm" class="btn btn-primary form-control">Sign in</button>
        </div>
    </form>
    <div class="text-center">or <a href="register.php">Create an account</a></div>
</div>
<?php include_once("footer.php")?>