<?php
session_start();
if ($_SESSION['user_type'] != 'citizen') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CleanConnect</title>
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

        /* Container for Dashboard Content */
        .dashboard-container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-container h1 {
            color: #2a9d8f;
            margin-bottom: 20px;
        }

        .report-list, .notifications, .profile {
            margin-bottom: 30px;
        }

        .report-list table, .notifications ul {
            width: 100%;
            border-collapse: collapse;
        }

        .report-list th, .report-list td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .report-list th {
            background-color: #2a9d8f;
            color: white;
        }

        .status-submitted {
            color: #f1c40f;
        }

        .status-in-progress {
            color: #f39c12;
        }

        .status-resolved {
            color: #2ecc71;
        }

        .notifications ul {
            list-style: none;
            padding: 0;
        }

        .notifications li {
            background: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .profile {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
        }

        .profile h2 {
            margin-bottom: 20px;
        }

        .profile input[type="text"], 
        .profile input[type="email"], 
        .profile input[type="tel"], 
        .profile textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .profile input[type="submit"] {
            background-color: #2a9d8f;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .profile input[type="submit"]:hover {
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
    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="report_issue.php">Report an Issue</a>
        <a href="profile.php">Profile</a>
        <a href="index.php">Logout</a>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <h1>Welcome to Your Dashboard</h1>

        <!-- Report List -->
        <div class="report-list">
            <h2>Your Submitted Reports</h2>
            <table>
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example Report Entries -->
                    <tr>
                        <td>12345</td>
                        <td>Overflowing bin at Main Street</td>
                        <td class="status-submitted">Submitted</td>
                        <td>2024-09-01</td>
                    </tr>
                    <tr>
                        <td>12346</td>
                        <td>Illegal dumping near park</td>
                        <td class="status-in-progress">In Progress</td>
                        <td>2024-09-05</td>
                    </tr>
                    <tr>
                        <td>12347</td>
                        <td>Litter in front of the school</td>
                        <td class="status-resolved">Resolved</td>
                        <td>2024-09-10</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Notifications -->
        <div class="notifications">
            <h2>Notifications</h2>
            <ul>
                <li>Your report ID 12346 has been updated to In Progress.</li>
                <li>New cleanliness initiative started in your area.</li>
            </ul>
        </div>

        <!-- Profile Section -->
        <div class="profile">
            <h2>Update Your Profile</h2>
            <form action="update_profile_process.html" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="tel" name="contact_number" placeholder="Contact Number">
                <textarea name="address" placeholder="Address" rows="4"></textarea>
                <input type="submit" value="Update Profile">
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 CleanConnect. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
        <p>Follow us on <a href="#">Social Media</a></p>
    </div>
</body>
</html>
