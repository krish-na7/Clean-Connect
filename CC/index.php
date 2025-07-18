<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanConnect - Home</title>
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
        }

        /* Navigation Bar */
        .navbar {
            background-color: #2a9d8f;
            padding: 15px 20px;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #2a9d8f, #264653);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .cta-buttons {
            margin-top: 20px;
        }

        .cta-buttons a {
            text-decoration: none;
            color: white;
            background-color: #e76f51;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 1.1em;
            margin: 0 10px;
            transition: background-color 0.3s ease;
        }

        .cta-buttons a:hover {
            background-color: #d64f37;
        }

        /* Content Section */
        .content {
            padding: 40px 20px;
            text-align: center;
        }

        .content h2 {
            font-size: 2em;
            color: #2a9d8f;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        /* Footer */
        .footer {
            background-color: #2a9d8f;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
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
        <a href="index.php">Home</a>
        <a href="about.html">About</a>
        <a href="contact.html">Contact</a>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome to CleanConnect</h1>
        <p>Revolutionizing urban cleanliness with smart solutions and community collaboration.</p>
        <div class="cta-buttons">
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content">
        <h2>Our Mission</h2>
        <p>At CleanConnect, our mission is to transform urban cleanliness and waste management through cutting-edge technology and engaged community participation. We believe that every citizen has a crucial role in maintaining a pristine and healthy environment.</p>
        <p>Our platform streamlines the process of reporting cleanliness issues—from litter and overflowing bins to illegal dumping. By offering an intuitive, user-friendly interface, we connect residents directly with local authorities, ensuring swift and effective responses.</p>
        <p>We are dedicated to bridging the gap between the community and municipal services through a collaborative approach. Our system provides real-time updates, fostering transparency and accountability while building trust between citizens and their local governments.</p>
        <p>Beyond addressing immediate cleanliness concerns, we are committed to promoting environmental stewardship. Our educational initiatives encourage sustainable behaviors and responsible waste management practices. By leveraging user feedback and data-driven insights, we continuously enhance our platform to meet the evolving needs of our cities.</p>
        <p>Join us in our mission to create greener, cleaner, and more vibrant urban spaces. Together, we can make a lasting impact and build a healthier future for all.</p>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 CleanConnect. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
        <p>Follow us on <a href="#">Social Media</a></p>
    </div>
</body>
</html>
