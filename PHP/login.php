<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ====== Database Connection (Inline) ======
// Update these values according to your environment
$host = 'localhost';
$db   = 'ruaa_db';          // Your Ruaa database name
$user = 'root';             // Database username
$pass = 'root';             // Database password (update if needed)
// If you're using MAMP with port 8889, include it as the 5th parameter.
$conn = new mysqli($host, $user, $pass, $db, 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ====== Handle Log-In Form Submission ======
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // 1. Check required fields
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=All fields are required!");
        exit();
    }

    // 2. Fetch user from the database
    $stmt = $conn->prepare("SELECT Name, Email, Password, Role FROM user WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // 3. If exactly one user is found
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($name, $db_email, $db_password, $role);
        $stmt->fetch();

        // 4. Verify the password
        if (password_verify($password, $db_password)) {
            // Store session variables
            $_SESSION["user"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["role"] = $role;

            // 5. Redirect based on the user role
            if ($role == "organizer") {
                header("Location: organizer_page.html");
            } else {
                header("Location: Home_page.php");
            }
            exit();
        } else {
            header("Location: login.php?error=Incorrect password!");
            exit();
        }
    } else {
        header("Location: login.php?error=Email not found!");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ruaa | Log In</title>
    <!-- Link to the existing CSS file -->
    <link rel="stylesheet" href="Register_style.css">
</head>
<body>
    <!-- Background blur elements -->
    <span class="blur"></span>
    <span class="blur"></span>

    <!-- Main container with 'active' to keep sign-in visible -->
    <div class="container active" id="container">
        <!-- Left Panel (Purple) -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Log in to access your account and continue your journey with Ruaa.</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Sign-In Form -->
        <div class="form-container sign-in">
            <form action="login.php" method="POST">
                <h1>Log In</h1>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Log In</button>

                <!-- Display error message (in white, as styled in Register_style.css) -->
                <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>
