<?php
  
  session_start();
  $email = $_SESSION["email"];

    use PHPMailer\PHPMailer\PHPMailer;  
    use PHPMailer\PHPMailer\Exception;
    // include '../connection.php';
    require 'Exception.php';
    require 'PHPMailer.php';
    require 'SMTP.php';
    // require '../vendor/autoload.php';
        
      $mail=new PHPMailer(true);

      try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        // $mail->Username   = 'adityachudasama098@gmail.com';
        $mail->Username   = 'eventmanagementsytem@gmail.com';                     //SMTP username
        $mail->Password   = 'trih wmya cyft prco';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('eventmanagementsytem@gmail.com', 'Event management sytem');
        $mail->addAddress($email);     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        //$mail->addReplyTo('crickteamofficial@gmail.com', 'no-reply');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
    
        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Booking Application Success';
        $mail->Body    = 'Hello, Your <b>Booking</b> will be confirm and administrator will contact soon';
        $mail->AltBody = 'thank you !';
    
        $mail->send();
        echo '<script>alert("Payment Successful.")</script>';
                    
                echo '<script>
                        setTimeout(function(){
                        window.location.href = "booking.php";
                    })
                    </script>';
    } catch (Exception $e) 
    {
        echo "<script>alert('Message could not be sent');</script>";
        
    }

      // $check="SELECT * FROM `ptrial` WHERE email='$email';";
      // $rec=mysqli_query($con,$check);

      // if($r=mysqli_num_rows($rec)>0)
      // {
      //   echo "<script>alert('Player Already Approval');</script>";
      //   echo "<script>window.location.href='registerplayer.php';</script>";
      // }
      // else
      // {
      //         $sq = "INSERT INTO `ptrial` ( `id` ,`first_name`, `last_name`, `mobile`, `gender`, `dob`, `age`, `email`, `country`, `level`, `batting`, `wk`, `bowling_arm`, `bowling_pace`, `first_pref`, `captain_exp`, `photo`) VALUES ('$id','$fname1', '$lname', '$mobile', '$gn', '$dob', '$age', '$email', '$country', '$level', '$bat', '$wk', '$bow_arm', '$pace', '$pf', '$cap', '$photo');";
      //         $qr = mysqli_query($con, $sq);
      //         if ($qr) {
      //             echo "<script>alert('Approval Sucessfully');</script>";
      //             echo "<script>window.location.href='registerplayer.php';</script>";
      //         }
      // }
?>