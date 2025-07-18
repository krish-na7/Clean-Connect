<?php
// Start the session
session_start();

// Database connection setup
$host = 'localhost';
$dbname = 'cleanconnect_db';
$username = 'root';
$password = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle form submission for login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $user_type = $_POST['user_type'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password are entered
    if (!empty($username) && !empty($password)) {
        try {
            if ($user_type === 'citizen') {
                // Check citizen credentials
                $stmt = $conn->prepare("SELECT * FROM citizens WHERE username = :username OR email = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    // Correct password, store user data in session
                    $_SESSION['user_type'] = 'citizen';
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid username/email or password.";
                }
            } elseif ($user_type === 'authority') {
                // Check authority credentials
                $stmt = $conn->prepare("SELECT * FROM authorities WHERE username = :username OR email = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    // Correct password, store user data in session
                    $_SESSION['user_type'] = 'authority';
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: authority_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid username/email or password.";
                }
            } else {
                $error = "Please select a user type.";
            }
        } catch (Exception $e) {
            $error = "Error: Could not complete the login process.";
        }
    } else {
        $error = "Please enter both username/email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CleanConnect</title>
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
            max-width: 400px;
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
        .form-container input[type="password"], 
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
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

        .form-container .forgot-password {
            margin-top: 20px;
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

        /* Error Message */
        .error-message {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Login Form -->
    <div class="form-container">
        <h1>Login to CleanConnect</h1>
        <form method="POST" action="">
            <select name="user_type" required>
                <option value="" disabled selected>Select User Type</option>
                <option value="citizen">Citizen</option>
                <option value="authority">Authority</option>
            </select>
            <input type="text" name="username" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>

            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
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
