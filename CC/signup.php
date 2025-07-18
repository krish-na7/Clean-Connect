<?php
// Start session
session_start();

// Database connection setup
$host = 'localhost';
$dbname = 'cleanconnect_db';
$username = 'root';
$password = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission for signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_type'], $_POST['username'], $_POST['email'], $_POST['password'])) {
    $user_type = $_POST['user_type'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    try {
        if ($user_type == 'citizen') {
            // Insert citizen data into citizens table
            $stmt = $conn->prepare("INSERT INTO citizens (username, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, $contact_number, $address]);

            // Set session data
            $_SESSION['user_type'] = 'citizen';
            $_SESSION['username'] = $username;
            // Redirect to citizen dashboard
            header("Location: dashboard.php");
            exit();

        } elseif ($user_type == 'authority') {
            // Insert authority data into authorities table
            $stmt = $conn->prepare("INSERT INTO authorities (username, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, $contact_number, $address]);

            // Set session data
            $_SESSION['user_type'] = 'authority';
            $_SESSION['username'] = $username;
            // Redirect to authority dashboard
            header("Location: authority_dashboard.php");
            exit();
        }
    } catch (Exception $e) {
        echo "<script>alert('Error: Could not complete the registration.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CleanConnect</title>
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

        /* Container for the Form */
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
            margin: auto;
            flex: 1;
        }

        .form-container h1 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #2a9d8f;
        }

        .form-container input[type="text"], 
        .form-container input[type="email"], 
        .form-container input[type="password"], 
        .form-container select, 
        .form-container input[type="tel"], 
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-container input[type="checkbox"] {
            margin-right: 10px;
        }

        .form-container input[type="submit"] {
            background-color: #2a9d8f;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container input[type="submit"]:hover {
            background-color: #264653;
        }

        .form-container a {
            color: #2a9d8f;
            text-decoration: none;
            display: block;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .form-container .terms {
            margin-top: 20px;
            text-align: left;
        }

        .form-container .terms p {
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #555;
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
    <!-- Sign-Up Form -->
    <div class="form-container">
        <h1>Sign Up for CleanConnect</h1>
        <form method="POST">
            <select name="user_type" required>
                <option value="" disabled selected>Select User Type</option>
                <option value="citizen">Citizen</option>
                <option value="authority">Authority</option>
            </select>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="tel" name="contact_number" placeholder="Contact Number (Optional)">
            <textarea name="address" placeholder="Address (Optional)" rows="4"></textarea>
            <div class="terms">
                <input type="checkbox" name="terms" required>
                <label for="terms">I agree to the <a href="terms.html">Terms and Conditions</a></label>
            </div>
            <input type="submit" value="Sign Up">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 CleanConnect. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
        <p>Follow us on <a href="#">Social Media</a></p>
    </div>
</body>
</html>
