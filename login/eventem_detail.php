<?php
    session_start();
    require 'eventem_db.php';

    if (!isset($_SESSION["state"]) || $_SESSION["state"] == 0) {
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo '<h3>Invalid Event ID</h3>';
        exit();
    }

    $event_id = intval($_GET['id']);
    $servername = getenv('DB_HOST') ?: "localhost";
    $username = getenv('DB_USER') ?: "root";
    $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
    $dbname = getenv('DB_NAME') ?: "event_mgt";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve event details along with organizer profile
    $sql = "SELECT e.*, u.First_Name, u.Last_Name, u.Email_ID, u.MobileNo 
            FROM tbl_public_events e 
            JOIN tbl_user u ON e.organizer_id = u.ID 
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '<h3>Event not found</h3>';
        $stmt->close();
        $conn->close();
        exit();
    }

    $event = $result->fetch_assoc();
    $stmt->close();

    // Parse Agenda Timeline
    $agenda_sessions = [];
    if (!empty($event['agenda'])) {
        $agenda_sessions = explode(" | ", $event['agenda']);
    }

    // Handle RSVP / Registration submit
    if (isset($_POST['btn_register'])) {
        $name = $_POST['reg_name'];
        $email = $_POST['reg_email'];
        $phone = $_POST['reg_phone'];
        $delivery = $_POST['delivery_method'];

        $insSql = "INSERT INTO tbl_public_registrations (event_id, attendee_name, attendee_email, attendee_phone, delivery_method, checkin_status) 
                   VALUES (?, ?, ?, ?, ?, 'Pending')";
        $insStmt = $conn->prepare($insSql);
        $insStmt->bind_param("issss", $event_id, $name, $email, $phone, $delivery);
        
        if ($insStmt->execute()) {
            $reg_id = $conn->insert_id;
            $insStmt->close();
            $conn->close();
            
            // Redirect to ticket view
            echo "<script>
            alert('Registration Successful! Generating your ticket...');
            window.location.href = 'eventem_ticket.php?reg_id=" . $reg_id . "';
            </script>";
            exit();
        } else {
            echo '<script>alert("Registration failed. Please try again.");</script>';
        }
        $insStmt->close();
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['event_name']); ?> - Eventem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');

        :root {
            --main-color: #03e9f4;
            --bg-dark: #0b0f19;
            --bg-card: rgba(22, 30, 49, 0.7);
            --border-color: rgba(3, 233, 244, 0.2);
            --hover-bg: rgba(3, 233, 244, 0.08);
        }

        * {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            outline: none;
            border: none;
            text-decoration: none;
        }

        body {
            background-color: var(--bg-dark);
            background-image: radial-gradient(at 100% 0%, rgba(3, 233, 244, 0.08) 0, transparent 40%),
                              radial-gradient(at 0% 100%, rgba(15, 23, 42, 0.95) 0, transparent 50%);
            min-height: 100vh;
            color: #fff;
        }

        /* Header Style */
        .eventem-header {
            background: rgba(13, 20, 35, 0.85);
            backdrop-filter: blur(12px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            border-bottom: 1.5px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .eventem-header .logo {
            font-size: 2.8rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .eventem-header .logo span {
            color: var(--main-color);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-btn {
            font-size: 1.4rem;
            color: #fff;
            border: 1px solid var(--border-color);
            padding: 0.8rem 1.6rem;
            border-radius: 0.6rem;
            background: transparent;
            font-weight: 600;
        }

        .nav-btn:hover {
            background: var(--main-color);
            color: #0b0f19;
            box-shadow: 0 0 12px rgba(3, 233, 244, 0.3);
        }

        /* Detail Container */
        .detail-container {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 3%;
            gap: 3rem;
        }

        .main-column {
            flex: 1 1 700px;
        }

        .sidebar-column {
            flex: 1 1 380px;
            position: sticky;
            top: 10rem;
            align-self: flex-start;
        }

        /* Cards styling */
        .details-card, .rsvp-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1.5px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 3.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .category-badge {
            background: rgba(3, 233, 244, 0.15);
            border: 1px solid var(--main-color);
            color: var(--main-color);
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .event-title {
            font-size: 3.2rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 2rem;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
            background: rgba(15, 23, 42, 0.4);
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid rgba(255,255,255,0.03);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .meta-item i {
            font-size: 2.2rem;
            color: var(--main-color);
            background: rgba(3, 233, 244, 0.1);
            padding: 1rem;
            border-radius: 0.6rem;
        }

        .meta-info h5 {
            font-size: 1.2rem;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .meta-info p {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .section-header {
            font-size: 2rem;
            color: var(--main-color);
            margin: 3rem 0 1.5rem 0;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .desc-text {
            font-size: 1.55rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        /* Timeline Agenda */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            margin-top: 2rem;
            position: relative;
            padding-left: 2rem;
            border-left: 2px solid rgba(3, 233, 244, 0.2);
        }

        .timeline-item {
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.7rem;
            top: 0.4rem;
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 50%;
            background: var(--main-color);
            border: 3px solid var(--bg-dark);
            box-shadow: 0 0 8px var(--main-color);
        }

        .timeline-time {
            font-size: 1.3rem;
            color: var(--main-color);
            font-weight: bold;
            text-transform: uppercase;
        }

        .timeline-title {
            font-size: 1.55rem;
            color: #f1f5f9;
            margin-top: 0.3rem;
        }

        /* RSVP / Booking form */
        .rsvp-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }

        .rsvp-price-row span {
            font-size: 1.6rem;
            color: #94a3b8;
        }

        .rsvp-price-row h3 {
            font-size: 2.4rem;
            color: var(--main-color);
            font-weight: bold;
        }

        .inputBox {
            margin-bottom: 1.8rem;
        }

        .inputBox span {
            font-size: 1.4rem;
            color: #94a3b8;
            display: block;
            margin-bottom: 0.8rem;
        }

        .inputBox input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 0.8rem;
            padding: 1.2rem;
            font-size: 1.5rem;
            color: #fff;
            transition: all 0.3s;
        }

        .inputBox input:focus {
            border-color: var(--main-color);
            box-shadow: 0 0 8px rgba(3, 233, 244, 0.3);
        }

        /* Radio option checkbox cards (WhatsApp / Email Wowsly styling) */
        .delivery-selection {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .delivery-card {
            flex: 1;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 0.8rem;
            padding: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.3rem;
            user-select: none;
        }

        .delivery-card:hover {
            border-color: var(--main-color);
            background: var(--hover-bg);
        }

        .delivery-card input {
            accent-color: var(--main-color);
            width: 1.8rem;
            height: 1.8rem;
        }

        .delivery-card.selected {
            border-color: var(--main-color);
            background: rgba(3, 233, 244, 0.05);
            color: var(--main-color);
            font-weight: bold;
        }

        .submit-btn {
            width: 100%;
            background: var(--main-color);
            color: #0b0f19;
            font-size: 1.8rem;
            font-weight: bold;
            padding: 1.4rem;
            border-radius: 0.8rem;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background: #fff;
            box-shadow: 0 0 15px rgba(3, 233, 244, 0.4);
            transform: translateY(-2px);
        }

        .wowsly-info {
            background: rgba(34, 197, 94, 0.05);
            border: 1px dashed rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 1.2rem;
            border-radius: 0.8rem;
            font-size: 1.2rem;
            margin-top: 1.5rem;
            line-height: 1.5;
            display: flex;
            gap: 0.8rem;
            align-items: flex-start;
        }

        .wowsly-info i {
            font-size: 1.6rem;
            margin-top: 0.2rem;
        }

        @media(max-width: 768px) {
            .detail-container {
                flex-direction: column;
            }
            .sidebar-column {
                position: static;
            }
        }
    </style>
</head>
<body>

    <header class="eventem-header">
        <a href="eventem.php" class="logo"><i class="fa fa-slack"></i> <span>EVENT</span>em</a>
        <div class="nav-actions">
            <a href="eventem.php" class="nav-btn"><i class="fa fa-arrow-left"></i> Back to Feed</a>
        </div>
    </header>

    <div class="detail-container">
        <!-- Main details columns -->
        <main class="main-column">
            <div class="details-card">
                <span class="category-badge"><?php echo htmlspecialchars($event['category']); ?></span>
                <h1 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h1>

                <div class="meta-grid">
                    <div class="meta-item">
                        <i class="fa fa-calendar"></i>
                        <div class="meta-info">
                            <h5>Date & Time</h5>
                            <p><?php echo date("d M, Y", strtotime($event['event_date'])); ?></p>
                            <span style="font-size: 1.2rem; color: #94a3b8;"><?php echo date("h:i A", strtotime($event['event_time'])); ?></span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-map-marker"></i>
                        <div class="meta-info">
                            <h5>Venue Location</h5>
                            <p style="text-transform: capitalize;"><?php echo htmlspecialchars($event['venue']); ?></p>
                            <span style="font-size: 1.2rem; color: #94a3b8;"><?php echo htmlspecialchars($event['district']); ?> District</span>
                        </div>
                    </div>
                </div>

                <h3 class="section-header"><i class="fa fa-align-left"></i> Event Description</h3>
                <p class="desc-text"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

                <!-- Agenda timeline shows only if large event provider configured it -->
                <?php if (!empty($agenda_sessions)): ?>
                    <h3 class="section-header"><i class="fa fa-clock-o"></i> Hourly Timeline Agenda</h3>
                    <div class="timeline">
                        <?php foreach ($agenda_sessions as $session): ?>
                            <?php
                                // Split "Time - Title"
                                $parts = explode(" - ", $session, 2);
                                $time = isset($parts[0]) ? trim($parts[0]) : '';
                                $title = isset($parts[1]) ? trim($parts[1]) : '';
                            ?>
                            <div class="timeline-item">
                                <span class="timeline-time"><?php echo htmlspecialchars($time); ?></span>
                                <p class="timeline-title"><?php echo htmlspecialchars($title); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Sidebar registration form columns -->
        <aside class="sidebar-column">
            <div class="rsvp-card">
                <div class="rsvp-price-row">
                    <span>Ticket Fee:</span>
                    <h3>
                        <?php echo ($event['price'] == 0) ? 'FREE / RSVP' : 'Rs. ' . number_format($event['price']); ?>
                    </h3>
                </div>

                <h3 style="font-size: 1.8rem; font-weight: bold; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem;">
                    <i class="fa fa-ticket"></i> Get Event Tickets
                </h3>

                <form action="" method="post" onsubmit="return validateForm()">
                    <div class="inputBox">
                        <span>Attendee Name :</span>
                        <input type="text" name="reg_name" placeholder="enter your full name" required>
                    </div>

                    <div class="inputBox">
                        <span>Attendee Email :</span>
                        <input type="email" name="reg_email" placeholder="example@gmail.com" required>
                    </div>

                    <div class="inputBox">
                        <span>WhatsApp Number :</span>
                        <input type="tel" name="reg_phone" placeholder="e.g. 9876543210" minlength="10" maxlength="12" required>
                    </div>

                    <span>Select Ticket Delivery:</span>
                    <div class="delivery-selection">
                        <label class="delivery-card selected" id="dev-email" onclick="selectDelivery('Email')">
                            <input type="radio" name="delivery_method" value="Email" checked style="display: none;">
                            <i class="fa fa-envelope" style="color: #03e9f4;"></i>
                            <span>Email PDF</span>
                        </label>
                        <label class="delivery-card" id="dev-wa" onclick="selectDelivery('WhatsApp')">
                            <input type="radio" name="delivery_method" value="WhatsApp" style="display: none;">
                            <i class="fa fa-whatsapp" style="color: #4ade80;"></i>
                            <span>WhatsApp (Wowsly)</span>
                        </label>
                    </div>

                    <button type="submit" name="btn_register" class="submit-btn">RSVP / Book Ticket</button>
                </form>

                <div class="wowsly-info" id="wowsly-badge" style="display: none;">
                    <i class="fa fa-whatsapp"></i>
                    <span><strong>Wowsly Enabled:</strong> Choosing WhatsApp delivery will dynamically push your ticket link and receipt confirmation directly to your phone.</span>
                </div>
            </div>
        </aside>
    </div>

    <script>
        function selectDelivery(method) {
            document.getElementById('dev-email').classList.remove('selected');
            document.getElementById('dev-wa').classList.remove('selected');

            if (method === 'Email') {
                document.getElementById('dev-email').classList.add('selected');
                document.getElementById('wowsly-badge').style.display = 'none';
            } else {
                document.getElementById('dev-wa').classList.add('selected');
                document.getElementById('wowsly-badge').style.display = 'flex';
            }
        }

        function validateForm() {
            // Verify mobile format
            const phone = document.forms[0]["reg_phone"].value;
            if(phone.length < 10) {
                alert("Please enter a valid 10-digit mobile number.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
