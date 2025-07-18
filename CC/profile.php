<?php
// Start the session
session_start();

// Database connection setup
$host = 'localhost';
$dbname = 'cleanconnect_db';
$username = 'root';
$password = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details based on the session
$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'];

try {
    if ($user_type === 'citizen') {
        $stmt = $conn->prepare("SELECT * FROM citizens WHERE id = :id");
    } else if ($user_type === 'authority') {
        $stmt = $conn->prepare("SELECT * FROM authorities WHERE id = :id");
    } else {
        throw new Exception("Invalid user type.");
    }
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact']; // Use contact_number to match the database
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    try {
        if ($user_type === 'citizen') {
            $stmt = $conn->prepare("UPDATE citizens SET username = :username, email = :email, contact_number = :contact_number, password = :password WHERE id = :id");
        } else if ($user_type === 'authority') {
            $stmt = $conn->prepare("UPDATE authorities SET username = :username, email = :email, contact_number = :contact_number, password = :password WHERE id = :id");
        }

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contact_number', $contact_number); // Updated parameter binding
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'profile.php'; // Replace 'profile.php' with your actual profile page URL
            </script>";
        } else {
            echo "<script>alert('Failed to update profile. Please try again.');</script>";
        }
        
    } catch (Exception $e) {
        echo "<script>alert('Error updating profile: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        if ($user_type === 'citizen') {
            $stmt = $conn->prepare("DELETE FROM citizens WHERE id = :id");
        } else if ($user_type === 'authority') {
            $stmt = $conn->prepare("DELETE FROM authorities WHERE id = :id");
        }

        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Destroy the session and redirect to the index page
        session_destroy();
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "<script>alert('Error deleting account: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CleanConnect</title>
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
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        /* Profile Container */
        .profile-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .profile-container h2 {
            text-align: center;
            color: #2a9d8f;
            margin-bottom: 20px;
        }

        .profile-container label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }

        .profile-container input[type="text"],
        .profile-container input[type="email"],
        .profile-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        .profile-container input[type="submit"] {
            background-color: #2a9d8f;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .profile-container input[type="submit"]:hover {
            background-color: #264653;
        }

        .delete-account {
            background-color: #e76f51;
            margin-top: 20px;
        }

        .profile-container .delete-account:hover {
            background-color: #d62828;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>My Profile</h2>
        <form method="POST" action="">
            <label for="user_type">User Type</label>
            <input type="text" id="user_type" name="user_type" value="<?php echo ucfirst($user_type); ?>" disabled>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>

            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" value="<?php echo isset($user['contact_number']) ? htmlspecialchars($user['contact_number']) : ''; ?>">

            <label for="password">Update Password</label>
            <input type="password" id="password" name="password" placeholder="New Password">

            <input type="submit" name="update_profile" value="Update Profile">
            <input type="submit" name="delete_account" value="Delete Account" class="delete-account" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
        </form>
    </div>
</body>
</html>
