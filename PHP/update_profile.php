<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include 'connection.php';

// Check if username is provided
if (!isset($_POST['username'])) {
    echo json_encode(['success' => false, 'error' => 'User data is incomplete.']);
    exit();
}

$username = trim($_POST['username']);
$experiences = isset($_POST['experiences']) ? trim($_POST['experiences']) : "";

// Use the current email from the session (email is not allowed to change)
$currentEmail = $_SESSION['email'];

// Prepare SQL statement to update Name and Experiences
$sql = "UPDATE user SET Name = ?, Experiences = ? WHERE Email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}
$stmt->bind_param("sss", $username, $experiences, $currentEmail);

if ($stmt->execute()) {
    // Update session data to reflect the new values
    $_SESSION['user'] = $username;
    $_SESSION['experiences'] = $experiences;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>