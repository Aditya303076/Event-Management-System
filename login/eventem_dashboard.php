<?php
    session_start();
    require 'eventem_db.php';

    if (!isset($_SESSION["state"]) || $_SESSION["state"] == 0) {
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event_mgt";

    // Handle CSV Export
    if (isset($_GET['export_csv']) && is_numeric($_GET['export_csv'])) {
        $export_id = intval($_GET['export_csv']);
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed");
        }

        // Verify that the event belongs to this organizer
        $verSql = "SELECT organizer_id FROM tbl_public_events WHERE id = ?";
        $verStmt = $conn->prepare($verSql);
        $verStmt->bind_param("i", $export_id);
        $verStmt->execute();
        $verRes = $verStmt->get_result()->fetch_assoc();
        $verStmt->close();

        if ($verRes && $verRes['organizer_id'] == $_SESSION['uId']) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Eventem_Guests_' . $export_id . '.csv');
            $output = fopen('php://output', 'w');
            
            fputcsv($output, array('Name', 'Email', 'Phone', 'Delivery Channel', 'Registered On', 'Check-In Status'));
            
            $sql = "SELECT attendee_name, attendee_email, attendee_phone, delivery_method, registration_date, checkin_status 
                    FROM tbl_public_registrations WHERE event_id = ? ORDER BY registration_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $export_id);
            $stmt->execute();
            $res = $stmt->get_result();
            
            while ($row = $res->fetch_assoc()) {
                fputcsv($output, $row);
            }
            fclose($output);
            $stmt->close();
            $conn->close();
            exit();
        }
        $conn->close();
    }

    // AJAX Check-In Handler
    if (isset($_GET['checkin_reg_id']) && isset($_GET['status'])) {
        $reg_id = intval($_GET['checkin_reg_id']);
        $new_status = $_GET['status']; // 'Arrived' or 'Pending'

        if ($new_status == 'Arrived' || $new_status == 'Pending') {
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                echo "Error";
                exit();
            }

            $upSql = "UPDATE tbl_public_registrations SET checkin_status = ? WHERE id = ?";
            $upStmt = $conn->prepare($upSql);
            $upStmt->bind_param("si", $new_status, $reg_id);
            if ($upStmt->execute()) {
                echo "Success";
            } else {
                echo "Error";
            }
            $upStmt->close();
            $conn->close();
        }
        exit();
    }

    // Fetch organizer's public events
    $conn = new mysqli($servername, $username, $password, $dbname);
    $orgSql = "SELECT * FROM tbl_public_events WHERE organizer_id = ? ORDER BY event_date DESC";
    $orgStmt = $conn->prepare($orgSql);
    $orgStmt->bind_param("i", $_SESSION['uId']);
    $orgStmt->execute();
    $eventsResult = $orgStmt->get_result();
    
    $my_events = [];
    while ($row = $eventsResult->fetch_assoc()) {
        $my_events[] = $row;
    }
    $orgStmt->close();

    // Select current active event
    $selected_event_id = null;
    if (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
        $selected_event_id = intval($_GET['event_id']);
    } elseif (!empty($my_events)) {
        $selected_event_id = $my_events[0]['id'];
    }

    // Load active event details & registrations
    $event_details = null;
    $registrations = [];
    $stats = ['total' => 0, 'arrived' => 0, 'rate' => 0];

    if ($selected_event_id) {
        // Fetch details
        $detSql = "SELECT * FROM tbl_public_events WHERE id = ? AND organizer_id = ?";
        $detStmt = $conn->prepare($detSql);
        $detStmt->bind_param("ii", $selected_event_id, $_SESSION['uId']);
        $detStmt->execute();
        $event_details = $detStmt->get_result()->fetch_assoc();
        $detStmt->close();

        if ($event_details) {
            // Fetch registrations
            $regSql = "SELECT * FROM tbl_public_registrations WHERE event_id = ? ORDER BY registration_date DESC";
            $regStmt = $conn->prepare($regSql);
            $regStmt->bind_param("i", $selected_event_id);
            $regStmt->execute();
            $regRes = $regStmt->get_result();
            while ($r = $regRes->fetch_assoc()) {
                $registrations[] = $r;
                $stats['total']++;
                if ($r['checkin_status'] == 'Arrived') {
                    $stats['arrived']++;
                }
            }
            $regStmt->close();

            if ($stats['total'] > 0) {
                $stats['rate'] = round(($stats['arrived'] / $stats['total']) * 100);
            }
        }
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventem Organizer Panel</title>
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
            padding-top: 10rem;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10000;
            background: rgba(10, 15, 30, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2.5px solid rgba(3, 233, 244, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
        }

        .header .logo {
            font-size: 2.8rem;
            font-weight: 700;
            color: #fff;
        }

        .header .logo span {
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
        }

        /* Layout */
        .dashboard-layout {
            display: flex;
            flex-wrap: wrap;
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 3%;
            gap: 3rem;
        }

        .sidebar-events {
            flex: 1 1 300px;
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 1.2rem;
            padding: 2.5rem;
            align-self: flex-start;
        }

        .main-stats-feed {
            flex: 2 1 800px;
            display: flex;
            flex-direction: column;
            gap: 3rem;
        }

        .section-title {
            font-size: 1.8rem;
            color: var(--main-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.8rem;
            margin-bottom: 2rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        /* Event selector buttons */
        .my-event-btn {
            width: 100%;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 0.8rem;
            padding: 1.5rem;
            color: #cbd5e1;
            text-align: left;
            cursor: pointer;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .my-event-btn:hover, .my-event-btn.active {
            border-color: var(--main-color);
            background: var(--hover-bg);
            color: var(--main-color);
        }

        .my-event-btn h4 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .my-event-btn p {
            font-size: 1.2rem;
            color: #94a3b8;
        }

        /* KPI widgets */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .kpi-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1.5px solid var(--border-color);
            border-radius: 1rem;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .kpi-card i {
            font-size: 2.8rem;
            color: var(--main-color);
            background: rgba(3, 233, 244, 0.1);
            padding: 1.2rem;
            border-radius: 0.6rem;
        }

        .kpi-info {
            display: flex;
            flex-direction: column;
        }

        .kpi-info h3 {
            font-size: 2.2rem;
            color: #fff;
        }

        .kpi-info span {
            font-size: 1.2rem;
            color: #94a3b8;
            text-transform: uppercase;
        }

        /* Scanner check-in & filters toolbar */
        .scan-toolbar {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 1.2rem;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .scan-row {
            display: flex;
            gap: 1.5rem;
            position: relative;
        }

        .scan-row i {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.8rem;
        }

        .scan-input {
            flex: 1;
            background: rgba(11, 15, 25, 0.7);
            border: 1px solid var(--border-color);
            color: #fff;
            font-size: 1.5rem;
            padding: 1.2rem 1.5rem 1.2rem 4.5rem;
            border-radius: 0.8rem;
        }

        .export-btn {
            background: #22c55e;
            color: #0b0f19;
            padding: 1rem 2rem;
            border-radius: 0.8rem;
            font-size: 1.4rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            cursor: pointer;
        }

        .export-btn:hover {
            background: #4ade80;
        }

        /* Registry table */
        .registry-table-wrapper {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            overflow: hidden;
            border: 1.5px solid var(--border-color);
        }

        .registry-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(30, 41, 59, 0.7);
        }

        .registry-table th {
            background: #0f172a;
            color: #03e9f4;
            font-size: 1.4rem;
            padding: 1.5rem;
            text-align: left;
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
        }

        .registry-table td {
            font-size: 1.4rem;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #e2e8f0;
        }

        .registry-table tr:hover {
            background: rgba(3, 233, 244, 0.05);
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 5rem;
            font-size: 1.15rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-arrived {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid #22c55e;
        }

        .badge-pending {
            background: rgba(234, 179, 8, 0.15);
            color: #facc15;
            border: 1px solid #eab308;
        }

        .btn-checkin {
            background: var(--main-color);
            color: #0b0f19;
            padding: 0.5rem 1rem;
            border-radius: 0.4rem;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-checkin:hover {
            background: #fff;
        }

        @media(max-width: 900px) {
            .dashboard-layout {
                flex-direction: column;
            }
            .sidebar-events {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <a href="eventem.php" class="logo"><i class="fa fa-slack"></i> EVENT<span>em</span> Organizer</a>
        <div class="nav-actions">
            <a href="eventem.php" class="nav-btn"><i class="fa fa-slack"></i> Discovery Hub</a>
            <a href="user.php" class="nav-btn"><i class="fa fa-home"></i> Home Panel</a>
        </div>
    </header>

    <div class="dashboard-layout">
        <!-- Sidebar my events -->
        <aside class="sidebar-events">
            <h3 class="section-title"><i class="fa fa-calendar-check-o"></i> My Public Events</h3>
            <?php if (!empty($my_events)): ?>
                <?php foreach ($my_events as $me): ?>
                    <button class="my-event-btn <?php echo ($selected_event_id == $me['id']) ? 'active' : ''; ?>" 
                            onclick="window.location.href='eventem_dashboard.php?event_id=<?php echo $me['id']; ?>'">
                        <h4><?php echo htmlspecialchars($me['event_name']); ?></h4>
                        <p><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($me['district']); ?> | <?php echo date("d M, Y", strtotime($me['event_date'])); ?></p>
                    </button>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #94a3b8; font-size: 1.4rem;">You haven't listed any public events on Eventem yet.</p>
                <a href="eventem_create.php" class="nav-btn" style="margin-top: 1.5rem; display: block; text-align: center;">Publish Event Now</a>
            <?php endif; ?>
        </aside>

        <!-- Main stats feed panel -->
        <main class="main-stats-feed">
            <?php if ($event_details): ?>
                <h2 style="font-size: 2.4rem; color: #fff;"><?php echo htmlspecialchars($event_details['event_name']); ?></h2>
                
                <!-- KPI Statistics -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <i class="fa fa-users"></i>
                        <div class="kpi-info">
                            <h3><?php echo number_format($stats['total']); ?></h3>
                            <span>Registered Guests</span>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <i class="fa fa-check-square-o" style="color: #4ade80; background: rgba(34, 197, 94, 0.1);"></i>
                        <div class="kpi-info">
                            <h3><?php echo number_format($stats['arrived']); ?></h3>
                            <span>Checked In Pass</span>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <i class="fa fa-line-chart" style="color: #60a5fa; background: rgba(59, 130, 246, 0.1);"></i>
                        <div class="kpi-info">
                            <h3><?php echo $stats['rate']; ?>%</h3>
                            <span>Arrival check rate</span>
                        </div>
                    </div>
                </div>

                <!-- On-site scanner check-in toolbar -->
                <div class="scan-toolbar">
                    <h3 class="section-title" style="margin-bottom: 0.5rem;"><i class="fa fa-qrcode"></i> InviteDesk On-Site Check-In Scanner</h3>
                    <div class="scan-row">
                        <i class="fa fa-search"></i>
                        <input type="text" id="scan-input" class="scan-input" placeholder="Search ticket pass ID, guest name, or email..." onkeyup="filterRegistry()">
                        <a href="eventem_dashboard.php?export_csv=<?php echo $selected_event_id; ?>" class="export-btn">
                            <i class="fa fa-file-excel-o"></i> Export CSV
                        </a>
                    </div>
                </div>

                <!-- Registrations registry table -->
                <div class="registry-table-wrapper">
                    <table class="registry-table">
                        <thead>
                            <tr>
                                <th>Guest Name</th>
                                <th>Email Address</th>
                                <th>Phone Number</th>
                                <th>Delivery Option</th>
                                <th>Check-In ID</th>
                                <th>Status</th>
                                <th>Check-In Action</th>
                            </tr>
                        </thead>
                        <tbody id="registry-table-body">
                            <?php if (!empty($registrations)): ?>
                                <?php foreach ($registrations as $reg): ?>
                                    <?php
                                        $badgeClass = ($reg['checkin_status'] == 'Arrived') ? 'badge-arrived' : 'badge-pending';
                                        $ticketCode = "E-REG-" . str_pad($reg['id'], 6, '0', STR_PAD_LEFT);
                                        $name = htmlspecialchars($reg['attendee_name']);
                                        $email = htmlspecialchars($reg['attendee_email']);
                                        $phone = htmlspecialchars($reg['attendee_phone']);
                                        $delivery = htmlspecialchars($reg['delivery_method']);
                                        $status = htmlspecialchars($reg['checkin_status']);
                                    ?>
                                    <tr data-name="<?php echo strtolower($name); ?>" 
                                        data-email="<?php echo strtolower($email); ?>" 
                                        data-code="<?php echo strtolower($ticketCode); ?>">
                                        
                                        <td style="font-weight: bold;"><?php echo $name; ?></td>
                                        <td><?php echo $email; ?></td>
                                        <td>+91 <?php echo $phone; ?></td>
                                        <td>
                                            <?php if ($delivery == 'WhatsApp'): ?>
                                                <span style="color: #4ade80;"><i class="fa fa-whatsapp"></i> WhatsApp</span>
                                            <?php else: ?>
                                                <span style="color: #03e9f4;"><i class="fa fa-envelope-o"></i> Email</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-family: monospace; font-weight: bold; color: #03e9f4;"><?php echo $ticketCode; ?></td>
                                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span></td>
                                        
                                        <td>
                                            <?php if ($status == 'Pending'): ?>
                                                <button class="btn-checkin" onclick="performCheckIn(<?php echo $reg['id']; ?>, 'Arrived')"><i class="fa fa-check"></i> Arrive</button>
                                            <?php else: ?>
                                                <button class="btn-checkin" style="background: #3b82f6; color: #fff;" onclick="performCheckIn(<?php echo $reg['id']; ?>, 'Pending')"><i class="fa fa-refresh"></i> Reset</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">No attendees registered for this event yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 5rem 0;">
                    <i class="fa fa-calendar" style="font-size: 5rem; color: #475569; margin-bottom: 2rem;"></i>
                    <p style="font-size: 1.8rem; color: #94a3b8;">Choose or create a public event to manage check-ins and attendee lists.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // AJAX Check-in toggle triggers
        function performCheckIn(regId, newStatus) {
            const xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText.trim() === "Success") {
                        // Reload page/grid instantly to reflect stats calculations
                        location.reload();
                    } else {
                        alert("Check-in adjustment error. Please try again.");
                    }
                }
            };
            xhttp.open("GET", "eventem_dashboard.php?checkin_reg_id=" + regId + "&status=" + newStatus, true);
            xhttp.send();
        }

        // Live text filter for guest lists
        function filterRegistry() {
            const searchVal = document.getElementById('scan-input').value.toLowerCase();
            const rows = document.querySelectorAll('#registry-table-body tr');

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const code = row.getAttribute('data-code');

                if (name && email && code) {
                    const matches = name.includes(searchVal) || email.includes(searchVal) || code.includes(searchVal);
                    if (matches) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    </script>
</body>
</html>
