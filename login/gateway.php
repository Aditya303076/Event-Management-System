<?php
    session_start();

    if($_SESSION["state"] == 0) {
        echo '<script>
        setTimeout(function(){
            window.location.href = "login.php";
        })
        </script>';           
        exit();
    }

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

    $addons = parseAddons($_SESSION["b_msg"]);
    $clean_msg = parseUserMessage($_SESSION["b_msg"]);

    // Calculate base price
    $total_price = intval($_SESSION["b_price"]);
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Payment Gateway</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap');

        :root {
            --main-color: #03e9f4;
            --bg-dark: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.7);
            --border-color: rgba(3, 233, 244, 0.25);
        }

        * {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            outline: none;
            border: none;
            text-decoration: none;
        }

        body {
            background-color: var(--bg-dark);
            background-image: radial-gradient(at 0% 0%, rgba(3, 233, 244, 0.15) 0, transparent 50%), 
                              radial-gradient(at 50% 100%, rgba(15, 23, 42, 0.95) 0, transparent 50%);
            min-height: 100vh;
            color: #fff;
            padding: 2rem 5%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 1100px;
        }

        .header h1 {
            font-size: 3rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header h1 span {
            color: var(--main-color);
        }

        .gateway-container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            max-width: 1100px;
            gap: 3rem;
            margin-top: 1rem;
        }

        .invoice-panel, .payment-panel {
            flex: 1 1 450px;
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .panel-title {
            font-size: 2.2rem;
            color: var(--main-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Invoice styles */
        .invoice-item {
            display: flex;
            justify-content: space-between;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 1rem;
        }

        .invoice-item .label {
            color: #94a3b8;
        }

        .invoice-item .value {
            font-weight: 600;
        }

        .addons-title {
            font-size: 1.6rem;
            color: var(--main-color);
            margin: 2rem 0 1rem 0;
            font-weight: bold;
        }

        .addon-line {
            display: flex;
            justify-content: space-between;
            font-size: 1.4rem;
            color: #cbd5e1;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
            border-left: 2px solid var(--main-color);
        }

        .total-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-section .label {
            font-size: 2rem;
            font-weight: bold;
        }

        .total-section .value {
            font-size: 2.6rem;
            color: var(--main-color);
            font-weight: bold;
        }

        /* Payment form styling */
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
            border-radius: 0.6rem;
            padding: 1.2rem;
            font-size: 1.5rem;
            color: #fff;
            transition: all 0.3s;
        }

        .inputBox input:focus {
            border-color: var(--main-color);
            box-shadow: 0 0 8px rgba(3, 233, 244, 0.3);
        }

        .card-icons {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card-icons img {
            height: 3.5rem;
            background: #fff;
            border-radius: 0.3rem;
            padding: 0.2rem;
        }

        .flex-row {
            display: flex;
            gap: 2rem;
        }

        .flex-row .inputBox {
            flex: 1;
        }

        .submit-btn {
            width: 100%;
            background: var(--main-color);
            color: #0f172a;
            font-size: 1.8rem;
            font-weight: bold;
            padding: 1.4rem;
            border-radius: 0.8rem;
            cursor: pointer;
            margin-top: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background: #fff;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }

        @media(max-width: 768px) {
            .gateway-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <h1><span>Secure</span> Payment Gateway</h1>
</header>

<div class="gateway-container">
    <!-- Left Column: Invoice Summary -->
    <div class="invoice-panel">
        <h3 class="panel-title"><i class="fa fa-file-text-o"></i> Booking Receipt Invoice</h3>
        
        <div class="invoice-item">
            <span class="label">Event Category:</span>
            <span class="value"><?php echo htmlspecialchars($_SESSION["b_type"]); ?></span>
        </div>
        <div class="invoice-item">
            <span class="label">Selected Package:</span>
            <span class="value"><?php echo htmlspecialchars($_SESSION["b_package"]); ?></span>
        </div>
        <div class="invoice-item">
            <span class="label">Date of Event:</span>
            <span class="value"><?php echo htmlspecialchars($_SESSION["b_date"]); ?></span>
        </div>
        <div class="invoice-item">
            <span class="label">Selected Venue:</span>
            <span class="value" style="text-transform: capitalize;"><?php echo htmlspecialchars($_SESSION["b_venue"]); ?></span>
        </div>
        
        <?php if (!empty($addons)): ?>
            <h4 class="addons-title">Selected Enhancements:</h4>
            <?php foreach ($addons as $addon): ?>
                <div class="addon-line">
                    <span><?php echo htmlspecialchars($addon['name']); ?></span>
                    <span>Rs. <?php echo number_format($addon['cost']); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($clean_msg)): ?>
            <div class="invoice-item" style="flex-direction: column; border: none; margin-top: 1.5rem;">
                <span class="label" style="margin-bottom: 0.5rem;">Special Notes:</span>
                <span class="value" style="font-weight: normal; font-size: 1.4rem; color: #94a3b8; font-style: italic;">
                    "<?php echo htmlspecialchars($clean_msg); ?>"
                </span>
            </div>
        <?php endif; ?>

        <div class="total-section">
            <span class="label">Total Booking cost:</span>
            <span class="value">Rs. <?php echo number_format($total_price); ?></span>
        </div>
    </div>

    <!-- Right Column: Card Payment Form -->
    <div class="payment-panel">
        <h3 class="panel-title"><i class="fa fa-credit-card"></i> Pay via Credit / Debit Card</h3>
        
        <form action="" method="post">
            <div class="card-icons">
                <img src="images/card_img.png" alt="Accepted Cards">
            </div>

            <div class="inputBox">
                <span>Cardholder Name :</span>
                <input type="text" placeholder="e.g. Johnathan Doe" name="card_name" required>
            </div>
            
            <div class="inputBox">
                <span>Card Number :</span>
                <input type="text" placeholder="1111-2222-3333-4444" name="card_number" minlength="16" maxlength="19" required>
            </div>

            <div class="flex-row">
                <div class="inputBox">
                    <span>Expiry Month :</span>
                    <input type="text" placeholder="e.g. September" name="card_month" required>
                </div>
                <div class="flex-row">
                    <div class="inputBox" style="width: 80px;">
                        <span>Exp Year :</span>
                        <input type="number" placeholder="2028" name="card_year" min="2026" max="2040" required>
                    </div>
                    <div class="inputBox" style="width: 70px;">
                        <span>CVV :</span>
                        <input type="text" placeholder="123" name="card_cvv" minlength="3" maxlength="4" required>
                    </div>
                </div>
            </div>

            <h3 class="panel-title" style="margin-top: 2rem; font-size: 1.8rem;"><i class="fa fa-home"></i> Billing Address</h3>
            
            <div class="inputBox">
                <span>Street Address :</span>
                <input type="text" placeholder="e.g. 101, Nilambag Street" name="billing_address" required>
            </div>

            <div class="flex-row">
                <div class="inputBox">
                    <span>City :</span>
                    <input type="text" placeholder="Bhavnagar" name="billing_city" required>
                </div>
                <div class="inputBox">
                    <span>Zip Code :</span>
                    <input type="text" placeholder="364001" name="billing_zip" required>
                </div>
            </div>

            <input type="submit" name="btn_final" value="Pay Rs. <?php echo number_format($total_price); ?>" class="submit-btn">
        </form>
    </div>
</div>

<?php
    if(array_key_exists('btn_final', $_POST)) {
        $servername = getenv('DB_HOST') ?: "localhost";
        $username = getenv('DB_USER') ?: "root";
        $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
        $dbname = getenv('DB_NAME') ?: "event_mgt";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }   
       
        $uid = $_SESSION["uId"];
        $btype = $_SESSION["b_type"];
        $bpack = $_SESSION["b_package"];
        $bprice = $_SESSION["b_price"];
        $bvenue = $_SESSION["b_venue"];
        $bdate = $_SESSION["b_date"];
        $bmsg = $_SESSION["b_msg"];
        $bstate = "Upcoming";

        // Insert booking into Database
        $sql = "INSERT INTO tbl_booking (b_uid, b_type, b_package, b_date, b_venue, b_price, b_msg, b_state) 
                VALUES ('$uid', '$btype', '$bpack', '$bdate', '$bvenue', '$bprice', '$bmsg', '$bstate')";

        $result = $conn->query($sql);

        if ($result == TRUE) {
            // Get the newly inserted booking ID to store in session
            $_SESSION["last_booking_id"] = $conn->insert_id;
            
            echo '<script>
                setTimeout(function(){
                    window.location.href = "approval.php";
                })
                </script>';
            $conn->close();
            exit();
        } else {
            echo '<script>alert("Error inserting booking detail: ' . $conn->error . '")</script>';
        }
        $conn->close();
    }
?>
</body>
</html>