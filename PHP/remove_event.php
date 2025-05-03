<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

require 'connection.php';

$email = $_SESSION['email'];
$title = $_POST['title'] ?? '';

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'No event specified']);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // 1. Remove from shapeparticipant table
    $stmt1 = $conn->prepare("DELETE FROM shapeparticipant WHERE Email = ? AND Title = ?");
    $stmt1->bind_param("ss", $email, $title);
    $stmt1->execute();
    
    // 2. Check if user is a team member and remove from team_member table
    $stmt2 = $conn->prepare("DELETE FROM team_member WHERE Member_Email = ? AND Team_Name IN (SELECT Team_Name FROM team WHERE Title = ?)");
    $stmt2->bind_param("ss", $email, $title);
    $stmt2->execute();
    
    // 3. Check if user is a team leader and delete the team if empty
    $stmt3 = $conn->prepare("SELECT Team_Name FROM team WHERE Leader_Email = ? AND Title = ?");
    $stmt3->bind_param("ss", $email, $title);
    $stmt3->execute();
    $teamResult = $stmt3->get_result();
    
    if ($teamResult->num_rows > 0) {
        $team = $teamResult->fetch_assoc();
        $teamName = $team['Team_Name'];
        
        // Check if team has other members
        $stmt4 = $conn->prepare("SELECT COUNT(*) AS member_count FROM team_member WHERE Team_Name = ?");
        $stmt4->bind_param("s", $teamName);
        $stmt4->execute();
        $countResult = $stmt4->get_result();
        $count = $countResult->fetch_assoc()['member_count'];
        
        if ($count == 0) {
            // Delete the team if no members left
            $stmt5 = $conn->prepare("DELETE FROM team WHERE Team_Name = ?");
            $stmt5->bind_param("s", $teamName);
            $stmt5->execute();
        }
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>