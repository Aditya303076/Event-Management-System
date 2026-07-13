<?php
    session_start();  
    if(!isset($_SESSION["state"]) || $_SESSION["state"] == 0) {
        echo '<script>
        setTimeout(function(){
            window.location.href = "login.php";
        })
        </script>';           
        exit();
    }  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Event Management</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../style.css" >
    
    <style>
        .header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(10, 15, 30, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid rgba(3, 233, 244, 0.25);
        }
        
        .bookings-container {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(10px);
            min-height: 100vh;
            padding: 8rem 5%;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3rem 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            border-radius: 1rem;
            overflow: hidden;
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(3, 233, 244, 0.25);
        }

        .booking-table th {
            background: #0f172a;
            color: #03e9f4 !important;
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1.5rem;
            font-weight: bold;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .booking-table td {
            font-size: 1.5rem;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #f1f5f9;
        }

        .booking-table tr:nth-child(even) {
            background: rgba(15, 23, 42, 0.4);
        }

        .booking-table tr:hover {
            background: rgba(3, 233, 244, 0.1);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 5rem;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-upcoming {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid #3b82f6;
        }

        .status-finished {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid #22c55e;
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid #ef4444;
        }

        .status-confirmed {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
            border: 1px solid #eab308;
        }

        .action-icon {
            font-size: 1.8rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .action-icon:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="user.php" class="main"><span>EVENT</span>management</a>
        <nav class="navbar" style="display: flex; align-items: center;">
            <div class="dropdown" style="float: right;">
                <a class="nav-link dropdown-toggle" style="margin-right: 15px; border:2px solid white; padding:5px; padding-left:10px; padding-right:10px; border-radius: 50%; cursor: pointer;">
                    <?php echo strtoupper(substr($_SESSION["firstname"], 0, 1));?>
                </a>
                <div class="dropdown-content">
                    <a href="profile.php"><i class="fa fa-id-badge" style="padding-right: 1rem;"></i>My Profile</a>
                    <a href="booking.php"><i class="fa fa-calendar" style="padding-right: 1rem;"></i>My Bookings</a>
                    <a href="login.php"><i class="fa fa-sign-out" style="padding-right: 1rem;"></i>Log out</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="bookings-container">
        <section class="contact" style="background: transparent; box-shadow: none; padding: 0;">
            <div class="content" style="border-bottom: 2px solid #03e9f4;">
                <h3>Your Event <span>Bookings</span></h3>
            </div>
            
            <div class="contactsection" style="margin-top: 2rem;">
            
            <?php
            $servername = getenv('DB_HOST') ?: "localhost";
            $username = getenv('DB_USER') ?: "root";
            $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
            $dbname = getenv('DB_NAME') ?: "event_mgt";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Automatically check and update states to 'Finished' if the event date has passed
            $checkdate = "SELECT * FROM tbl_booking WHERE b_uid = ?";
            $stmt = $conn->prepare($checkdate);
            $stmt->bind_param("i", $_SESSION["uId"]);
            $stmt->execute();
            $result1 = $stmt->get_result();

            $currdate = date('Y-m-d');
            while ($row = $result1->fetch_assoc()) {
                if (strtotime($currdate) > strtotime($row["b_date"]) && $row["b_state"] != "Cancelled" && $row["b_state"] != "Finished") {
                    $updateSql = "UPDATE tbl_booking SET b_state = 'Finished' WHERE b_id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("i", $row["b_id"]);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
            $result1->free();
            $stmt->close();
            
            // Retrieve bookings
            $sql = "SELECT * FROM tbl_booking WHERE b_uid = ? ORDER BY b_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION["uId"]);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<table class="booking-table">';
                echo '<tr>
                        <th>Date of Event</th>
                        <th>Venue</th>
                        <th>Event Type</th>
                        <th>Package</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Receipt Invoice</th>
                        <th>Cancel Event</th>
                    </tr>';
                
                while ($row = $result->fetch_assoc()) {
                    // Decide status badge styling class
                    $badgeClass = 'status-upcoming';
                    if ($row["b_state"] == 'Finished') {
                        $badgeClass = 'status-finished';
                    } elseif ($row["b_state"] == 'Cancelled') {
                        $badgeClass = 'status-cancelled';
                    } elseif ($row["b_state"] == 'Confirmed') {
                        $badgeClass = 'status-confirmed';
                    }

                    echo "<tr>";
                    echo "<td style='font-weight: bold;'>" . date("d M, Y", strtotime($row["b_date"])) . "</td>";
                    echo "<td style='text-transform: capitalize;'>" . htmlspecialchars($row["b_venue"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["b_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["b_package"]) . "</td>";
                    echo "<td style='font-weight: bold; color: #03e9f4;'>Rs. " . number_format($row["b_price"]) . "</td>";
                    echo "<td><span class='status-badge $badgeClass'>" . htmlspecialchars($row["b_state"]) . "</span></td>";
                    
                    // Printable Invoice link
                    echo "<td>";
                    echo "<a href='invoice.php?b_id=" . $row["b_id"] . "' target='_blank' style='font-size: 1.8rem; color: #03e9f4;' class='action-icon'><i class='fa fa-file-text-o'></i></a>";
                    echo "</td>";

                    // Cancel booking option
                    echo "<td>";
                    if ($row["b_state"] != "Cancelled" && $row["b_state"] != "Finished") {
                        echo "<i class='fa fa-trash-o action-icon' style='color:#f87171;' onclick='deleteBooking(" . $row["b_id"] . ")'></i>";
                    } else {
                        echo "<span style='color: #64748b;'>-</span>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div style='font-size: 2rem; color: #94a3b8; text-align: center; margin: 5rem 0;'>No bookings found. Head back to the dashboard to plan an event!</div>";
            }

            // AJAX booking cancellation receiver
            if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
                $deleteId = $_GET['delete_id'];
                $deleteSql = "UPDATE tbl_booking SET b_state = 'Cancelled' WHERE b_id = ? AND b_uid = ?";
                $delStmt = $conn->prepare($deleteSql);
                $delStmt->bind_param("ii", $deleteId, $_SESSION["uId"]);
                if ($delStmt->execute()) {
                    echo "Success";
                } else {
                    echo "Error";
                }
                $delStmt->close();
                $conn->close();
                exit();
            }

            $stmt->close();
            $conn->close();
            ?>

            </div>
        </section>
    </div>

    <script>
    function deleteBooking(bookingId) {
        var confirmDelete = confirm("Are you sure you want to cancel this booking?");
        if (confirmDelete) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    alert("Booking cancelled successfully.");
                    location.reload();
                }
            };
            xhttp.open("GET", "booking.php?delete_id=" + bookingId, true);
            xhttp.send();
        }
    }
    </script>
    
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>