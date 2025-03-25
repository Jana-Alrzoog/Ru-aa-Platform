<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ====== Database Connection (MySQLi) ======
// Update these values according to your environment
include 'connection.php';

// ====== Handle Sign-Up Form Submission ======
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Collect form data
    $name             = trim($_POST["name"]);
    $email            = trim($_POST["email"]);
    $password         = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role             = $_POST["role"];  // 'participant' or 'organizer'

    // 2. Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        // Pass the error message as a GET parameter
        header("Location: signup.php?error=All+fields+are+required!");
        exit();
    }

    // 3. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup.php?error=Invalid+email+format!");
        exit();
    }

    // 4. Validate password length (minimum 8 characters)
    if (strlen($password) < 8) {
        header("Location: signup.php?error=Password+must+be+at+least+8+characters!");
        exit();
    }

    // 5. Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: signup.php?error=Passwords+do+not+match!");
        exit();
    }

    // 6. Check if the email is already registered
    $stmt = $conn->prepare("SELECT Email FROM User WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // If email exists, redirect with an error
        header("Location: signup.php?error=Email+already+registered!");
        exit();
    }
    $stmt->close();

    // 7. Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 8. Insert the new user
    $stmt = $conn->prepare("INSERT INTO User (Name, Email, Password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // On success, redirect based on role
        if ($role === 'organizer') {
            header("Location: organizer_page.php?success=Account+created+successfully!");
        } else {
            header("Location: Home_page.php?success=Account+created+successfully!");
        }
        exit();
    } else {
        echo "Error inserting record: " . $stmt->error;
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
  <title>Ruaa | Sign Up</title>
  <!-- Link to your external CSS file -->
  <link rel="stylesheet" href="Register_style.css">
</head>
<body>
  <!-- Optional blurred background elements -->
  <span class="blur"></span>
  <span class="blur"></span>

  <!-- Main container (no toggle/animation) -->
  <div class="signup-container">
    
    <!-- Left panel: purple background with welcome text -->
    <div class="signup-left">
        
      <h1>Welcome to Ruaa!</h1>
      <p>Join our community of innovators and start your journey with us.</p>
    </div>
    
    <!-- Right panel: white background with the sign-up form -->
    <div class="signup-right">
      <form action="signup.php" method="POST">
        <h1>Create Account</h1>
        
        <!-- Form fields -->
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <select name="role" required id="role">
          <option value="">Select Role</option>
          <option value="participant">Participant</option>
          <option value="organizer">Organizer</option>
        </select>

        <button type="submit" name="signup">Sign Up</button>
        
        <!-- Display error or success messages -->
        <?php
          if (isset($_GET['error'])) {
              // .error-message class in CSS will make text white
              echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
          }
          if (isset($_GET['success'])) {
              // .success-message class in CSS can make text green
              echo '<p class="success-message">' . htmlspecialchars($_GET['success']) . '</p>';
          }
        ?>
      </form>
    </div>
  </div>

  <!-- Simple JS to ensure user selects a role -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const signUpForm = document.querySelector(".signup-right form");
      signUpForm.addEventListener("submit", function(event) {
        const roleSelect = document.getElementById("role");
        if (!roleSelect.value) {
          alert("Please select a role before signing up!");
          event.preventDefault();
        }
      });
    });
  </script>
</body>
</html>