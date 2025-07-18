<?php
session_start();
if ($_SESSION['user_type'] != 'authority') {
    header("Location: login.php");
    exit();
}

// Database connection details
$host = 'localhost';
$dbname = 'cleanconnect_db';
$username = 'root';
$password = '';

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update issue status if a request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'], $_POST['action'])) {
    $issue_id = $_POST['issue_id'];
    $new_status = $_POST['action'];
    
    // Prepare the statement to update the status of an issue
    $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $issue_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all issues
$result = $conn->query("SELECT * FROM issues ORDER BY date_reported DESC");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authority Dashboard - CleanConnect</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        /* Navigation Bar */
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

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Container for Dashboard */
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .dashboard-container h1 {
            color: #2a9d8f;
            margin-bottom: 20px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #264653;
            color: white;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-section select, .filter-section input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        /* Button Styling */
        .btn {
            padding: 10px 20px;
            background-color: #2a9d8f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #264653;
        }

        /* Footer */
        .footer {
            background-color: #2a9d8f;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
            position: relative;
            bottom: 0;
            margin-top: auto;
        }

        .footer a {
            color: #e76f51;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="authority_dashboard.php">Dashboard</a>
        <a href="issue_details.php">Manage Issues</a>
        <a href="admin.html">Statistics</a>
        <a href="profile.php">Profile</a>
        <a href="index.php">Logout</a>
    </div>

    <div class="dashboard-container">
        <h1>Authority Dashboard</h1>

        <table>
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date Reported</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo ucfirst($row['issue_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td><?php echo $row['date_reported']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                <button name="action" value="in_progress" class="btn">Mark as In Progress</button>
                                <button name="action" value="resolved" class="btn">Mark as Resolved</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
