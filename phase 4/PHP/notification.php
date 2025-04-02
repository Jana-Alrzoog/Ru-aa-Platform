
<?php
session_start();
require_once 'connection.php';

// ✅ تفعيل الأخطاء للتصحيح أثناء التطوير
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ التحقق من تسجيل الدخول
if (!isset($_SESSION['email'])) {
    die('<p style="color: white; text-align: center;">Please login to view requests.</p>');
}

$currentUserEmail = $_SESSION['email'];

// ✅ معالجة قبول الدعوة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_notification'])) {
    $notification_id = $_POST['notification_id'];

    // ✅ جلب معلومات الدعوة المقبولة
    $query_notification = "SELECT team_name, email FROM notification WHERE id = ?";
    $stmt_notification = $conn->prepare($query_notification);
    $stmt_notification->bind_param("i", $notification_id);
    $stmt_notification->execute();
    $result_notification = $stmt_notification->get_result();

    if ($result_notification->num_rows > 0) {
        $row_notification = $result_notification->fetch_assoc();
        $team_name = $row_notification['team_name'];
        $invite_email = $row_notification['email'];

        // ✅ تحديث حالة الدعوة إلى 'Accepted'
        $update_notification_query = "UPDATE notification SET status = 'Accepted' WHERE id = ?";
        $stmt_update = $conn->prepare($update_notification_query);
        $stmt_update->bind_param("i", $notification_id);

        if ($stmt_update->execute()) {
        
// ✅ التحقق إذا الفريق موجود قبل إدخال العضو في team_member
$check_team_exists_query = "SELECT * FROM team WHERE Team_Name = ?";
$stmt_check_team = $conn->prepare($check_team_exists_query);
$stmt_check_team->bind_param("s", $team_name);
$stmt_check_team->execute();
$result_check_team = $stmt_check_team->get_result();

if ($result_check_team->num_rows == 0) {
    echo '<script>alert("Team not found. Cannot add member! ❗"); window.location.href="notification.php";</script>';
    exit();
}


// ✅ التحقق إذا العضو موجود قبل إضافته لـ team_member
$check_member_query = "SELECT * FROM team_member WHERE Team_Name = ? AND Member_Email = ?";
$stmt_check_member = $conn->prepare($check_member_query);
$stmt_check_member->bind_param("ss", $team_name, $invite_email);
$stmt_check_member->execute();
$result_member = $stmt_check_member->get_result();

if ($result_member->num_rows == 0) {
    // ✅ إذا ما كان موجود، ندخله
    $insert_member_query = "INSERT INTO team_member (Team_Name, Member_Email) VALUES (?, ?)";
    $stmt_member = $conn->prepare($insert_member_query);
    $stmt_member->bind_param("ss", $team_name, $invite_email);
    $stmt_member->execute();
}


// ✅ زيادة عدد الأعضاء فقط إذا تمت إضافة العضو بنجاح
if ($stmt_member->affected_rows > 0) {
    $update_team_query = "UPDATE team SET Team_Members = Team_Members + 1 WHERE Team_Name = ?";
    $stmt_team = $conn->prepare($update_team_query);
    $stmt_team->bind_param("s", $team_name);
    $stmt_team->execute();
}

            echo '<script>alert("Invitation accepted successfully! ✅"); window.location.href="notification.php";</script>';
        } else {
            echo '<script>alert("Error while accepting the invitation. ❗"); window.location.href="notification.php";</script>';
        }
    } else {
        echo '<script>alert("Notification not found. ❗"); window.location.href="notification.php";</script>';
    }
}

// ✅ معالجة رفض الدعوة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_notification'])) {
    $notification_id = $_POST['notification_id'];

    // ✅ تحديث حالة الدعوة إلى 'Rejected'
    $update_query = "UPDATE notification SET status = 'Rejected' WHERE id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("i", $notification_id);

    if ($stmt_update->execute()) {
        echo '<script>alert("Notification rejected successfully!"); window.location.href="notification.php";</script>';
    } else {
        echo '<script>alert("Error rejecting the notification. ❗"); window.location.href="notification.php";</script>';
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
                 FROM team_member 
                 WHERE Member_Email = ?";

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

