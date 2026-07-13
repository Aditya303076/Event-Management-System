<?php
    session_start();
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event_mgt";

    // AJAX Receiver for inline status changes
    if (isset($_GET['update_id']) && isset($_GET['new_status'])) {
        $b_id = intval($_GET['update_id']);
        $new_status = $_GET['new_status'];
        
        $valid_statuses = ['Upcoming', 'Confirmed', 'Finished', 'Cancelled'];
        if (in_array($new_status, $valid_statuses)) {
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                echo "Connection failed";
                exit();
            }
            $updateSql = "UPDATE tbl_booking SET b_state = ? WHERE b_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $new_status, $b_id);
            if ($updateStmt->execute()) {
                echo "Success";
            } else {
                echo "Error";
            }
            $updateStmt->close();
            $conn->close();
        }
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS Admin Panel - Bookings</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="admin.css">

    <style>
        body {
            background: #0f172a !important;
            background-image: radial-gradient(at 0% 0%, rgba(3, 233, 244, 0.1) 0, transparent 50%) !important;
            color: #fff;
            padding-top: 10rem;
        }

        .header {
            background: rgba(10, 15, 30, 0.95);
            border-bottom: 2px solid rgba(3, 233, 244, 0.25);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10000;
        }

        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 2rem;
            margin: 2rem 5%;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(3, 233, 244, 0.2);
            border-radius: 1rem;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-card i {
            font-size: 3rem;
            color: #03e9f4;
            background: rgba(3, 233, 244, 0.1);
            padding: 1.5rem;
            border-radius: 0.8rem;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-info .value {
            font-size: 2.2rem;
            font-weight: bold;
            color: #fff;
        }

        .stat-info .label {
            font-size: 1.3rem;
            color: #94a3b8;
            text-transform: uppercase;
        }

        /* Filter Controls */
        .filters-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin: 3rem 5% 1rem 5%;
            gap: 1.5rem;
            background: rgba(30, 41, 59, 0.4);
            padding: 1.5rem 2rem;
            border-radius: 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .filter-group label {
            font-size: 1.5rem;
            color: #94a3b8;
        }

        .filter-input {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(3, 233, 244, 0.3);
            color: #fff;
            padding: 0.8rem 1.2rem;
            border-radius: 0.5rem;
            font-size: 1.4rem;
            outline: none;
            min-width: 200px;
        }

        .filter-input:focus {
            border-color: #03e9f4;
        }

        /* Booking Table Styles */
        .bookings-table-wrapper {
            margin: 0 5% 5rem 5%;
            overflow-x: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            border: 1px solid rgba(3, 233, 244, 0.25);
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(30, 41, 59, 0.7);
        }

        .booking-table th {
            background: #0f172a;
            color: #03e9f4;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(3, 233, 244, 0.25);
        }

        .booking-table td {
            font-size: 1.4rem;
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }

        .booking-table tr:hover {
            background: rgba(3, 233, 244, 0.05);
        }

        /* Badges */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 5rem;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-upcoming {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid #3b82f6;
        }

        .badge-confirmed {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
            border: 1px solid #eab308;
        }

        .badge-finished {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid #22c55e;
        }

        .badge-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid #ef4444;
        }

        /* Action Buttons */
        .btn-action {
            padding: 0.6rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            margin: 0.2rem;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-confirm {
            background: #eab308;
            color: #0f172a;
        }

        .btn-confirm:hover {
            background: #facc15;
        }

        .btn-finish {
            background: #22c55e;
            color: #0f172a;
        }

        .btn-finish:hover {
            background: #4ade80;
        }

        .btn-cancel {
            background: #ef4444;
            color: #fff;
        }

        .btn-cancel:hover {
            background: #f87171;
        }

        .view-invoice-btn {
            color: #03e9f4;
            font-size: 1.8rem;
            cursor: pointer;
        }

        .view-invoice-btn:hover {
            color: #fff;
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="admin.php" class="main"><span>EVENT</span>management</a>
        <nav class="navbar" style="display: flex; align-items: center;">
            <div style="margin-bottom: 10px; display: flex;">
                <a href="admin.php" id="homeLink" class="nav-link">Home</a>
                <a href="adminBooking.php" id="serviceLink" class="nav-link active">Booking</a>
                <a href="../login/login.php" id="aboutusLink" class="nav-link">Log Out</a>
            </div>
        </nav>
    </header>

    <?php
        // Fetch dashboard statistics
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $statSql = "SELECT 
                        COUNT(*) as total, 
                        SUM(CASE WHEN b_state='Upcoming' THEN 1 ELSE 0 END) as upcoming,
                        SUM(CASE WHEN b_state='Confirmed' THEN 1 ELSE 0 END) as confirmed,
                        SUM(CASE WHEN b_state='Finished' THEN 1 ELSE 0 END) as finished,
                        SUM(CASE WHEN b_state='Cancelled' THEN 1 ELSE 0 END) as cancelled,
                        SUM(CASE WHEN b_state != 'Cancelled' THEN b_price ELSE 0 END) as earnings
                    FROM tbl_booking";
        $statResult = $conn->query($statSql);
        $stats = $statResult->fetch_assoc();
    ?>

    <!-- Dashboard Statistics Widgets -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fa fa-calendar-check-o"></i>
            <div class="stat-info">
                <span class="value"><?php echo number_format($stats['total']); ?></span>
                <span class="label">Total Bookings</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa fa-clock-o" style="color: #60a5fa; background: rgba(59, 130, 246, 0.1);"></i>
            <div class="stat-info">
                <span class="value"><?php echo number_format($stats['upcoming']); ?></span>
                <span class="label">Upcoming</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa fa-check-circle-o" style="color: #facc15; background: rgba(234, 179, 8, 0.1);"></i>
            <div class="stat-info">
                <span class="value"><?php echo number_format($stats['confirmed']); ?></span>
                <span class="label">Confirmed</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa fa-flag-checkered" style="color: #4ade80; background: rgba(34, 197, 94, 0.1);"></i>
            <div class="stat-info">
                <span class="value"><?php echo number_format($stats['finished']); ?></span>
                <span class="label">Finished</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa fa-times-circle" style="color: #f87171; background: rgba(239, 68, 68, 0.1);"></i>
            <div class="stat-info">
                <span class="value"><?php echo number_format($stats['cancelled']); ?></span>
                <span class="label">Cancelled</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa fa-inr" style="color: #03e9f4; background: rgba(3, 233, 244, 0.1);"></i>
            <div class="stat-info">
                <span class="value">Rs. <?php echo number_format($stats['earnings']); ?></span>
                <span class="label">Total Revenue</span>
            </div>
        </div>
    </div>

    <!-- Live filter/search toolbar -->
    <div class="filters-container">
        <div class="filter-group">
            <label for="search-name"><i class="fa fa-search"></i> Search:</label>
            <input type="text" id="search-name" class="filter-input" placeholder="Search Client or Venue..." onkeyup="filterBookings()">
        </div>
        <div class="filter-group">
            <label for="filter-status"><i class="fa fa-filter"></i> Status:</label>
            <select id="filter-status" class="filter-input" onchange="filterBookings()">
                <option value="All">All Statuses</option>
                <option value="Upcoming">Upcoming</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Finished">Finished</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Booking Table -->
    <div class="bookings-table-wrapper">
        <table class="booking-table">
            <thead>
                <tr>
                    <th>sr.no</th>
                    <th>Client Name</th>
                    <th>Date of Event</th>
                    <th>Venue</th>
                    <th>Event Type</th>
                    <th>Package</th>
                    <th>Total Price</th>
                    <th>Invoice</th>
                    <th>Status</th>
                    <th>Manager Actions</th>
                </tr>
            </thead>
            <tbody id="bookings-table-body">
                <?php
                $sql = "SELECT b.*, u.First_Name, u.Last_Name 
                        FROM tbl_booking b
                        JOIN tbl_user u ON b.b_uid = u.id 
                        ORDER BY b.b_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        // Badge type
                        $badgeClass = 'badge-upcoming';
                        if ($row["b_state"] == 'Finished') {
                            $badgeClass = 'badge-finished';
                        } elseif ($row["b_state"] == 'Cancelled') {
                            $badgeClass = 'badge-cancelled';
                        } elseif ($row["b_state"] == 'Confirmed') {
                            $badgeClass = 'badge-confirmed';
                        }

                        $clientName = htmlspecialchars($row["First_Name"] . " " . $row["Last_Name"]);
                        $venue = htmlspecialchars($row["b_venue"]);
                        $status = htmlspecialchars($row["b_state"]);

                        echo "<tr data-name='$clientName' data-venue='$venue' data-status='$status'>";
                        echo "<td style='font-weight: bold; color: #94a3b8;'>" . $count . "</td>";
                        echo "<td style='font-weight: bold; text-align: left;'>" . $clientName . "</td>";
                        echo "<td>" . date("d M, Y", strtotime($row["b_date"])) . "</td>";
                        echo "<td style='text-transform: capitalize;'>" . $venue . "</td>";
                        echo "<td>" . htmlspecialchars($row["b_type"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["b_package"]) . "</td>";
                        echo "<td style='font-weight: bold; color: #03e9f4;'>Rs. " . number_format($row["b_price"]) . "</td>";
                        
                        // Invoice link
                        echo "<td>";
                        echo "<a href='../login/invoice.php?b_id=" . $row["b_id"] . "' target='_blank' class='view-invoice-btn'><i class='fa fa-file-text-o'></i></a>";
                        echo "</td>";

                        // Status badge
                        echo "<td><span class='badge $badgeClass'>" . $status . "</span></td>";

                        // Manager action options
                        echo "<td>";
                        if ($row["b_state"] == 'Upcoming') {
                            echo "<button class='btn-action btn-confirm' onclick='updateStatus(" . $row["b_id"] . ", \"Confirmed\")'><i class='fa fa-check'></i> Confirm</button>";
                            echo "<button class='btn-action btn-cancel' onclick='updateStatus(" . $row["b_id"] . ", \"Cancelled\")'><i class='fa fa-close'></i> Cancel</button>";
                        } elseif ($row["b_state"] == 'Confirmed') {
                            echo "<button class='btn-action btn-finish' onclick='updateStatus(" . $row["b_id"] . ", \"Finished\")'><i class='fa fa-flag'></i> Finish</button>";
                            echo "<button class='btn-action btn-cancel' onclick='updateStatus(" . $row["b_id"] . ", \"Cancelled\")'><i class='fa fa-close'></i> Cancel</button>";
                        } else {
                            echo "<span style='color: #64748b;'>-</span>";
                        }
                        echo "</td>";
                        echo "</tr>";

                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='10'>No event bookings recorded in the system.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Dynamic search and filter rows client-side
        function filterBookings() {
            const searchVal = document.getElementById('search-name').value.toLowerCase();
            const statusVal = document.getElementById('filter-status').value;
            const rows = document.querySelectorAll('#bookings-table-body tr');

            rows.forEach(row => {
                const name = row.getAttribute('data-name').toLowerCase();
                const venue = row.getAttribute('data-venue').toLowerCase();
                const status = row.getAttribute('data-status');

                const matchesSearch = name.includes(searchVal) || venue.includes(searchVal);
                const matchesStatus = (statusVal === 'All') || (status === statusVal);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Send AJAX request to update status without reload
        function updateStatus(bookingId, newStatus) {
            var confirmAction = confirm("Are you sure you want to change booking status to " + newStatus + "?");
            if (confirmAction) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText.trim() === "Success") {
                            alert("Booking status successfully updated.");
                            location.reload();
                        } else {
                            alert("Error updating status. Please try again.");
                        }
                    }
                };
                xhttp.open("GET", "adminBooking.php?update_id=" + bookingId + "&new_status=" + newStatus, true);
                xhttp.send();
            }
        }
    </script>
</body>
</html>
