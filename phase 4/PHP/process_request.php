<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = $_POST['team_name'];
    $currentUserEmail = $_SESSION['email'];

    if (isset($_POST['accept_request'])) {
        // ✅ قبول الدعوة وتحديث الحالة
        $update_query = "UPDATE Team_Member SET Status = 'Accepted' WHERE Team_Name = ? AND Member_Email = ?";
        $stmt = $conn->prepare($update_query);
        $query_team = "SELECT Max_Members FROM team WHERE Team_Name = ?";
$stmt_team = $conn->prepare($query_team);
$stmt_team->bind_param("s", $teamName);
$stmt_team->execute();
$result_team = $stmt_team->get_result();
$row_team = $result_team->fetch_assoc();
$max_members = $row_team['Max_Members'];

$query_count = "SELECT COUNT(*) AS current_members FROM team_member WHERE Team_Name = ? AND Status = 'Accepted'";
$stmt_count = $conn->prepare($query_count);
$stmt_count->bind_param("s", $teamName);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$current_members = $row_count['current_members'];

if ($current_members < $max_members) {
    $stmt->bind_param("ss", $teamName, $currentUserEmail);
    $stmt->execute();
} else {
echo "<script>alert('Sorry, the team is already full!');</script>";
}

        if ($stmt->execute()) {
            echo "<script>alert('Request accepted successfully! ✅'); window.location.href='notification.php';</script>";
        } else {
            echo "<script>alert('Error while accepting request ❗');</script>";
        }
    } elseif (isset($_POST['reject_request'])) {
        // ❌ رفض الدعوة وحذفها
        $delete_query = "DELETE FROM Team_Member WHERE Team_Name = ? AND Member_Email = ?";
        $stmt = $conn->prepare($delete_query);
        $query_team = "SELECT Max_Members FROM team WHERE Team_Name = ?";
$stmt_team = $conn->prepare($query_team);
$stmt_team->bind_param("s", $teamName);
$stmt_team->execute();
$result_team = $stmt_team->get_result();
$row_team = $result_team->fetch_assoc();
$max_members = $row_team['Max_Members'];

$query_count = "SELECT COUNT(*) AS current_members FROM team_member WHERE Team_Name = ? AND Status = 'Accepted'";
$stmt_count = $conn->prepare($query_count);
$stmt_count->bind_param("s", $teamName);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$current_members = $row_count['current_members'];

if ($current_members < $max_members) {
    $stmt->bind_param("ss", $teamName, $currentUserEmail);
    $stmt->execute();
} else {
echo "<script>alert('Sorry, the team is already full!');</script>";
}

        if ($stmt->execute()) {
            echo "<script>alert('Request rejected successfully! ❌'); window.location.href='notification.php';</script>";
        } else {
            echo "<script>alert('Error while rejecting request ❗');</script>";
        }
    }
}
?>
