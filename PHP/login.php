<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if required fields are provided
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=All fields are required!");
        exit();
    }

    // Prepare SQL statement to fetch user details including Experiences
    $stmt = $conn->prepare("SELECT Name, Email, Password, Role, Experiences FROM user WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If exactly one user is found
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($name, $db_email, $db_password, $role, $experiences);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $db_password)) {
            // Store session variables (including experiences)
            $_SESSION["user"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["role"] = $role;
            $_SESSION["experiences"] = $experiences; // May be null if not yet updated
            if ($role == "organizer") {
                header("Location: organizer_page.php");
            } else {
                header("Location: profile.php");
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
    <link rel="stylesheet" href="Log-in.css">
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
                    <br><br><br><br><br><br><br><br>
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

                <!-- Display error message -->
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