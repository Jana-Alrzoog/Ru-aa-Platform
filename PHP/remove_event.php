<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $eventTitle = $_POST['title'];

    $stmt = $conn->prepare("DELETE FROM ShapeParticipant WHERE Email = ? AND Title = ?");
    $stmt->bind_param("ss", $email, $eventTitle);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
