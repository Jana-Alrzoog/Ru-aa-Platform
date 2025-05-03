<?php
session_start();
include("connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['event_id'])) {
    $eventTitle = $_GET['event_id'];

    // جلب عنوان الحدث
    $getTitleQuery = "SELECT Title FROM Event WHERE Title = ?";
    $stmt = $conn->prepare($getTitleQuery);
    $stmt->bind_param("s", $eventTitle);
    $stmt->execute();
    $stmt->bind_result($event_title);
    $stmt->fetch();
    $stmt->close();

    // جلب إيميلات المسجلين
    $registeredQuery = "SELECT Email FROM Registration WHERE Team_Name IN (
        SELECT Team_Name FROM Team WHERE Title = ?
    )";
    $stmt = $conn->prepare($registeredQuery);
    $stmt->bind_param("s", $eventTitle);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // تجهيز استعلام الإشعار فقط إذا فيه مشاركين
        $insertNotificationQuery = "INSERT INTO cancellation_notification (email, event_title, message) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertNotificationQuery);

        while ($row = $result->fetch_assoc()) {
            $email = $row['Email'];
            $message = "The event '$event_title' has been cancelled.";
            $insertStmt->bind_param("sss", $email, $event_title, $message);
            $insertStmt->execute();
        }

        $insertStmt->close();
    }

    $stmt->close();

    // حذف الحدث من جدول Event
    $stmt = $conn->prepare("DELETE FROM Event WHERE Title = ?");
    $stmt->bind_param("s", $eventTitle);
    $stmt->execute();
    $stmt->close();

    // حذف الفرق المرتبطة من جدول Team
    $stmt = $conn->prepare("DELETE FROM Team WHERE Title = ?");
    $stmt->bind_param("s", $eventTitle);
    $stmt->execute();
    $stmt->close();

    header("Location: organizer_page.php?deleted=true");
    exit();
}
?>
