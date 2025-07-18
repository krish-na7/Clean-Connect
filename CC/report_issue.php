<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'localhost';
    $dbname = 'cleanconnect_db';
    $username = 'root';
    $password = '';

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $photo_name = '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_name = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_name);
    }

    $stmt = $conn->prepare("INSERT INTO issues (issue_type, description, photo, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdd", $issue_type, $description, $photo_name, $latitude, $longitude);

    if ($stmt->execute()) {
        echo "<script>alert('Issue reported successfully!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to report the issue. Please try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Issue - CleanConnect</title>

    <!-- Leaflet CSS & JS (No API key needed) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        .navbar {
            background: #2a9d8f;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1em;
        }

        .report-container {
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .report-container h1 {
            color: #2a9d8f;
            margin-bottom: 20px;
        }

        .report-container form {
            display: flex;
            flex-direction: column;
        }

        .report-container select, 
        .report-container textarea, 
        .report-container input[type="file"],
        .report-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .report-container input[type="submit"] {
            background-color: #2a9d8f;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .report-container input[type="submit"]:hover {
            background-color: #264653;
        }

        .map-container {
            width: 100%;
            height: 400px;
            margin: 20px 0;
            border-radius: 5px;
        }

        #preview {
            display: none;
            margin-top: 10px;
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
        }

        .footer {
            background-color: #2a9d8f;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="dashboard.php">Dashboard</a>
    <a href="report_issue.php">Report an Issue</a>
    <a href="profile.php">Profile</a>
    <a href="index.php">Logout</a>
</div>

<div class="report-container">
    <h1>Report an Issue</h1>
    <form action="report_issue.php" method="POST" enctype="multipart/form-data">
        <label for="issue_type">Issue Type</label>
        <select id="issue_type" name="issue_type" required>
            <option value="" disabled selected>Select Issue Type</option>
            <option value="litter">Litter</option>
            <option value="overflowing_bins">Overflowing Bins</option>
            <option value="illegal_dumping">Illegal Dumping</option>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Detailed description of the issue" rows="4" required></textarea>

        <label for="photo">Upload Photo</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        <img id="preview" />

        <!-- Hidden coordinates -->
        <input type="hidden" id="latitude" name="latitude" required>
        <input type="hidden" id="longitude" name="longitude" required>

        <!-- Map container -->
        <div id="map" class="map-container"></div>

        <input type="submit" value="Submit Report">
    </form>
</div>

<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> CleanConnect. All Rights Reserved.</p>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('map').fitWorld();

        // Load OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Locate user
        map.locate({ setView: true, maxZoom: 16 });

        var marker;

        function onLocationFound(e) {
            marker = L.marker(e.latlng).addTo(map)
                     .bindPopup("Your current location").openPopup();

            // Increase radius circle
            L.circle(e.latlng, 100).addTo(map);

            document.getElementById("latitude").value = e.latlng.lat;
            document.getElementById("longitude").value = e.latlng.lng;
        }

        function onLocationError(e) {
            alert("Location access denied or unavailable. Please enable location services.");
        }

        map.on('locationfound', onLocationFound);
        map.on('locationerror', onLocationError);

        // Image preview logic
        document.getElementById('photo').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

</body>
</html>
