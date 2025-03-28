<?php
session_start();
require_once 'connection.php'; // Use the same connection file
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

$currentUserEmail = $_SESSION['email'];
$requestEmail = $_POST['email'] ?? '';
$action = $_POST['action'] ?? '';

// ======== Keep your existing team request processing logic below ========
// This includes all the database operations for accepting/rejecting requests
// For example:

// Get the team name for current user
$teamQuery = "SELECT Team_Name FROM Team_Member WHERE LeaderName = 
            (SELECT Name FROM User WHERE Email = '$currentUserEmail')";
$teamResult = $conn->query($teamQuery);

if (!$teamResult || $teamResult->num_rows === 0) {
    die(json_encode(['success' => false, 'message' => 'You are not a team leader']));
}


$teamRow = $teamResult->fetch_assoc();
$teamName = $teamRow['Team_Name'];

if ($action === 'accept') {
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // 1. Add user to Registration table
        $addMemberQuery = "INSERT INTO Registration (Team_Name, Email, Name) 
                          SELECT '$teamName', Email, Name FROM User WHERE Email = '$requestEmail'";
        if (!$conn->query($addMemberQuery)) {
            throw new Exception("Failed to add team member");
        }
        
        // 2. Remove from JoinRequest table
        $deleteRequestQuery = "DELETE FROM JoinRequest 
                             WHERE Email = '$requestEmail' AND Team_Name = '$teamName'";
        if (!$conn->query($deleteRequestQuery)) {
            throw new Exception("Failed to remove join request");
        }
        
        // 3. Update team members count
        $updateCountQuery = "UPDATE Team SET Team_Members = Team_Members + 1 
                            WHERE Team_Name = '$teamName'";
        if (!$conn->query($updateCountQuery)) {
            throw new Exception("Failed to update team count");
        }
        
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
} elseif ($action === 'reject') {
    $deleteRequestQuery = "DELETE FROM JoinRequest 
                         WHERE Email = '$requestEmail' AND Team_Name = '$teamName'";
    
    if ($conn->query($deleteRequestQuery)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>