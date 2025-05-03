<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

require 'connection.php';

$email = $_SESSION['email'];
$teamName = $_POST['team'] ?? '';

if (empty($teamName)) {
    echo json_encode(['success' => false, 'error' => 'No team specified']);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM team_member WHERE Team_Name = ? AND Member_Email = ?");
    $stmt->bind_param("ss", $teamName, $email);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>