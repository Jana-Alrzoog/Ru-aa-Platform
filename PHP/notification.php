<?php
session_start();
require_once 'connection.php'; // Include your connection file

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    die('<p style="color: white; text-align: center;">Please login to view requests.</p>');
}



// Get current user from session
$currentUserEmail = $_SESSION['email'];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ruaa</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="notification.css" />
    <script src="notification.js" defer></script>
  </head>
  <body>
    <div class="container">
      <nav>
        <div class="nav-logo">
            <a href="#">
                <img src="images/logo_ruaa.png" alt="Ruaa Logo">
            </a>
        </div>

        <ul class="nav-links">
            <li class="link"><a href="Home page.html">Home</a></li>
            <li id="link1" class="link"><a href="event.html">Events</a></li>
            <li id="link4" class="link"><a href="profile.html">Profile</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <section class="card-container">
          <?php
          
          $servername = "localhost";
          $username = "root"; // replace with your database username
          $password = "root"; // replace with your database password
          $dbname = "ruaa_db";

          // Create connection
          $conn = new mysqli($servername, $username, $password, $dbname);

          // Check connection
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }

          // Assuming the logged-in user is a team leader
          // First get the team name for the current user
          if (isset($_SESSION['email'])) {
              $currentUserEmail = $_SESSION['email'];
              
              $teamQuery = "SELECT Team_Name FROM Team_Member WHERE LeaderName = 
                          (SELECT Name FROM User WHERE Email = '$currentUserEmail')";
              $teamResult = $conn->query($teamQuery);
              
              if ($teamResult && $teamResult->num_rows > 0) {
                  $teamRow = $teamResult->fetch_assoc();
                  $teamName = $teamRow['Team_Name'];
                  
                  // Now get all join requests for this team
                  $requestQuery = "SELECT u.Name, u.Email 
                                 FROM JoinRequest jr
                                 JOIN User u ON jr.Email = u.Email
                                 WHERE jr.Team_Name = '$teamName'";
                  $requestResult = $conn->query($requestQuery);
                  
                  if ($requestResult && $requestResult->num_rows > 0) {
                      while ($row = $requestResult->fetch_assoc()) {
                          echo '<article class="user-card" data-email="' . htmlspecialchars($row['Email']) . '">
                                  <div class="user-info">
                                      <img src="https://via.placeholder.com/50" alt="User" class="user-avatar" />
                                      <h3 class="username">' . htmlspecialchars($row['Name']) . '</h3>
                                      <p class="user-email">' . htmlspecialchars($row['Email']) . '</p>
                                  </div>
                                  <div class="action-buttons">
                                      <button class="accept-btn">Accept</button>
                                      <button class="reject-btn">Reject</button>
                                  </div>
                              </article>';
                      }
                  } else {
                      echo '<p style="color: white; text-align: center;">No team requests yet.</p>';
                  }
              } else {
                  echo '<p style="color: white; text-align: center;">You are not a team leader.</p>';
              }
          } else {
              echo '<p style="color: white; text-align: center;">Please login to view requests.</p>';
          }
          
          $conn->close();
          ?>
        </section>
      </main>

      <div class="copyright">
        Copyright © 2024 Ruaa. All Rights Reserved.
      </div>
    </div>
  </body>
</html>