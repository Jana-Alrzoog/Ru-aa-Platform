<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_title'])) {
    $event_title = $_POST['event_title'];
    $event_title = trim($event_title);  

    if (!empty($event_title)) {
        $query = "DELETE FROM Event WHERE Title = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $event_title);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: organizer_page.php");
            exit();
        } else {
            echo "Failed to delete event.";
        }
    } else {
        echo "Invalid event title.";
    }
} else {
    echo "Invalid request.";
}
?>
