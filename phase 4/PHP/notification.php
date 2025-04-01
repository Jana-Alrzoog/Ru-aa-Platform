
<?php
session_start();
require_once 'connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    die('<p style="color: white; text-align: center;">Please login to view requests.</p>');
}

$currentUserEmail = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_notification'])) {
    $notification_id = $_POST['notification_id'];

    // ✅ جلب معلومات الدعوة المقبولة
    $query_notification = "SELECT team_name FROM notification WHERE id = ?";
    $stmt = $conn->prepare($query_notification);
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
    $result_notification = $stmt->get_result();

    if ($result_notification->num_rows > 0) {
        $row_notification = $result_notification->fetch_assoc();
        $team_name = $row_notification['team_name'];

        // ✅ تحديث حالة الدعوة إلى 'Accepted'
        $update_query = "UPDATE notification SET status = 'Accepted' WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("i", $notification_id);
        
if ($stmt_update->execute()) {
    // ✅ التحقق من العدد الحالي للفريق قبل القبول
    $check_team_query = "SELECT Team_Members, Max_Members FROM team WHERE Team_Name = ?";
    $stmt_check_team = $conn->prepare($check_team_query);
    $stmt_check_team->bind_param("s", $team_name);
    $stmt_check_team->execute();
    $result_team = $stmt_check_team->get_result();

    if ($result_team->num_rows > 0) {
        $row_team = $result_team->fetch_assoc();
        $current_members = $row_team['Team_Members'];
        $max_members = $row_team['Max_Members'];

        // ✅ تحقق إذا كان الفريق ممتلئ
        if ($current_members >= $max_members) {
            echo '<script>alert("The team is already full. Cannot accept more members! ❗"); window.location.href="notification.php";</script>';
        } else {
            // ✅ زيادة العدد إذا لم يصل الحد الأقصى
            $update_team_query = "UPDATE team SET Team_Members = Team_Members + 1 WHERE Team_Name = ?";
            $stmt_team = $conn->prepare($update_team_query);
            $stmt_team->bind_param("s", $team_name);
            if ($stmt_team->execute()) {
                echo '<script>alert("Notification accepted successfully! ✅"); window.location.href="notification.php";</script>';
            } else {
                echo '<script>alert("Error updating team members. ❗"); window.location.href="notification.php";</script>';
            }
        }
    } else {
        echo '<script>alert("Team not found. ❗"); window.location.href="notification.php";</script>';
    }
} else {
    echo '<script>alert("Error accepting notification. ❗"); window.location.href="notification.php";</script>';
}


}}


// ✅ معالجة رفض الإشعارات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_notification'])) {
    $notification_id = $_POST['notification_id'];

    $updateQuery = "UPDATE notification SET status = 'Rejected' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $notification_id);

    if ($stmt->execute()) {
        echo '<script>alert("Notification rejected successfully!"); window.location.href="notification.php";</script>';
    } else {
        echo '<script>alert("Error rejecting notification."); window.location.href="notification.php";</script>';
    }
}
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
                    <li class="link"><a href="Home_page.php">Home</a></li>
                    <li id="link1" class="link"><a href="event.php">Events</a></li>
                    <li id="link4" class="link"><a href="profile.php">Profile</a></li>
                </ul>
            </nav>

            <main class="main-content">
                <section class="card-container">
                    <?php
                    require_once 'connection.php';

                    // Get join requests for the current user based on Member_Email
                    $requestQuery = "SELECT Team_Name 
                           FROM Team_Member 
                           WHERE Member_Email = ? 
                           AND Status = 'Pending'";
                    $stmt = $conn->prepare($requestQuery);
                    $stmt->bind_param("s", $currentUserEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
// ✅ جلب الإشعارات للمستخدم الحالي من جدول notification
$notificationQuery = "SELECT id, event_title, team_name, status FROM notification WHERE email = ? AND status = 'Pending'";
                    $stmt2 = $conn->prepare($notificationQuery);
                    $stmt2->bind_param("s", $currentUserEmail);
                    $stmt2->execute();
                    $notificationResult = $stmt2->get_result();

if ($notificationResult->num_rows > 0) {
    while ($row = $notificationResult->fetch_assoc()) {
        echo '<article class="user-card">
                <div class="user-info">
                    <h3 class="username">Notification</h3>
                    <p class="user-email">You requested to join: ' . htmlspecialchars($row['event_title']) . ' with team: ' . htmlspecialchars($row['team_name']) . '</p>
                </div>
                <div class="action-buttons">
                    <form method="POST" action="notification.php">
                        <input type="hidden" name="notification_id" value="' . htmlspecialchars($row['id']) . '">
                        <button type="submit" class="accept-btn" name="accept_notification">Accept</button>
                    </form>
                    <form method="POST" action="notification.php">
                        <input type="hidden" name="notification_id" value="' . htmlspecialchars($row['id']) . '">
                        <button type="submit" class="reject-btn" name="reject_notification">Reject</button>
                    </form>
                </div>
            </article>';
    }
} else {
    echo '<p style="color: white; text-align: center;">No new notifications.</p>';
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
