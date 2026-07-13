<?php
    session_start();

    if (!isset($_SESSION["state"]) || $_SESSION["state"] == 0) {
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    }

    if (!isset($_GET['b_id']) || !is_numeric($_GET['b_id'])) {
        echo '<h3>Invalid Booking ID</h3>';
        exit();
    }

    $b_id = intval($_GET['b_id']);
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event_mgt";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve booking along with user details
    $sql = "SELECT b.*, u.First_Name, u.Last_Name, u.Email_ID, u.MobileNo 
            FROM tbl_booking b 
            JOIN tbl_user u ON b.b_uid = u.ID 
            WHERE b.b_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $b_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '<h3>Booking not found.</h3>';
        $stmt->close();
        $conn->close();
        exit();
    }

    $booking = $result->fetch_assoc();

    // Secure checking: Only the booked user or Admin can view the invoice
    if ($_SESSION["uId"] != $booking['b_uid'] && $_SESSION["email"] != "admin@gmail.com") {
        echo '<h3>Unauthorized Access.</h3>';
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();
    $conn->close();

    // Helper functions to parseaddons
    function parseAddons($msg) {
        $addons = [];
        if (preg_match('/^\[Add-ons:\s*(.*?)\]/', $msg, $matches)) {
            $addon_str = $matches[1];
            $parts = explode(" | ", $addon_str);
            foreach ($parts as $part) {
                $cost = 0;
                if (preg_match('/\(\+Rs\.(\d+)\)/', $part, $c_matches)) {
                    $cost = intval($c_matches[1]);
                }
                $clean_name = preg_replace('/\s*\(\+Rs\.\d+\)/', '', $part);
                $addons[] = [
                    'name' => $clean_name,
                    'cost' => $cost
                ];
            }
        }
        return $addons;
    }

    function parseUserMessage($msg) {
        return preg_replace('/^\[Add-ons:\s*.*?\]\s*/', '', $msg);
    }

    $addons = parseAddons($booking["b_msg"]);
    $clean_msg = parseUserMessage($booking["b_msg"]);

    $total_price = intval($booking["b_price"]);
    $addon_sum = 0;
    foreach ($addons as $addon) {
        $addon_sum += $addon['cost'];
    }
    $base_package_price = $total_price - $addon_sum;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Booking #<?php echo $booking['b_id']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 3rem 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .invoice-card {
            background: #fff;
            max-width: 800px;
            width: 100%;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 4rem;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .actions-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 1.5rem;
        }

        .btn-print {
            background: #03e9f4;
            color: #0f172a;
            padding: 0.8rem 1.8rem;
            border-radius: 0.5rem;
            font-size: 1.4rem;
            font-weight: bold;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.3s;
        }

        .btn-print:hover {
            background: #0f172a;
            color: #03e9f4;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4rem;
        }

        .brand h2 {
            font-size: 2.8rem;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0 0 0.5rem 0;
        }

        .brand h2 span {
            color: #03e9f4;
        }

        .brand p {
            color: #64748b;
            margin: 0;
            font-size: 1.4rem;
        }

        .meta-details {
            text-align: right;
        }

        .meta-details h3 {
            font-size: 2.2rem;
            color: #0f172a;
            margin: 0 0 0.5rem 0;
        }

        .meta-details p {
            margin: 0.3rem 0;
            font-size: 1.4rem;
            color: #64748b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .info-block h4 {
            font-size: 1.6rem;
            color: #0f172a;
            margin: 0 0 0.8rem 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .info-block p {
            margin: 0.4rem 0;
            font-size: 1.5rem;
            color: #475569;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4rem;
        }

        .table-items th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 1.3rem;
            padding: 1.2rem;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-items td {
            padding: 1.4rem 1.2rem;
            font-size: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        .table-items tr:last-child td {
            border-bottom: 2px solid #e2e8f0;
        }

        .total-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 3rem;
        }

        .total-box {
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            font-size: 1.5rem;
        }

        .total-row.grand-total {
            font-size: 2.2rem;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #0f172a;
            margin-top: 1rem;
            padding-top: 1.5rem;
        }

        .invoice-footer {
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 2.5rem;
            color: #64748b;
            font-size: 1.3rem;
        }

        .invoice-footer p {
            margin: 0.4rem 0;
        }

        .status-stamp {
            display: inline-block;
            border: 3px solid;
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            font-size: 1.8rem;
            font-weight: bold;
            text-transform: uppercase;
            transform: rotate(-10deg);
            position: absolute;
            top: 120px;
            right: 40px;
            opacity: 0.85;
        }

        .stamp-upcoming {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .stamp-finished {
            border-color: #22c55e;
            color: #22c55e;
        }

        .stamp-cancelled {
            border-color: #ef4444;
            color: #ef4444;
        }

        .stamp-confirmed {
            border-color: #eab308;
            color: #eab308;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .actions-panel {
                display: none;
            }
            .status-stamp {
                top: 80px;
            }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    <!-- Top toolbar, hidden during printing -->
    <div class="actions-panel">
        <a href="booking.php" style="color: #64748b; font-size: 1.4rem; font-weight: 600;"><i class="fa fa-arrow-left"></i> Back to My Bookings</a>
        <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> Print Invoice</button>
    </div>

    <!-- Status Stamp overlay -->
    <?php
        $stampClass = 'stamp-upcoming';
        if ($booking["b_state"] == 'Finished') {
            $stampClass = 'stamp-finished';
        } elseif ($booking["b_state"] == 'Cancelled') {
            $stampClass = 'stamp-cancelled';
        } elseif ($booking["b_state"] == 'Confirmed') {
            $stampClass = 'stamp-confirmed';
        }
    ?>
    <div class="status-stamp <?php echo $stampClass; ?>">
        <?php echo $booking["b_state"]; ?>
    </div>

    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="brand">
            <h2><span>EVENT</span>management</h2>
            <p>102 Royal Arcade, Nilambag Circle,</p>
            <p>Bhavnagar, Gujarat - 364004</p>
            <p>Phone: +91 98426 38632</p>
        </div>
        <div class="meta-details">
            <h3>INVOICE</h3>
            <p><strong>Invoice ID:</strong> #INV-<?php echo str_pad($booking['b_id'], 6, '0', STR_PAD_LEFT); ?></p>
            <p><strong>Booking Date:</strong> <?php echo date("d M, Y", strtotime($booking['b_date'])); ?></p>
            <p><strong>Payment Method:</strong> Card Payment (Simulated)</p>
        </div>
    </div>

    <!-- Info Billing section -->
    <div class="info-grid">
        <div class="info-block">
            <h4>Billed To:</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['First_Name'] . ' ' . $booking['Last_Name']); ?></p>
            <p><strong>Email ID:</strong> <?php echo htmlspecialchars($booking['Email_ID']); ?></p>
            <p><strong>Phone:</strong> +91 <?php echo htmlspecialchars($booking['MobileNo']); ?></p>
        </div>
        <div class="info-block">
            <h4>Event Booking Details:</h4>
            <p><strong>Event Type:</strong> <?php echo htmlspecialchars($booking['b_type']); ?></p>
            <p><strong>Selected Venue:</strong> <span style="text-transform: capitalize;"><?php echo htmlspecialchars($booking['b_venue']); ?></span></p>
            <p><strong>Package Type:</strong> <?php echo htmlspecialchars($booking['b_package']); ?></p>
        </div>
    </div>

    <!-- Billing Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 50%;">Description / Service</th>
                <th style="width: 25%; text-align: right;">Unit Price</th>
                <th style="width: 25%; text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Base Package: <?php echo htmlspecialchars($booking['b_type']) . ' (' . htmlspecialchars($booking['b_package']) . ')'; ?></strong><br>
                    <span style="font-size: 1.3rem; color: #64748b;">Includes catering standard, standard decorations, sounds and sounds coordination.</span>
                </td>
                <td style="text-align: right;">Rs. <?php echo number_format($base_package_price); ?></td>
                <td style="text-align: right; font-weight: 600;">Rs. <?php echo number_format($base_package_price); ?></td>
            </tr>
            
            <?php if (!empty($addons)): ?>
                <?php foreach ($addons as $addon): ?>
                    <tr>
                        <td>
                            <strong>+ Add-on: <?php echo htmlspecialchars($addon['name']); ?></strong><br>
                            <span style="font-size: 1.3rem; color: #64748b;">Requested additional service coordination.</span>
                        </td>
                        <td style="text-align: right;">Rs. <?php echo number_format($addon['cost']); ?></td>
                        <td style="text-align: right; font-weight: 600;">Rs. <?php echo number_format($addon['cost']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Totals and notes -->
    <div class="total-summary">
        <div class="total-box">
            <div class="total-row">
                <span style="color: #64748b;">Subtotal:</span>
                <span>Rs. <?php echo number_format($total_price); ?></span>
            </div>
            <div class="total-row">
                <span style="color: #64748b;">Service Tax (0%):</span>
                <span>Rs. 0</span>
            </div>
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span>Rs. <?php echo number_format($total_price); ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($clean_msg)): ?>
        <div style="background: #f8fafc; border-left: 4px solid #03e9f4; padding: 1.5rem; margin-bottom: 4rem; border-radius: 0.3rem;">
            <strong style="font-size: 1.4rem; display: block; margin-bottom: 0.5rem; color: #475569;">Client Instructions / Message:</strong>
            <p style="margin: 0; font-size: 1.4rem; color: #334155; font-style: italic;">"<?php echo htmlspecialchars($clean_msg); ?>"</p>
        </div>
    <?php endif; ?>

    <!-- Invoice Footer -->
    <div class="invoice-footer">
        <p>Thank you for choosing <strong>Event Management Systems</strong>!</p>
        <p>If you have any questions about this invoice, please contact our helpline.</p>
        <p style="margin-top: 1.5rem; font-size: 1.1rem; color: #94a3b8;">This is a computer-generated billing invoice receipt. No signature required.</p>
    </div>
</div>

</body>
</html>
