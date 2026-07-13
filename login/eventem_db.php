<?php
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
$dbname = getenv('DB_NAME') ?: "event_mgt";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Table 1: tbl_public_events
$createEventsTable = "CREATE TABLE IF NOT EXISTS `tbl_public_events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `organizer_id` INT NOT NULL,
  `event_name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `district` VARCHAR(50) NOT NULL,
  `venue` VARCHAR(150) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `price` DOUBLE NOT NULL,
  `description` TEXT NOT NULL,
  `agenda` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$conn->query($createEventsTable);

// Table 2: tbl_public_registrations
$createRegistrationsTable = "CREATE TABLE IF NOT EXISTS `tbl_public_registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_id` INT NOT NULL,
  `attendee_name` VARCHAR(100) NOT NULL,
  `attendee_email` VARCHAR(100) NOT NULL,
  `attendee_phone` VARCHAR(15) NOT NULL,
  `delivery_method` VARCHAR(20) NOT NULL,
  `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `checkin_status` VARCHAR(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$conn->query($createRegistrationsTable);

// Prepopulate database with mock Gujarat events if it's completely empty
$checkEvents = "SELECT COUNT(*) as count FROM tbl_public_events";
$res = $conn->query($checkEvents);
$row = $res->fetch_assoc();
if ($row['count'] == 0) {
    $mockEvents = [
        [9, "United Way Baroda Garba Festival 2026", "Garba & Cultural", "Vadodara", "United Way Grounds, Vasna Road, Vadodara", "2026-10-12", "19:00:00", 1200, "The largest and most iconic Navratri Garba festival in Vadodara. Come dance the night away with traditional tunes.", "19:00 - Entry starts | 20:00 - Garba begins | 23:30 - Maha Aarti | 00:00 - Event ends"],
        [9, "Ahmedabad Tech Expo 2026", "Business & Seminar", "Ahmedabad", "Gujarat University Exhibition Hall, Ahmedabad", "2026-08-15", "10:00:00", 0, "Explore the latest innovations in software, tech startups, AI development, and entrepreneurship in Ahmedabad.", "10:00 - Opening Keynote | 11:30 - Startup Pitch Round | 14:00 - Panel Discussion on AI agents | 17:00 - Networking Hour"],
        [9, "Surat Musical Concert Night", "Music Concert", "Surat", "Indoor Stadium, Surat", "2026-09-05", "18:00:00", 1500, "A sensational musical evening featuring Bollywood chartbusters live in the diamond city of Surat.", "18:00 - Gate Opens | 19:30 - Opening Act | 20:30 - Main Artist Performance | 23:00 - Concert wraps up"],
        [9, "Bhavnagar Local Art & Craft RSVP", "Local Workshop", "Bhavnagar", "Bhavnagar Town Hall, Bhavnagar", "2026-08-20", "11:00:00", 0, "A local community workshop showcasing Bhavnagar's traditional handicraft forms and pottery designs.", "11:00 - Craft Display | 12:30 - Live Pottery Class | 14:30 - Q&A Interactive session"]
    ];

    foreach ($mockEvents as $me) {
        $stmt = $conn->prepare("INSERT INTO tbl_public_events (organizer_id, event_name, category, district, venue, event_date, event_time, price, description, agenda) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssdss", $me[0], $me[1], $me[2], $me[3], $me[4], $me[5], $me[6], $me[7], $me[8], $me[9]);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
?>
