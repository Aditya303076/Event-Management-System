<?php
    session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <header class="header">
        <p class="main"><span>EVENT</span>management</p>

    </header>
    <div class="login-box">
        <h2>Login Page</h2>
        <form action="" method="post">
            <div class="user-box">
                <input type="text" name="username"  required="">
                <label>Email-ID</label>
            </div>
            <div class="user-box">
                <input type="password" name="password"  required="">
                <label>Password</label>
            </div>
            <input type="submit" value="Log in"  class="btn">
            <a href="../index.html" class="btn" style="float: right;">Home </a>
        </form>
    
        <p class="signup-link" style="color:white;">Don't have an account? <a href="signup.php" style="text-decoration:none;color:white;">Sign up here</a></p>
    </div>

    <?php
    $_SESSION["firstname"] = "";
    $_SESSION["lastname"] = "";
    $_SESSION["email"] = "";
    $_SESSION["mobileno"] = "";
    $_SESSION["uId"] =  "";
    $_SESSION["b_type"] = "";
    $_SESSION["b_package"] = "";
    $_SESSION["b_price"] = "";
    $_SESSION["state"] = 0;
    $_SESSION["b_venue"] = "";
    $_SESSION["b_date"] = "";
    $_SESSION["b_msg"] = "";
    $_SESSION["b_state"] = "";

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event_mgt";

    //Create Connection to DB
    $conn = new mysqli($servername, $username, $password, $dbname);
    //Check Connnection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($username == "admin@gmail.com" and $password == "admin")
        {
            echo '<script>
            setTimeout(function(){
                window.location.href = "../adminpanel/admin.php";
            })
            </script>';
         exit();

        }
        else
        {
            
            $sql = "SELECT First_Name,Last_Name,Email_ID,MobileNo,ID FROM tbl_user WHERE Email_ID = '". $username ."' and PWD = '" . $password . "'";
            // $sql = "SELECT First_Name,Last_Name,Email_ID,MobileNo,ID FROM tbl_user WHERE Email_ID = '' or '1' = '1' and PWD = '" . $password . "';";
            $result = $conn->query($sql);

            $user_details = mysqli_fetch_array($result);
            // print_r($result);

                
        if ($result->num_rows > 0)
        {

            $_SESSION["firstname"] = $user_details["First_Name"];
            $_SESSION["lastname"] = $user_details["Last_Name"];
            $_SESSION["email"] = $user_details["Email_ID"];
            $_SESSION["mobileno"] = $user_details["MobileNo"];
            $_SESSION["uId"] =  $user_details["ID"];
            $_SESSION["state"] = 1;
            echo '<script>
            setTimeout(function(){
                window.location.href = "user.php";
            })
            </script>';
            exit();
        }
        else
        {
            echo '<script>alert("Invalid Username or Password.")</script>';

        }
    }
    }

    $conn->close();
    ?>
</body>
</html>
