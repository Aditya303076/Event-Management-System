<?php
    session_start();
    require 'eventem_db.php';

    if (!isset($_SESSION["state"]) || $_SESSION["state"] == 0) {
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    }

    if (!isset($_GET['reg_id']) || !is_numeric($_GET['reg_id'])) {
        echo '<h3>Invalid Ticket ID</h3>';
        exit();
    }

    $reg_id = intval($_GET['reg_id']);
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event_mgt";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve registration details joined with event details
    $sql = "SELECT r.*, e.event_name, e.venue, e.event_date, e.event_time, e.price, e.district 
            FROM tbl_public_registrations r 
            JOIN tbl_public_events e ON r.event_id = e.id 
            WHERE r.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reg_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '<h3>Ticket badge not found.</h3>';
        $stmt->close();
        $conn->close();
        exit();
    }

    $ticket = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventem Ticket - #<?php echo $ticket['id']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0b0f19;
            background-image: radial-gradient(at 0% 0%, rgba(3, 233, 244, 0.12) 0, transparent 40%);
            color: #fff;
            margin: 0;
            padding: 3rem 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .ticket-wrapper {
            background: rgba(22, 30, 49, 0.75);
            backdrop-filter: blur(20px);
            max-width: 650px;
            width: 100%;
            border-radius: 1.5rem;
            border: 1.5px solid rgba(3, 233, 244, 0.35);
            padding: 3.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .ticket-wrapper::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(3, 233, 244, 0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid rgba(3, 233, 244, 0.2);
            padding-bottom: 1.5rem;
        }

        .btn {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .btn-print {
            background: #03e9f4;
            color: #0b0f19;
            padding: 0.8rem 1.6rem;
            border-radius: 0.6rem;
            font-size: 1.35rem;
            font-weight: bold;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .btn-print:hover {
            background: #fff;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.4);
        }

        /* Wowsly Badge Stamp */
        .wowsly-badge {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid #22c55e;
            color: #4ade80;
            padding: 0.6rem 1.2rem;
            border-radius: 5rem;
            font-size: 1.2rem;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
            padding-bottom: 2rem;
        }

        .brand h2 {
            font-size: 2.6rem;
            margin: 0;
        }

        .brand h2 span {
            color: #03e9f4;
        }

        .brand p {
            font-size: 1.2rem;
            color: #94a3b8;
            margin-top: 0.3rem;
        }

        .ticket-code {
            text-align: right;
        }

        .ticket-code h4 {
            font-size: 1.3rem;
            color: #94a3b8;
            text-transform: uppercase;
            margin: 0;
        }

        .ticket-code p {
            font-size: 1.8rem;
            color: #03e9f4;
            font-weight: bold;
            margin: 0.3rem 0 0 0;
        }

        .ticket-body {
            display: flex;
            flex-wrap: wrap;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .ticket-info {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-field h5 {
            font-size: 1.2rem;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .info-field p {
            font-size: 1.6rem;
            color: #fff;
            font-weight: 600;
        }

        /* Dynamic SVG QR mockup */
        .qr-column {
            flex: 0 0 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr-mockup {
            background: #fff;
            padding: 1rem;
            border-radius: 0.8rem;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }

        .qr-scanner-text {
            font-size: 1.1rem;
            color: #94a3b8;
            margin-top: 0.8rem;
            text-align: center;
        }

        .ticket-footer {
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 2rem;
            text-align: center;
            color: #64748b;
            font-size: 1.2rem;
            line-height: 1.5;
        }

        /* Print formatting */
        @media print {
            body {
                background: #white;
                color: #000;
                padding: 0;
            }
            .ticket-wrapper {
                background: #fff;
                border: 2px solid #000;
                box-shadow: none;
                color: #000;
                max-width: 100%;
                padding: 2rem;
            }
            .actions {
                display: none;
            }
            .brand h2, .ticket-code p, .info-field p {
                color: #000 !important;
            }
            .qr-mockup {
                border: 1px solid #000;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        <!-- Action Toolbar -->
        <div class="actions">
            <a href="eventem.php" class="btn" style="color: #94a3b8;"><i class="fa fa-arrow-left"></i> Back to Discovery</a>
            <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Print Badge Pass</button>
        </div>

        <!-- WhatsApp badge for Wowsly confirmations -->
        <?php if ($ticket['delivery_method'] == 'WhatsApp'): ?>
            <div class="wowsly-badge">
                <i class="fa fa-whatsapp"></i> Delivered via WhatsApp (Wowsly)
            </div>
        <?php endif; ?>

        <!-- Ticket Header -->
        <div class="ticket-header">
            <div class="brand">
                <h2>EVENT<span>em</span></h2>
                <p>Gujarat District Event Ticketing</p>
            </div>
            <div class="ticket-code">
                <h4>Ticket Pass ID</h4>
                <p>E-REG-<?php echo str_pad($ticket['id'], 6, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>

        <!-- Ticket Body -->
        <div class="ticket-body">
            <div class="ticket-info">
                <div class="info-field">
                    <h5>Event Name</h5>
                    <p style="color: #03e9f4;"><?php echo htmlspecialchars($ticket['event_name']); ?></p>
                </div>
                <div class="info-field">
                    <h5>Attendee Registry</h5>
                    <p><?php echo htmlspecialchars($ticket['attendee_name']); ?></p>
                    <span style="font-size: 1.2rem; color: #94a3b8;"><?php echo htmlspecialchars($ticket['attendee_email']); ?></span>
                </div>
                <div class="info-field">
                    <h5>Venue Coordinates</h5>
                    <p style="text-transform: capitalize;"><?php echo htmlspecialchars($ticket['venue']); ?> (<?php echo htmlspecialchars($ticket['district']); ?>)</p>
                </div>
                <div class="info-field">
                    <h5>Date & Schedule Time</h5>
                    <p><?php echo date("d M, Y", strtotime($ticket['event_date'])); ?> @ <?php echo date("h:i A", strtotime($ticket['event_time'])); ?></p>
                </div>
            </div>

            <!-- QR code vector mockup -->
            <div class="qr-column">
                <div class="qr-mockup">
                    <svg width="100" height="100" viewBox="0 0 100 100">
                        <!-- QR outline trackers -->
                        <rect x="0" y="0" width="30" height="30" fill="none" stroke="#111" stroke-width="6"/>
                        <rect x="7" y="7" width="16" height="16" fill="#111"/>
                        
                        <rect x="70" y="0" width="30" height="30" fill="none" stroke="#111" stroke-width="6"/>
                        <rect x="77" y="7" width="16" height="16" fill="#111"/>

                        <rect x="0" y="70" width="30" height="30" fill="none" stroke="#111" stroke-width="6"/>
                        <rect x="7" y="77" width="16" height="16" fill="#111"/>

                        <!-- Random QR matrix noise -->
                        <rect x="40" y="5" width="8" height="20" fill="#111"/>
                        <rect x="40" y="30" width="20" height="8" fill="#111"/>
                        <rect x="5" y="40" width="20" height="8" fill="#111"/>
                        <rect x="35" y="45" width="10" height="10" fill="#111"/>
                        <rect x="15" y="55" width="15" height="8" fill="#111"/>
                        <rect x="50" y="50" width="18" height="18" fill="#111"/>
                        <rect x="75" y="40" width="15" height="15" fill="#111"/>
                        <rect x="80" y="70" width="15" height="8" fill="#111"/>
                        <rect x="45" y="80" width="15" height="15" fill="#111"/>
                        <rect x="80" y="85" width="15" height="10" fill="#111"/>
                    </svg>
                </div>
                <div class="qr-scanner-text">On-Site Check-In Code</div>
            </div>
        </div>

        <!-- Ticket Footer -->
        <div class="ticket-footer">
            <p>Please present this digital ticket or printed badge to the security staff at the event entrance for scanning check-in.</p>
            <p style="margin-top: 1rem; color: #475569; font-size: 1.1rem;">Brought to you by Eventem. Powered by AllEvents & Wowsly Gujarat.</p>
        </div>
    </div>

</body>
</html>
