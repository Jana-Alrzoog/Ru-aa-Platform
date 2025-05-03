<?php
session_start();
require_once 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $event_title = $_POST['title'];
    $email = $_SESSION['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT Team_Name FROM team WHERE Title = ? AND Leader_Email = ?");
    $stmt->bind_param("ss", $event_title, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $isLeader = $result->num_rows > 0;
    $teamName = $isLeader ? $result->fetch_assoc()['Team_Name'] : null;

    $conn->begin_transaction();
    try {
        if ($isLeader) {
            $stmt = $conn->prepare("DELETE tm FROM team_member tm INNER JOIN team t ON tm.Team_Name = t.Team_Name WHERE t.Title = ?");
            $stmt->bind_param("s", $event_title);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM registration WHERE Team_Name IN (SELECT Team_Name FROM team WHERE Title = ?)");
            $stmt->bind_param("s", $event_title);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM team WHERE Title = ?");
            $stmt->bind_param("s", $event_title);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM shapeparticipant WHERE Title = ?");
            $stmt->bind_param("s", $event_title);
            $stmt->execute();

        } else {
$stmt = $conn->prepare("SELECT t.Team_Name FROM team t 
                        JOIN team_member tm ON tm.Team_Name = t.Team_Name 
                        WHERE tm.Member_Email = ? AND t.Title = ?");
$stmt->bind_param("ss", $email, $event_title);
$stmt->execute();
$team_result = $stmt->get_result();

if ($team_result->num_rows > 0) {
    $team_name = $team_result->fetch_assoc()['Team_Name'];

    $stmt = $conn->prepare("DELETE FROM team_member WHERE Member_Email = ? AND Team_Name = ?");
    $stmt->bind_param("ss", $email, $team_name);
    $stmt->execute();

   
    $stmt = $conn->prepare("UPDATE team SET Team_Members = Team_Members - 1 WHERE Team_Name = ?");
    $stmt->bind_param("s", $team_name);
    $stmt->execute();
}


$stmt = $conn->prepare("DELETE FROM shapeparticipant WHERE Email = ? AND Title = ?");
$stmt->bind_param("ss", $email, $event_title);
$stmt->execute();


        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Deleted successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Deletion failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>