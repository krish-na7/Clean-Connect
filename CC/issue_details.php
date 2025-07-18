<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'cleanconnect_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'], $_POST['status'])) {
    $issue_id = $_POST['issue_id'];
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $issue_id);
    if ($stmt->execute()) {
        echo "<script>alert('Status updated successfully.'); window.location.href = 'issue_details.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update status.');</script>";
    }
    $stmt->close();
}

// Fetch all issues
$stmt = $conn->prepare("SELECT id, issue_type, description, photo, latitude, longitude, date_reported, status FROM issues ORDER BY date_reported DESC");
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "<script>alert('No issues found.'); window.location.href = 'authority_dashboard.php';</script>";
    exit();
}

$stmt->bind_result($id, $issue_type, $description, $photo, $latitude, $longitude, $date_reported, $status);
$issues = [];
while ($stmt->fetch()) {
    $issues[] = [
        'id' => $id,
        'issue_type' => $issue_type,
        'description' => $description,
        'photo' => $photo,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'date_reported' => $date_reported,
        'status' => $status
    ];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Details - CleanConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
        }
        .navbar {
            background: #2a9d8f;
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-weight: bold;
        }
        .navbar a:hover { text-decoration: underline; }

        .dashboard-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .dashboard-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2a9d8f;
        }

        .issue-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            background: #fff;
        }

        .issue-card h2 {
            color: #2a9d8f;
        }

        .issue-card img {
            max-width: 300px;
            margin-top: 10px;
            border-radius: 8px;
        }

        .status-submitted { color: #f1c40f; font-weight: bold; }
        .status-in-progress { color: #f39c12; font-weight: bold; }
        .status-resolved { color: #2ecc71; font-weight: bold; }

        .status-update select,
        .status-update button {
            padding: 10px;
            margin-right: 10px;
            margin-top: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        .status-update button {
            background: #2a9d8f;
            color: white;
            border: none;
            cursor: pointer;
        }

        .status-update button:hover {
            background: #264653;
        }

        #map {
            height: 300px;
            width: 100%;
            border-radius: 10px;
            margin-top: 15px;
        }

        .footer {
            background: #2a9d8f;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .footer a {
            color: #e76f51;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="authority_dashboard.php">Dashboard</a>
    <a href="report_issue.php">Report an Issue</a>
    <a href="profile.php">Profile</a>
    <a href="index.php">Logout</a>
</div>

<div class="dashboard-container">
    <h1>All Issues</h1>
    <?php foreach ($issues as $issue): ?>
        <div class="issue-card">
            <h2>Issue ID: <?php echo htmlspecialchars($issue['id']); ?></h2>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($issue['issue_type']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($issue['description'])); ?></p>
            <p><strong>Location:</strong> Lat: <?php echo htmlspecialchars($issue['latitude']); ?>, Lng: <?php echo htmlspecialchars($issue['longitude']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($issue['date_reported']); ?></p>
            <p><strong>Status:</strong>
                <?php
                    switch ($issue['status']) {
                        case 'submitted': echo "<span class='status-submitted'>Submitted</span>"; break;
                        case 'in_progress': echo "<span class='status-in-progress'>In Progress</span>"; break;
                        case 'resolved': echo "<span class='status-resolved'>Resolved</span>"; break;
                    }
                ?>
            </p>

            <?php if (!empty($issue['photo'])): ?>
                <img src="<?php echo htmlspecialchars($issue['photo']); ?>" alt="Issue Image">
            <?php else: ?>
                <p><em>No image uploaded for this issue.</em></p>
            <?php endif; ?>

            <div id="map<?php echo $issue['id']; ?>" style="height: 300px;"></div>

            <?php if ($issue['status'] !== 'resolved'): ?>
                <div class="status-update">
                    <form method="POST" action="issue_details.php">
                        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                        <select name="status" required>
                            <option value="submitted" <?php if ($issue['status'] === 'submitted') echo 'selected'; ?>>Submitted</option>
                            <option value="in_progress" <?php if ($issue['status'] === 'in_progress') echo 'selected'; ?>>In Progress</option>
                            <option value="resolved" <?php if ($issue['status'] === 'resolved') echo 'selected'; ?>>Resolved</option>
                        </select>
                        <button type="submit">Update Status</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="footer">
    <p>&copy; 2025 CleanConnect | <a href="privacy.php">Privacy Policy</a></p>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const issues = <?php echo json_encode($issues); ?>;

    issues.forEach((issue, index) => {
        setTimeout(() => {
            const map = L.map('map' + issue.id).setView([issue.latitude, issue.longitude], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([issue.latitude, issue.longitude])
                .addTo(map)
                .bindPopup("Issue ID: " + issue.id)
                .openPopup();
        }, 100 * index);
    });
});
</script>

</body>
</html>
