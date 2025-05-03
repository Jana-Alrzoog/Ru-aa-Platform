<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $event_title = $_POST['event_title'];
    
    // Verify the user is removing themselves
    if ($email !== $_SESSION['email']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
        exit();
    }
    
    $delete_query = "DELETE FROM shapeparticipant WHERE Email = ? AND Title = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ss", $email, $event_title);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Removed from participant list']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing from participant list']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>