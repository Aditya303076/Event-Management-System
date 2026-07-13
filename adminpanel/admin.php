<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>EMS</title>
        <!-- custom css file link -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->
		<link rel="stylesheet" href="admin.css">
        

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

    <body>
        <!--header section starts-->
	<header class="header">
        <a href="#" class="main"><span>EVENT</span>management</a>

        <nav class="navbar" style="display: flex; align-items: center;">
            <div style="margin-bottom: 10px; display: flex;">
                <a href="#homeSection" id="homeLink" class="nav-link">Home</a>
                <a href="adminBooking.php" id="serviceLink" class="nav-link">Booking</a>
                <a href="../login/login.php" id="aboutusLink" class="nav-link">Log Out</a>
                
        </nav>
        <div id="menu-bars" class=""></div>
    </header>
    <section class="SERVICE" id="serviceSection">
        <section class="container">
            <section class="card__container">
         
                <div class="card__bx" style="--clr: #5b98eb">
                    <div class="card__data">
                        <div class="card__icon">
                            <i class="fa fa-fort-awesome" style="font-size: 5rem;"></i>
                        </div>
                        <div class="card__content">
                            <h3>Venue</h3>
                            <p>it is a part that  you  canmake afforts and book events. </p>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                </div>
         
                <div class="card__bx" style="--clr: #5b98eb">
                    <div class="card__data">
                        <div class="card__icon">
                            <i class="fa fa-camera" style="font-size: 5rem;"></i>
                        </div>
                        <div class="card__content">
                            <h3>Photography</h3>
                            <p>it is a part that  you  canmake afforts and book events.</p>
                            <a href="" id="galleryser">Read More</a>
                        </div>
                    </div>
                </div>
         
                <div class="card__bx" style="--clr: #5b98eb">
                    <div class="card__data">
                        <div class="card__icon">
                            <i class="fa fa-cutlery" style="font-size: 5rem;"></i>
                        </div>
                        <div class="card__content">
                            <h3>Food and Drinks</h3>
                            <p>it is a part that  you  canmake afforts and book events.</p>
                            <a href="#">Read More</a>
                        </div>
                    </div>
                </div>
          
            </section>
        </section>  
        </section>

  </body>
</html>