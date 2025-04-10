<?php
session_start();

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
                <li class="link"><a href="event.php">Events</a></li>
                <li class="link"><a href="profile.php">Profile</a></li>
                <li class="link"><a href="logout.php">Log out</a></li>
                <li class="link"><a href="notification.php">🔔</a></li>
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
                include 'connection.php';
                $currentUser = $_SESSION['email'];
            $sql = "
    SELECT DISTINCT e.Title, e.Date, e.Location, e.Type, 'Registered' as Status
    FROM event e
    LEFT JOIN team t ON e.Title = t.Title
    LEFT JOIN team_member tm ON tm.Team_Name = t.Team_Name
    WHERE t.Leader_Email = ? OR tm.Member_Email = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $currentUser, $currentUser);

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        $statusClass = strtolower($row['Status']);
                ?>
                    <li class="event-item <?php echo $statusClass; ?>">
                        <div class="event-details" onclick="window.location='event.php?title=<?php echo urlencode($row['Title']); ?>'">
                            <h3 class="event-name"><?php echo htmlspecialchars($row['Title']); ?></h3>
                            <p class="event-date"><?php echo htmlspecialchars(date("F j, Y", strtotime($row['Date']))); ?></p>
                            <p class="event-location"><?php echo htmlspecialchars($row['Location']); ?></p>
                            <p class="event-type"><?php echo htmlspecialchars($row['Type']); ?></p>
                            <span class="event-status"><?php echo htmlspecialchars($row['Status']); ?></span>
                        </div>
                        <button class="remove-event-btn" data-title="<?php echo htmlspecialchars($row['Title']); ?>">Remove</button>
                    </li>
                <?php
                    endwhile;
                else:
                ?>
                    <div class="no-events-message" id="noEventsMessage">You have no registered events.</div>
                <?php
                endif;
                $stmt->close();
                ?>
            </ul>
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

        <section class="profile-card">
    <h2>My Teams</h2>
    <div id="team-members" class="team-members-container">
         <?php
// ✅ استعلام يعرض الفرق اللي المستخدم قائد فيها
$leaderTeamsQuery = "SELECT Team_Name, 'leader' as role, Title FROM team WHERE Leader_Email = ?";
$leaderStmt = $conn->prepare($leaderTeamsQuery);
$leaderStmt->bind_param("s", $currentUser);
$leaderStmt->execute();
$leaderTeamsResult = $leaderStmt->get_result();

if ($leaderTeamsResult->num_rows > 0) {
    while ($team = $leaderTeamsResult->fetch_assoc()) {
        echo '<div class="team-section">';
        echo '<h3>'.htmlspecialchars($team['Team_Name']).' <span class="team-event">('.htmlspecialchars($team['Title']).')</span></h3>';

        $membersSql = "SELECT u.Name, u.Email,
                      CASE WHEN t.Leader_Email = u.Email THEN 'Leader' ELSE 'Member' END as Role
                      FROM team_member tm
                      JOIN user u ON tm.Member_Email = u.Email
                      JOIN team t ON tm.Team_Name = t.Team_Name
                      WHERE tm.Team_Name = ?";
        $membersStmt = $conn->prepare($membersSql);
        $membersStmt->bind_param("s", $team['Team_Name']);
        $membersStmt->execute();
        $membersResult = $membersStmt->get_result();

echo '<div class="team-member-list" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">';
        while ($member = $membersResult->fetch_assoc()) {
            echo '<div class="team-member-card">';
            echo '<div class="team-member-avatar"></div>';
            echo '<div class="team-member-info">';
            echo '<p class="team-member-name">'.htmlspecialchars($member['Name']).'</p>';
            echo '<p class="team-member-email">'.htmlspecialchars($member['Email']).'</p>';
            echo '<p class="member-role">'.htmlspecialchars($member['Role']).'</p>';




            echo '</div></div>';
        }
        echo '</div>';
        echo '</div>';
        $membersStmt->close();
    }
}
$leaderStmt->close();
?>

        <?php
       
        
  $teamsQuery = "(
   SELECT tm.Team_Name, t.Title, t.Leader_Email
   FROM team_member tm
   JOIN team t ON tm.Team_Name = t.Team_Name
   WHERE tm.Member_Email = ? AND t.Leader_Email != ?
)";

        
        $stmt = $conn->prepare($teamsQuery);
        $stmt->bind_param("ss", $currentUser, $currentUser);
        $stmt->execute();
        $teamsResult = $stmt->get_result();
        
        if ($teamsResult->num_rows > 0) {
            while ($team = $teamsResult->fetch_assoc()) {
                echo '<div class="team-section">';
                echo '<h3>'.htmlspecialchars($team['Team_Name']).' <span class="team-event">('.htmlspecialchars($team['Title']).')</span></h3>';
                
                $membersSql = "SELECT u.Name, u.Email,
                              CASE WHEN t.Leader_Email = u.Email THEN 'Leader' ELSE 'Member' END as Role
                              FROM team_member tm
                              JOIN user u ON tm.Member_Email = u.Email
                              JOIN team t ON tm.Team_Name = t.Team_Name
                              WHERE tm.Team_Name = ? ";
                
                $membersStmt = $conn->prepare($membersSql);
                $membersStmt->bind_param("s", $team['Team_Name']);
                $membersStmt->execute();
                $membersResult = $membersStmt->get_result();
                
                echo '<div class="team-member-list">'; // ➕ نضيف هذا السطر قبل اللوب

while ($member = $membersResult->fetch_assoc()) {
    echo '<div class="team-member-card">';
    echo '<div class="team-member-avatar"></div>';
    echo '<div class="team-member-info">';
    echo '<p class="team-member-name">'.htmlspecialchars($member['Name']).'</p>';
    echo '<p class="team-member-email">'.htmlspecialchars($member['Email']).'</p>';
    echo '<p class="member-role">'.htmlspecialchars($member['Role']).'</p>';



    echo '</div></div>'; // end team-member-info + card
}

                echo '</div>';
                $membersStmt->close();
            }
        } else {
            echo '<p style="color: white; text-align: center;">No teams yet.</p>';
        }
        $stmt->close();
        ?>
    </div>
</section>

        <footer class="copyright">
            Copyright © 2024 Ruaa. All Rights Reserved.
        </footer>
    </div>

    <script src="profile.js"></script>
    <script>
        document.querySelectorAll('.remove-event-btn').forEach(button => {
    button.addEventListener('click', () => {
        const title = button.dataset.title;

        if (confirm(`Are you sure you want to delete the event "${title}" and all related data?`)) {
            fetch('delete_event_Logic.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `title=${encodeURIComponent(title)}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload(); // يعيد تحميل الصفحة لتحديث My Registered Events و My Teams
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. ❗');
            });
        }
    });
});

    </script>
</body>
</html>