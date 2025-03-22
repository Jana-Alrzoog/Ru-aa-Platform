<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ====== Database Connection (MySQLi) ======
$host = 'localhost';
$db   = 'ruaa_db';
$user = 'root';
$pass = 'root';
// If you're using MAMP with port 8889, adjust accordingly
$conn = new mysqli($host, $user, $pass, $db, 8889);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ====== Handle Sign-Up Form Submission ======
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name             = trim($_POST["name"]);
    $email            = trim($_POST["email"]);
    $password         = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role             = $_POST["role"];  // 'participant' or 'organizer'

    // 1. Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        header("Location: signup.php?error=All fields are required!");
        exit();
    }

    // 2. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup.php?error=Invalid email format!");
        exit();
    }

    // 3. Validate password length (minimum 8 chars)
    if (strlen($password) < 8) {
        header("Location: signup.php?error=Password must be at least 8 characters!");
        exit();
    }

    // 4. Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: signup.php?error=Passwords do not match!");
        exit();
    }

    // 5. Check if the email is already registered
    $stmt = $conn->prepare("SELECT Email FROM User WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
header("Location: signup.php?error=Email+already+registered!");
        exit();
    }
    $stmt->close();

    // 6. Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 7. Insert the new user
    $stmt = $conn->prepare("INSERT INTO User (Name, Email, Password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Role-based redirect after successful sign-up
        if ($role === 'organizer') {
            header("Location: organizer_page.html?success=Account created successfully!");
        } else {
            // Default to participant
            header("Location: Home_page.php?success=Account created successfully!");
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Link to your external CSS file -->
  <link rel="stylesheet" href="Register_style.css">
  <title>Ruaa | Sign Up</title>
</head>
<body>
  <span class="blur"></span>
  <span class="blur"></span>
<div class="container active" id="container">
  <!-- Left Panel: Welcome Message (using toggle-container and toggle-panel classes) -->
  <div class="toggle-container">
    <div class="toggle">
      <div class="toggle-panel toggle-left">
        <h1>Welcome to Ruaa!</h1>
        <p>Join our community of innovators and start your journey with us.</p>
      </div>
    </div>
  </div>

  <!-- Right Panel: Sign-Up Form -->
  <div class="form-container sign-up">
    <form action="signup.php" method="post">
      <h1>Create Account</h1>
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      
      <!-- Role Selection Dropdown with id for JS validation -->
      <select name="role" required id="role">
        <option value="">Select Role</option>
        <option value="participant">Participant</option>
        <option value="organizer">Organizer</option>
      </select>
      
      <button type="submit" name="signup">Sign Up</button>
        <!-- Display Sign-Up Messages -->
        <?php
          if (isset($_GET['error'])) {
              echo "<p class='error-message'>" . htmlspecialchars($_GET['error']) . "</p>";
          }
          if (isset($_GET['success'])) {
              echo "<p class='success-message'>" . htmlspecialchars($_GET['success']) . "</p>";
          }
        ?>
    </form>
  
  </div>
</div>


      </form>

    </div>
  </div>
  <!-- JavaScript to validate that a role is selected before form submission -->
  <script>
    document.addEventListener("DOMContentLoaded", function(){
      const signUpForm = document.querySelector("form");
      signUpForm.addEventListener("submit", function(event){
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
