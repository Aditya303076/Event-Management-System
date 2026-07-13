<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Event Booking Checkout</title>
</head>
<body>
    <header class="checkout-header">
        <a href="../user.php" class="logo"><span>EVENT</span>management</a>
        <a href="../user.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </header>

    <div class="checkout-container">
        <div class="form-column">
            <section class="contact" id="contact">
                <div class="content">
                    <h3 class="heading">Customize your <span><?php echo htmlspecialchars($_SESSION["b_type"]); ?></span> Event</h3>
                </div>
                <form name="form" action="" method="post">
                    <div class="inputbox" style="display: flex; flex-direction: column; gap: 2rem;">
                        <!-- Select Venue -->
                        <div style="flex: 1;">
                            <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin-bottom: 1rem;"><i class="fa fa-map-marker"></i> Select Venue</h3>
                            <div class="dropdown-wrapper">
                                <select id="hotel" name="hotel" onchange="updateBackground()" required>
                                    <option value="nilambag palace">Nilambag Palace - Nilambag Circle, Bhavnagar, 364001, Gujarat</option>
                                    <option value="sarovar portico">Sarovar Portico - Near Himalaya Mall, Bhavnagar, 364001, Gujarat</option>
                                    <option value="the basil park" selected>The Basil Park - Near MKBU University Hostel, Bhavnagar, 364001, Gujarat</option>
                                </select>
                            </div>
                        </div>

                        <!-- Select Date -->
                        <div style="flex: 1;">
                            <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin-bottom: 1rem;"><i class="fa fa-calendar"></i> Select Date</h3>
                            <input type="date" id="date" name="date" style="color: white; width: 100%;" required>
                        </div>
                    </div>

                    <!-- Customizations based on event type -->
                    <div class="customization-section">
                        <?php if ($_SESSION["b_type"] == "BIRTHDAY"): ?>
                            <!-- Birthday Cake Flavor Options -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-birthday-cake"></i> Cake Flavor</h3>
                                <div class="option-grid">
                                    <label class="option-card">
                                        <input type="radio" name="cake" value="Chocolate (Included)" checked>
                                        <span class="card-title">Chocolate (Included)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="cake" value="Red Velvet (+Rs.1000)" data-addon="1000">
                                        <span class="card-title">Red Velvet (+Rs. 1,000)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="cake" value="Butterscotch (+Rs.800)" data-addon="800">
                                        <span class="card-title">Butterscotch (+Rs. 800)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Birthday Decoration Options -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-paint-brush"></i> Decoration Style</h3>
                                <div class="option-grid">
                                    <label class="option-card">
                                        <input type="radio" name="deco" value="Balloons (Included)" checked>
                                        <span class="card-title">Balloons (Included)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="deco" value="Confetti & Neon (+Rs.1500)" data-addon="1500">
                                        <span class="card-title">Confetti & Neon (+Rs. 1,500)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="deco" value="Premium Floral (+Rs.3000)" data-addon="3000">
                                        <span class="card-title">Premium Floral (+Rs. 3,000)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Birthday Addons -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-plus-circle"></i> Service Enhancements</h3>
                                <div class="option-grid">
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Photographer (+Rs.10000)" data-addon="10000">
                                        <span class="card-title">Photographer (+Rs. 10,000)</span>
                                    </label>
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="DJ and Sound (+Rs.8000)" data-addon="8000">
                                        <span class="card-title">DJ & Sound (+Rs. 8,000)</span>
                                    </label>
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Magic Show (+Rs.5000)" data-addon="5000">
                                        <span class="card-title">Magic Show & Host (+Rs. 5,000)</span>
                                    </label>
                                </div>
                            </div>

                        <?php elseif ($_SESSION["b_type"] == "WEDDING"): ?>
                            <!-- Wedding Stage Theme Options -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-university"></i> Stage & Theme</h3>
                                <div class="option-grid">
                                    <label class="option-card">
                                        <input type="radio" name="stage" value="Traditional Marigold (Included)" checked>
                                        <span class="card-title">Traditional Marigold (Included)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="stage" value="Royal White & Gold (+Rs.20000)" data-addon="20000">
                                        <span class="card-title">Royal White & Gold (+Rs. 20,000)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="stage" value="Modern Glass & Neon (+Rs.35000)" data-addon="35000">
                                        <span class="card-title">Modern Glass & Neon (+Rs. 35,000)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Wedding Addons -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-plus-circle"></i> Service Enhancements</h3>
                                <div class="option-grid">
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Live Orchestra (+Rs.25000)" data-addon="25000">
                                        <span class="card-title">Live Orchestra (+Rs. 25,000)</span>
                                    </label>
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Drone Video (+Rs.15000)" data-addon="15000">
                                        <span class="card-title">Drone Videography (+Rs. 15,000)</span>
                                    </label>
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Premium Dessert Buffet (+Rs.40000)" data-addon="40000">
                                        <span class="card-title">Premium Dessert Buffet (+Rs. 40,000)</span>
                                    </label>
                                </div>
                            </div>

                        <?php elseif ($_SESSION["b_type"] == "CONCERTS"): ?>
                            <!-- Concert Stage Layout Options -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-music"></i> Stage Layout</h3>
                                <div class="option-grid">
                                    <label class="option-card">
                                        <input type="radio" name="layout" value="Proscenium Stage (Included)" checked>
                                        <span class="card-title">Proscenium Stage (Included)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="layout" value="Thrust Stage (+Rs.25000)" data-addon="25000">
                                        <span class="card-title">Thrust Stage (+Rs. 25,000)</span>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="layout" value="Arena 360 Stage (+Rs.50000)" data-addon="50000">
                                        <span class="card-title">Arena 360 Stage (+Rs. 50,000)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Concert Addons -->
                            <div class="selection">
                                <h3 class="heading" style="font-size: 2rem; color: white; font-weight: bold; margin: 2rem 0 1rem 0;"><i class="fa fa-plus-circle"></i> Service Enhancements</h3>
                                <div class="option-grid">
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Laser Show Upgrade (+Rs.30000)" data-addon="30000">
                                        <span class="card-title">Laser Show Upgrade (+Rs. 30,000)</span>
                                    </label>
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="Extra Security Detail (+Rs.15000)" data-addon="15000">
                                        <span class="card-title">Extra Security Detail (+Rs. 15,000)</span>
                                    </label>
                                    <label class="option-card-checkbox">
                                        <input type="checkbox" name="addons[]" value="VIP Lounge Area (+Rs.25000)" data-addon="25000">
                                        <span class="card-title">VIP Lounge Area (+Rs. 25,000)</span>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <textarea name="message" placeholder="Write any specific event requirements, wishes, or notes for the managers..." id="" cols="30" rows="6" required></textarea>
                    <input type="submit" value="Confirm Details & Pay" class="btn" style="font-weight: bold; width: 100%; margin-top: 1rem;">  
                </form>
            </section>
        </div>

        <!-- Sidebar pricing billing invoice calculator -->
        <div class="invoice-column">
            <div class="invoice-summary-card">
                <h3 class="summary-title"><i class="fa fa-file-text-o"></i> Live Cost Breakdown</h3>
                <div class="summary-line">
                    <span class="label">Event Type</span>
                    <span class="val"><?php echo htmlspecialchars($_SESSION["b_type"]); ?></span>
                </div>
                <div class="summary-line">
                    <span class="label">Base Package</span>
                    <span class="val"><?php echo htmlspecialchars($_SESSION["b_package"]); ?></span>
                </div>
                <div class="summary-line border-bottom">
                    <span class="label">Package Price</span>
                    <span class="val">Rs. <span id="summary-base-price"><?php echo htmlspecialchars($_SESSION["b_price"]); ?></span></span>
                </div>

                <div id="dynamic-addons-list" class="dynamic-addons-list">
                    <!-- Populated by Javascript -->
                </div>

                <div class="summary-line total-line">
                    <span class="label">Total Est. Price</span>
                    <span class="val">Rs. <span id="summary-total-price"><?php echo htmlspecialchars($_SESSION["b_price"]); ?></span></span>
                </div>
                
                <div class="info-alert">
                    <i class="fa fa-info-circle"></i> Payments are processed via simulated sandbox cards. Confirming details will direct you to standard billing checks.
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateBackground() {
            var selectedHotel = document.getElementById("hotel").value;
            document.body.style.background = "url('" + getBackgroundImage(selectedHotel) + "') fixed center/cover";
            document.body.style.backgroundBlendMode = "darken";
            document.body.style.backgroundColor = "rgba(0, 0, 0, 0.65)";
        }

        function getBackgroundImage(hotel) {
            switch (hotel) {
                case 'nilambag palace':
                    return 'birthday2.jpg';
                case 'sarovar portico':
                    return 'birthday3.jpg';
                case 'the basil park':
                    return 'download.jpg';
                default:
                    return 'bg.png';
            }
        }

        // Realtime dynamic calculator
        const basePriceVal = parseInt("<?php echo $_SESSION['b_price']; ?>");
        const summaryTotalPrice = document.getElementById('summary-total-price');
        const dynamicAddonsList = document.getElementById('dynamic-addons-list');

        function calculatePrice() {
            let total = basePriceVal;
            let addonsHtml = '';

            // Selected Radios
            const selectedRadios = document.querySelectorAll('input[type="radio"]:checked');
            selectedRadios.forEach(radio => {
                const cost = parseInt(radio.getAttribute('data-addon') || 0);
                if (cost > 0) {
                    total += cost;
                    const cleanName = radio.value.split(" (+")[0];
                    addonsHtml += `<div class="addon-row"><span>+ ${cleanName}</span><span>Rs. ${cost.toLocaleString('en-IN')}</span></div>`;
                }
            });

            // Selected Checkboxes
            const selectedChecks = document.querySelectorAll('input[type="checkbox"]:checked');
            selectedChecks.forEach(check => {
                const cost = parseInt(check.getAttribute('data-addon') || 0);
                if (cost > 0) {
                    total += cost;
                    const cleanName = check.value.split(" (+")[0];
                    addonsHtml += `<div class="addon-row"><span>+ ${cleanName}</span><span>Rs. ${cost.toLocaleString('en-IN')}</span></div>`;
                }
            });

            dynamicAddonsList.innerHTML = addonsHtml;
            summaryTotalPrice.innerText = total.toLocaleString('en-IN');
        }

        document.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(input => {
            input.addEventListener('change', calculatePrice);
        });

        // Initialize setup
        updateBackground();
        calculatePrice();

        // Enforce future date selection
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').setAttribute('min', today);
    </script>

    <?php
        if($_SESSION["state"] == 0)
        {
            echo '<script>
            setTimeout(function(){
                window.location.href = "../login.php";
            })
            </script>';           
            exit();
        }  
        
        elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "event_mgt";
            
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Check double booking for the selected date and venue (exclude cancelled bookings)
            $checkSql = "SELECT * FROM tbl_booking WHERE b_venue = ? AND b_date = ? AND b_state != 'Cancelled'";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("ss", $_POST['hotel'], $_POST['date']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                echo '<script>
                alert("This venue is already booked on this date! Please select a different venue or date.");
                window.history.back();
                </script>';
                $checkStmt->close();
                $conn->close();
                exit();
            }
            $checkStmt->close();
            
            // Calculate final price and aggregate details
            $base_price = intval($_SESSION["b_price"]);
            $calculated_price = $base_price;
            $selected_addons = [];

            // Cake options check
            if (isset($_POST['cake'])) {
                if (strpos($_POST['cake'], '(+Rs.') !== false) {
                    preg_match('/\(\+Rs\.(\d+)\)/', $_POST['cake'], $matches);
                    if (isset($matches[1])) {
                        $calculated_price += intval($matches[1]);
                    }
                    $selected_addons[] = "Cake flavor: " . $_POST['cake'];
                } else {
                    $selected_addons[] = "Cake flavor: " . $_POST['cake'];
                }
            }

            // Decoration options check
            if (isset($_POST['deco'])) {
                if (strpos($_POST['deco'], '(+Rs.') !== false) {
                    preg_match('/\(\+Rs\.(\d+)\)/', $_POST['deco'], $matches);
                    if (isset($matches[1])) {
                        $calculated_price += intval($matches[1]);
                    }
                    $selected_addons[] = "Deco theme: " . $_POST['deco'];
                } else {
                    $selected_addons[] = "Deco theme: " . $_POST['deco'];
                }
            }

            // Stage theme check
            if (isset($_POST['stage'])) {
                if (strpos($_POST['stage'], '(+Rs.') !== false) {
                    preg_match('/\(\+Rs\.(\d+)\)/', $_POST['stage'], $matches);
                    if (isset($matches[1])) {
                        $calculated_price += intval($matches[1]);
                    }
                    $selected_addons[] = "Stage layout: " . $_POST['stage'];
                } else {
                    $selected_addons[] = "Stage layout: " . $_POST['stage'];
                }
            }

            // Concert Stage layout check
            if (isset($_POST['layout'])) {
                if (strpos($_POST['layout'], '(+Rs.') !== false) {
                    preg_match('/\(\+Rs\.(\d+)\)/', $_POST['layout'], $matches);
                    if (isset($matches[1])) {
                        $calculated_price += intval($matches[1]);
                    }
                    $selected_addons[] = "Stage config: " . $_POST['layout'];
                } else {
                    $selected_addons[] = "Stage config: " . $_POST['layout'];
                }
            }

            // Checkbox addons check
            if (isset($_POST['addons']) && is_array($_POST['addons'])) {
                foreach ($_POST['addons'] as $addon) {
                    preg_match('/\(\+Rs\.(\d+)\)/', $addon, $matches);
                    if (isset($matches[1])) {
                        $calculated_price += intval($matches[1]);
                    }
                    $selected_addons[] = $addon;
                }
            }

            // Save finalized values to Session variables
            $_SESSION["b_venue"] = $_POST['hotel'];
            $_SESSION["b_date"] = $_POST['date'];
            $_SESSION["b_price"] = $calculated_price;
            
            // Format details into message
            $msg_prefix = "";
            if (!empty($selected_addons)) {
                $msg_prefix = "[Add-ons: " . implode(" | ", $selected_addons) . "] ";
            }
            $_SESSION["b_msg"] = $msg_prefix . $_POST['message'];
            $_SESSION["b_state"] = "Upcoming";

            $conn->close();

            echo '<script>
            setTimeout(function(){
                window.location.href = "../gateway.php";
            })
            </script>';
            exit();
        }
    ?>
</body>
</html>
