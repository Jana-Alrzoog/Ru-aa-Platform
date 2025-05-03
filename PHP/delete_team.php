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

$conn->begin_transaction();

try {
    $verifyStmt = $conn->prepare("SELECT Team_Name FROM team WHERE Team_Name = ? AND Leader_Email = ?");
    $verifyStmt->bind_param("ss", $teamName, $email);
    $verifyStmt->execute();
    
    if ($verifyStmt->get_result()->num_rows === 0) {
        throw new Exception("You are not the leader of this team");
    }

    $stmt1 = $conn->prepare("DELETE FROM team_member WHERE Team_Name = ?");
    $stmt1->bind_param("s", $teamName);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM team WHERE Team_Name = ?");
    $stmt2->bind_param("s", $teamName);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>