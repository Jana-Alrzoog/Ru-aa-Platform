<?php
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
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
                <li class="link"><a href="profile.html">Profile</a></li>
                <li class="link"><a href="logout.php">Log out</a></li>
                <li class="link"><a href="notification.html">🔔</a></li>
    
            </ul>
        </nav>

        <section class="profile-card">
            <div class="profile-content">
                <div class="input-group">
                    <label class="label">UserName:</label>
                    <p id="username-display" class="input-field"><?php echo htmlspecialchars($username); ?></p>
                </div>
                <div class="input-group">
                    <label class="label">Email:</label>
                    <p id="email-display" class="input-field"><?php echo htmlspecialchars($email); ?></p>

                </div>
                <div class="input-group">
                    <label class="label">Experiences:</label>
                    <p id="experiences-display" class="input-field">programming <br> UX</p>
                </div>
            </div>
            <div class="profile-image-container">
                <div class="profile-image" id="profileImagePreview">
                    <span class="upload-icon">+</span>
                </div>
                <input type="file" id="profileImageInput" accept="image/*" class="hidden-input">
                <button class="upload-button" id="uploadTrigger">Upload Photo</button>
            </div>
        </section>

        <div class="button-group">
            <button class="action-button" id="showEventsBtn">My Registered Events</button>
            <button class="action-button" id="editProfileBtn">Edit Profile</button>
        </div>

        <section class="registered-events" id="registeredEvents">
            <h2 class="events-title">My Registered Events</h2>
<ul class="events-list" id="eventsList">
<?php
include 'connection.php'; // ✅ your connection file

$currentUser = $_SESSION['email']; // get the logged-in user's email

$sql = "SELECT e.Title, e.Date 
        FROM ShapeParticipant sp 
        JOIN Event e ON sp.Title = e.Title 
        WHERE sp.Email = ? AND sp.Status = 'Approved'";


$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <li class="event-item">
        <div class="event-details">
            <h3 class="event-name"><?php echo htmlspecialchars($row['Title']); ?></h3>
            <p class="event-date"><?php echo htmlspecialchars(date("F j, Y", strtotime($row['Date']))); ?></p>
        </div>
        <button class="remove-event-btn">Remove</button>
    </li>
<?php
    endwhile;
else:
?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("eventsList").style.display = "none";
            document.getElementById("noEventsMessage").style.display = "block";
        });
    </script>
<?php
endif;

$stmt->close();
?>
</ul>

            <div class="no-events-message" id="noEventsMessage">You have no registered events.</div>
        </section>

        <div class="edit-profile-modal" id="editProfileModal">
            <div class="modal-content">
                <span class="close-modal" id="closeModal">&times;</span>
                <h2 class="modal-title">Edit Profile</h2>
                <form id="editProfileForm">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($username); ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="experiences">Experiences:</label>
                        <textarea id="experiences" class="form-input">programming\nUX</textarea>
                    </div>
                    <button type="submit" class="save-button">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- My Team Members Section -->
        <section class="profile-card">
            <h2>My Team Members</h2>
            <div id="team-members" class="team-members-container"></div>
        </section>
        <footer class="copyright">
            Copyright © 2024 Ruaa. All Rights Reserved.
        </footer>
    </div>

    <script src="profile.js"></script>
</body>
</html>