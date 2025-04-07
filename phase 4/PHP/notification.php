<?php
session_start();
require_once 'connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['email'])) {
    die('<p style="color: white; text-align: center;">Please login to view notifications.</p>');
}

$currentUserEmail = $_SESSION['email'];

// ✅ التحقق إذا المستخدم عضو أو قائد في فريق بنفس الحدث
$check_membership = "SELECT * FROM team t 
                     LEFT JOIN team_member tm ON t.Team_Name = tm.Team_Name
                     WHERE t.Title = ? AND (t.Leader_Email = ? OR tm.Member_Email = ?)";

$check_stmt = $conn->prepare($check_membership);
$check_stmt->bind_param("sss", $event_title, $currentUserEmail, $currentUserEmail);
$check_stmt->execute();
$membership_result = $check_stmt->get_result();

if ($membership_result->num_rows > 0) {

    $delete_notification = "DELETE FROM notification WHERE id = ?";
    $stmt_delete = $conn->prepare($delete_notification);
    $stmt_delete->bind_param("i", $notification_id);
    $stmt_delete->execute();

    echo "<script>alert('You are already part of a team in this event. ❗');</script>";
    exit;
}

// معالجة قبول الدعوة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_notification'])) {
    $notification_id = $_POST['notification_id'];

    $query_notification = "SELECT team_name, email FROM notification WHERE id = ?";
    $stmt_notification = $conn->prepare($query_notification);
    $stmt_notification->bind_param("i", $notification_id);
    $stmt_notification->execute();
    $result_notification = $stmt_notification->get_result();

    if ($result_notification->num_rows > 0) {
        $row_notification = $result_notification->fetch_assoc();
        $team_name = $row_notification['team_name'];
        $invite_email = $row_notification['email'];
// ✅ جلب عنوان الحدث المرتبط بالفريق
        $get_event_query = "SELECT Title FROM team WHERE Team_Name = ?";
        $stmt_event = $conn->prepare($get_event_query);
        $stmt_event->bind_param("s", $team_name);
        $stmt_event->execute();
        $event_result = $stmt_event->get_result();
        $event_data = $event_result->fetch_assoc();
        $event_title = $event_data['Title'] ?? '';

        if (!empty($event_title)) {
            $check_membership = "SELECT * FROM team t 
                         LEFT JOIN team_member tm ON t.Team_Name = tm.Team_Name
                         WHERE t.Title = ? AND (t.Leader_Email = ? OR tm.Member_Email = ?)";
            $stmt_check = $conn->prepare($check_membership);
            $stmt_check->bind_param("sss", $event_title, $invite_email, $invite_email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {

                $delete_notification = "DELETE FROM notification WHERE id = ?";
                $stmt_delete = $conn->prepare($delete_notification);
                $stmt_delete->bind_param("i", $notification_id);
                $stmt_delete->execute();

                echo '<script>alert("You are already part of a team in this event. ❗"); window.location.href="notification.php";</script>';
                exit();
            }
        }

        // Check if team exists
        $check_team_exists_query = "SELECT * FROM team WHERE Team_Name = ?";
        $stmt_check_team = $conn->prepare($check_team_exists_query);
        $stmt_check_team->bind_param("s", $team_name);
        $stmt_check_team->execute();
        $result_check_team = $stmt_check_team->get_result();

        if ($result_check_team->num_rows == 0) {

            $delete_notification = "DELETE FROM notification WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_notification);
            $stmt_delete->bind_param("i", $notification_id);
            $stmt_delete->execute();

            echo '<script>alert("Team not found. Cannot add member! ❗"); window.location.href="notification.php";</script>';
            exit();
        }

        // Get current accepted members count
        $count_members_query = "SELECT COUNT(*) AS current_members FROM team_member WHERE Team_Name = ?";
        $stmt_count = $conn->prepare($count_members_query);
        $stmt_count->bind_param("s", $team_name);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        $current_members = $row_count['current_members'];

        // Get max allowed members
        $max_query = "SELECT Max_Members FROM team WHERE Team_Name = ?";
        $stmt_max = $conn->prepare($max_query);
        $stmt_max->bind_param("s", $team_name);
        $stmt_max->execute();
        $result_max = $stmt_max->get_result();
        $row_max = $result_max->fetch_assoc();
        $max_members = $row_max['Max_Members'];

        if ($current_members >= $max_members) {
            // Team is full → reject notification
            $reject_query = "UPDATE notification SET status = 'Rejected' WHERE id = ?";
            $stmt_reject = $conn->prepare($reject_query);
            $stmt_reject->bind_param("i", $notification_id);
            $stmt_reject->execute();
            $delete_notification = "DELETE FROM notification WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_notification);
            $stmt_delete->bind_param("i", $notification_id);
            $stmt_delete->execute();

            echo '<script>alert("Sorry, this team is already full! ❌"); window.location.href="notification.php";</script>';
            exit();
        }

        // Check if member already in team
        $check_member_query = "SELECT * FROM team_member WHERE Team_Name = ? AND Member_Email = ?";
        $stmt_check_member = $conn->prepare($check_member_query);
        $stmt_check_member->bind_param("ss", $team_name, $invite_email);
        $stmt_check_member->execute();
        $result_member = $stmt_check_member->get_result();

        if ($result_member->num_rows == 0) {
            $insert_member_query = "INSERT INTO team_member (Team_Name, Member_Email) VALUES (?, ?)";
            $stmt_member = $conn->prepare($insert_member_query);
            $stmt_member->bind_param("ss", $team_name, $invite_email);
            $stmt_member->execute();

            if ($stmt_member->affected_rows > 0) {
                $update_team_query = "UPDATE team SET Team_Members = Team_Members + 1 WHERE Team_Name = ?";
                $stmt_team = $conn->prepare($update_team_query);
                $stmt_team->bind_param("s", $team_name);
                $stmt_team->execute();

                // ✅ Now update the notification status
                $update_notification_query = "UPDATE notification SET status = 'Accepted' WHERE id = ?";
                $stmt_update = $conn->prepare($update_notification_query);
                $stmt_update->bind_param("i", $notification_id);
                $stmt_update->execute();

                echo '<script>alert("Invitation accepted successfully! ✅"); window.location.href="notification.php";</script>';
                exit();
            }
        } else {
            echo '<script>alert("You are already in the team!"); window.location.href="notification.php";</script>';
            exit();
        }
    }
}

// معالجة رفض الدعوة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_notification'])) {
    $notification_id = $_POST['notification_id'];
    $update_query = "UPDATE notification SET status = 'Rejected' WHERE id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("i", $notification_id);
    if ($stmt_update->execute()) {
        echo '<script>alert("Notification rejected successfully!"); window.location.href="notification.php";</script>';
    } else {
        echo '<script>alert("Error rejecting the notification. ❗"); window.location.href="notification.php";</script>';
    }
}

// جلب إشعارات الإلغاء
$cancelQuery = "SELECT message, created_at FROM cancellation_notification WHERE email = ? ORDER BY created_at DESC";
$cancelStmt = $conn->prepare($cancelQuery);
$cancelStmt->bind_param("s", $currentUserEmail);
$cancelStmt->execute();
$cancelResults = $cancelStmt->get_result();
$cancelStmt->close();

// جلب إشعارات الفريق
$notificationQuery = "SELECT id, event_title, team_name, status FROM notification WHERE email = ? AND status = 'Pending'";
$stmt2 = $conn->prepare($notificationQuery);
$stmt2->bind_param("s", $currentUserEmail);
$stmt2->execute();
$notificationResult = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Notifications</title>
        <link rel="stylesheet" href="notification.css">
    </head>
    <body>
        <div class="container">
            <nav>
                <div class="nav-logo">
                    <a href="home.php"><img src="images/logo_ruaa.png" alt="Logo"></a>
                </div>
                <ul class="nav-links">
                    <li class="link"><a href="event.php">Events</a></li>
                    <li class="link"><a href="profile.php">Profile</a></li>
                    <li class="link"><a href="logout.php">Logout</a></li>
                    <li class="link"><a href="notification.php">🔔</a></li>
                </ul>
            </nav>

            <div class="main-content">

                <!-- إشعارات إلغاء الأحداث -->
                <div class="card-container">
                    <h2 style="color:white; margin-bottom:20px;">Cancelled Event Notifications</h2>
<?php if ($cancelResults->num_rows > 0): ?>
                        <ul class="notification-list">
    <?php while ($row = $cancelResults->fetch_assoc()): ?>
                                <li class="user-card notification">
                                    <div class="user-info">

                                        <div>
                                            <p class="username"><?php echo htmlspecialchars($row['message']); ?></p>
                                            <small style="color:#555;"><?php echo $row['created_at']; ?></small>
                                        </div>
                                    </div>
                                </li>
    <?php endwhile; ?>
                        </ul>
<?php else: ?>
                        <p style="color:white;">No cancelled event notifications.</p>
                        <?php endif; ?>
                </div>

                <!-- إشعارات الفريق -->
                <div class="card-container">
                    <h2 style="color:white; margin-bottom:20px;">Team Invitations</h2>
<?php if ($notificationResult->num_rows > 0): ?>
    <?php while ($row = $notificationResult->fetch_assoc()): ?>
                            <article class="user-card">
                                <div class="user-info">
                                    <h3 class="username">Notification</h3>
                                    <p class="user-email">You requested to join: <?php echo htmlspecialchars($row['event_title']); ?> with team: <?php echo htmlspecialchars($row['team_name']); ?></p>
                                </div>
                                <div class="action-buttons">
                                    <form method="POST" action="notification.php">
                                        <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="accept-btn" name="accept_notification">Accept</button>
                                    </form>
                                    <form method="POST" action="notification.php">
                                        <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="reject-btn" name="reject_notification">Reject</button>
                                    </form>
                                </div>
                            </article>
    <?php endwhile; ?>
<?php else: ?>
                        <p style="color:white;">No team-related notifications.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div class="copyright">
                <p>&copy; 2025 Ru'aa Platform. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
