
<?php
    session_start();
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup page</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <div class="login-box" style="margin-bottom:5rem;">
        <h2 style="margin-top:5rem;">Signup Page</h2>
        <form action="" method="post" style="margin-bottom:5rem;">
            <div class="user-box">
                <input type="text" name="firstname" required="">
                <label>First Name</label>
            </div>
            <div class="user-box">
                <input type="text" name="lastname"  required="">
                <label>Last Name</label>
            </div>
            <div class="user-box">
                <input type="number" name="mobileNo"  required="">
                <label>Mobile Number</label>
            </div>
            <div class="user-box">
                <input type="Email-ID" name="Email" required="">
                <label>Email</label>
            </div>
            <div class="user-box">
                <input type="password" name="Password" required="">
                <label>Password</label>
            </div>
            <div class="user-box">
                <input type="password" name="Cpassword" required="">
                <label>Confirm Password</label>
            </div>
            <input type="submit" value="Submit" class="btn" style="margin: 0 40% 0 40%;">
            <p style="color:white;">Already have an account? <a href="login.php" style="text-decoration:none;color:black;" class="btn">Login here</a></p>
        </form>
        
    </div>

    <?php
    $servername = getenv('DB_HOST') ?: "localhost";
    $username = getenv('DB_USER') ?: "root";
    $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
    $dbname = getenv('DB_NAME') ?: "event_mgt";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $emailid = $_POST['Email'];
        $password = $_POST['Password'];
        $mobileno = $_POST['mobileNo'];
        $cpassword = $_POST['Cpassword'];

        if ($password != $cpassword) {
            echo '<script>alert("Passwords do not match")</script>';
        } else {
           /* $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            echo "Hashed Password before Insert: " . $hashedPassword . "<br>";*/

            $sql = "INSERT INTO tbl_user (First_Name,Last_Name,Email_ID,MobileNo,PWD) values ('" . $firstname . "','" . $lastname . "','" . $emailid ."','" . $mobileno . "','" . $password . "')";
            
            $result = $conn->query($sql);

            if ($result)
            {
                
                echo '<script>alert("Account created Successfully")</script>';
                                
                echo '<script>
                    setTimeout(function(){
                        window.location.href = "login.php";
                    })
                    </script>';
                 exit();
                exit();
            }
            else
            {
                echo '<script>alert("Please Enter Correct Data.")</script>';
    
            }
    
            
            
            /*$stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();*/
        }
    }

    $conn->close();
    ?>
</body>
</html>
