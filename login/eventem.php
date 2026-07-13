<?php
    session_start();
    require 'eventem_db.php'; // Ensures tables and mock data are active

    if (!isset($_SESSION["state"]) || $_SESSION["state"] == 0) {
        echo '<script>window.location.href = "login.php";</script>';
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event_mgt";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve active public events
    $sql = "SELECT e.*, u.First_Name, u.Last_Name FROM tbl_public_events e JOIN tbl_user u ON e.organizer_id = u.ID ORDER BY e.event_date ASC";
    $result = $conn->query($sql);
    
    $events = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventem - Gujarat Local Events Directory</title>
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
            display: flex;
            flex-direction: column;
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
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
        }

        .nav-btn:hover, .nav-btn.active {
            background: var(--main-color);
            color: #0b0f19;
            box-shadow: 0 0 12px rgba(3, 233, 244, 0.3);
        }

        /* Layout */
        .eventem-layout {
            display: flex;
            flex: 1;
            width: 100%;
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 3%;
            gap: 2.5rem;
        }

        /* District Sidebar */
        .district-sidebar {
            width: 260px;
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 1.2rem;
            padding: 2rem;
            align-self: flex-start;
            position: sticky;
            top: 9rem;
        }

        .sidebar-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--main-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 0.8rem;
        }

        .district-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .district-item button {
            width: 100%;
            text-align: left;
            background: transparent;
            color: #cbd5e1;
            font-size: 1.45rem;
            padding: 0.8rem 1.2rem;
            border-radius: 0.6rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .district-item button:hover, .district-item button.active {
            background: var(--hover-bg);
            color: var(--main-color);
            font-weight: 600;
        }

        .district-item button span.badge {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.2rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 1.1rem;
            color: #94a3b8;
        }

        /* Main feed */
        .main-feed {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        /* Search & Filter Toolbar */
        .toolbar {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 1.2rem;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .search-row {
            position: relative;
            width: 100%;
        }

        .search-row i {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.8rem;
        }

        .search-input {
            width: 100%;
            background: rgba(11, 15, 25, 0.7);
            border: 1px solid var(--border-color);
            color: #fff;
            font-size: 1.6rem;
            padding: 1.2rem 1.5rem 1.2rem 4.5rem;
            border-radius: 0.8rem;
        }

        .search-input:focus {
            border-color: var(--main-color);
            box-shadow: 0 0 10px rgba(3, 233, 244, 0.2);
        }

        .category-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .category-chip {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            padding: 0.6rem 1.4rem;
            border-radius: 5rem;
            font-size: 1.3rem;
            cursor: pointer;
            font-weight: 500;
        }

        .category-chip:hover, .category-chip.active {
            background: var(--hover-bg);
            border-color: var(--main-color);
            color: var(--main-color);
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .event-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 1.2rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(3, 233, 244, 0.15);
            border-color: var(--main-color);
        }

        .card-header {
            position: relative;
            height: 120px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .category-tag {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: rgba(3, 233, 244, 0.15);
            border: 1px solid var(--main-color);
            color: var(--main-color);
            padding: 0.3rem 0.8rem;
            border-radius: 0.4rem;
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .price-badge {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            background: #0f172a;
            color: #03e9f4;
            border: 1px solid var(--border-color);
            padding: 0.4rem 1rem;
            border-radius: 0.5rem;
            font-weight: bold;
            font-size: 1.3rem;
        }

        .card-header .event-icon {
            font-size: 4rem;
            color: rgba(3, 233, 244, 0.3);
        }

        .card-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 1rem;
        }

        .event-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
            line-height: 1.3;
        }

        .event-meta-line {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.35rem;
            color: #94a3b8;
        }

        .event-meta-line i {
            color: var(--main-color);
            width: 1.5rem;
        }

        .event-desc {
            font-size: 1.3rem;
            color: #cbd5e1;
            margin-top: 0.5rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-footer {
            padding: 1.5rem 2rem;
            background: rgba(15, 23, 42, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .organizer-info {
            font-size: 1.2rem;
            color: #64748b;
        }

        .view-btn {
            background: var(--main-color);
            color: #0b0f19;
            font-size: 1.3rem;
            font-weight: bold;
            padding: 0.6rem 1.4rem;
            border-radius: 0.5rem;
        }

        .view-btn:hover {
            background: #fff;
        }

        @media(max-width: 900px) {
            .eventem-layout {
                flex-direction: column;
            }
            .district-sidebar {
                width: 100%;
                position: static;
            }
            .district-list {
                flex-direction: row;
                flex-wrap: wrap;
            }
            .district-item {
                flex: 1 1 120px;
            }
        }
    </style>
</head>
<body>

    <header class="eventem-header">
        <a href="user.php" class="logo"><i class="fa fa-slack"></i> <span>EVENT</span>em</a>
        <div class="nav-actions">
            <a href="user.php" class="nav-btn"><i class="fa fa-arrow-left"></i> Home</a>
            <a href="eventem_create.php" class="nav-btn"><i class="fa fa-plus"></i> Create Event</a>
            <a href="eventem_dashboard.php" class="nav-btn active"><i class="fa fa-tasks"></i> Organizer Panel</a>
        </div>
    </header>

    <div class="eventem-layout">
        <!-- Sidebar filters by Gujarat Districts -->
        <aside class="district-sidebar">
            <div class="sidebar-title">
                <i class="fa fa-map-marker"></i> Gujarat Districts
            </div>
            <ul class="district-list">
                <li class="district-item"><button class="active" onclick="filterDistrict('All', this)">All districts <span class="badge" id="badge-all">0</span></button></li>
                <li class="district-item"><button onclick="filterDistrict('Ahmedabad', this)">Ahmedabad <span class="badge" id="badge-ahmedabad">0</span></button></li>
                <li class="district-item"><button onclick="filterDistrict('Surat', this)">Surat <span class="badge" id="badge-surat">0</span></button></li>
                <li class="district-item"><button onclick="filterDistrict('Vadodara', this)">Vadodara <span class="badge" id="badge-vadodara">0</span></button></li>
                <li class="district-item"><button onclick="filterDistrict('Rajkot', this)">Rajkot <span class="badge" id="badge-rajkot">0</span></button></li>
                <li class="district-item"><button onclick="filterDistrict('Bhavnagar', this)">Bhavnagar <span class="badge" id="badge-bhavnagar">0</span></button></li>
                <li class="district-item"><button onclick="filterDistrict('Gandhinagar', this)">Gandhinagar <span class="badge" id="badge-gandhinagar">0</span></button></li>
            </ul>
        </aside>

        <!-- Main listings stream -->
        <main class="main-feed">
            <!-- Search + Category filters -->
            <div class="toolbar">
                <div class="search-row">
                    <i class="fa fa-search"></i>
                    <input type="text" id="search-input" class="search-input" placeholder="Search event titles, organizers, venues..." onkeyup="filterEvents()">
                </div>
                <div class="category-row">
                    <button class="category-chip active" onclick="filterCategory('All', this)">All Categories</button>
                    <button class="category-chip" onclick="filterCategory('Garba & Cultural', this)">Garba & Cultural</button>
                    <button class="category-chip" onclick="filterCategory('Music Concert', this)">Music Concert</button>
                    <button class="category-chip" onclick="filterCategory('Business & Seminar', this)">Business & Seminar</button>
                    <button class="category-chip" onclick="filterCategory('Local Workshop', this)">Local Workshop</button>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="events-grid" id="events-container">
                <?php foreach ($events as $event): ?>
                    <?php
                        // Select nice placeholder icon based on category
                        $iconClass = 'fa-calendar';
                        if (strpos($event['category'], 'Garba') !== false) {
                            $iconClass = 'fa-fire'; // campfire for Navratri vibe
                        } elseif (strpos($event['category'], 'Concert') !== false) {
                            $iconClass = 'fa-music';
                        } elseif (strpos($event['category'], 'Seminar') !== false) {
                            $iconClass = 'fa-microphone';
                        } elseif (strpos($event['category'], 'Workshop') !== false) {
                            $iconClass = 'fa-wrench';
                        }
                    ?>
                    <div class="event-card" 
                         data-district="<?php echo htmlspecialchars($event['district']); ?>" 
                         data-category="<?php echo htmlspecialchars($event['category']); ?>" 
                         data-title="<?php echo htmlspecialchars(strtolower($event['event_name'])); ?>"
                         data-venue="<?php echo htmlspecialchars(strtolower($event['venue'])); ?>">
                        
                        <div class="card-header">
                            <span class="category-tag"><?php echo htmlspecialchars($event['category']); ?></span>
                            <i class="fa <?php echo $iconClass; ?> event-icon"></i>
                            <span class="price-badge">
                                <?php echo ($event['price'] == 0) ? 'FREE / RSVP' : 'Rs. ' . number_format($event['price']); ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                            <div class="event-meta-line" style="margin-top: 0.5rem;">
                                <i class="fa fa-calendar"></i>
                                <span><?php echo date("d M, Y", strtotime($event['event_date'])); ?> @ <?php echo date("h:i A", strtotime($event['event_time'])); ?></span>
                            </div>
                            <div class="event-meta-line">
                                <i class="fa fa-map-marker"></i>
                                <span style="text-transform: capitalize;"><?php echo htmlspecialchars($event['venue']); ?> (<?php echo htmlspecialchars($event['district']); ?>)</span>
                            </div>
                            <p class="event-desc"><?php echo htmlspecialchars($event['description']); ?></p>
                        </div>

                        <div class="card-footer">
                            <span class="organizer-info">By <?php echo htmlspecialchars($event['First_Name'] . ' ' . $event['Last_Name']); ?></span>
                            <a href="eventem_detail.php?id=<?php echo $event['id']; ?>" class="view-btn">View Event</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        let selectedDistrict = 'All';
        let selectedCategory = 'All';

        function filterDistrict(district, btn) {
            // Remove active classes
            document.querySelectorAll('.district-list button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedDistrict = district;
            filterEvents();
        }

        function filterCategory(category, btn) {
            document.querySelectorAll('.category-row button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedCategory = category;
            filterEvents();
        }

        function filterEvents() {
            const searchVal = document.getElementById('search-input').value.toLowerCase();
            const cards = document.querySelectorAll('#events-container .event-card');

            cards.forEach(card => {
                const dist = card.getAttribute('data-district');
                const cat = card.getAttribute('data-category');
                const title = card.getAttribute('data-title');
                const venue = card.getAttribute('data-venue');

                const matchesSearch = title.includes(searchVal) || venue.includes(searchVal);
                const matchesDist = (selectedDistrict === 'All') || (dist === selectedDistrict);
                const matchesCat = (selectedCategory === 'All') || (cat === selectedCategory);

                if (matchesSearch && matchesDist && matchesCat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Calculate badges count
        function updateBadges() {
            const cards = document.querySelectorAll('#events-container .event-card');
            let counts = { All: cards.length, Ahmedabad: 0, Surat: 0, Vadodara: 0, Rajkot: 0, Bhavnagar: 0, Gandhinagar: 0 };

            cards.forEach(card => {
                const dist = card.getAttribute('data-district');
                if (counts[dist] !== undefined) {
                    counts[dist]++;
                }
            });

            document.getElementById('badge-all').innerText = counts.All;
            document.getElementById('badge-ahmedabad').innerText = counts.Ahmedabad;
            document.getElementById('badge-surat').innerText = counts.Surat;
            document.getElementById('badge-vadodara').innerText = counts.Vadodara;
            document.getElementById('badge-rajkot').innerText = counts.Rajkot;
            document.getElementById('badge-bhavnagar').innerText = counts.Bhavnagar;
            document.getElementById('badge-gandhinagar').innerText = counts.Gandhinagar;
        }

        // Initialize badges on load
        updateBadges();
    </script>
</body>
</html>
