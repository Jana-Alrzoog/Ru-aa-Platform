<?php


require_once 'connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);
$action = isset($data['action']) ? $data['action'] : '';

$team_name = $data['team_name'];
$event_title = $data['event_title'];
$email = $data['email'];


$check_team = $conn->prepare("SELECT * FROM team WHERE Team_Name = ?");
$check_team->bind_param("s", $team_name);
$check_team->execute();
$result = $check_team->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Team not found! Please create the team first before inviting others. ❗"
    ]);
    exit();
}

if ($action === 'send_request') {
    $email = $data['email'];
    $event_title = $data['event_title'];
    $team_name = "Pending Team"; 

    if (!empty($email)) {
       
        $check_query = "SELECT * FROM team_member WHERE Member_Email = ? AND Team_Name = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $email, $team_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            
            $insert_query = "INSERT INTO team_member (Team_Name, Member_Email, Status) VALUES (?, ?, 'Pending')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ss", $team_name, $email);

            if ($insert_stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Request sent successfully to ' . $email]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error sending request.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Request already sent to this user.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No email provided.']);
    }
}

if ($action === 'add_participant') {
    $email = $data['email'];
    $event_title = $data['event_title'];

  
    $check_query = "SELECT * FROM shapeparticipant WHERE Email = ? AND Title = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ss", $email, $event_title);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
 
        $insert_query = "INSERT INTO shapeparticipant (Email, Title, Status) VALUES (?, ?, 'Pending')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ss", $email, $event_title);

        if ($insert_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'You have been added successfully! ✅']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error while adding to participants. ❗']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'You are already on the list. ❗']);
    }
}


if ($action === 'remove_participant') {
    $email = $data['email'];
    $event_title = $data['event_title'];

    $delete_query = "DELETE FROM shapeparticipant WHERE Email = ? AND Title = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ss", $email, $event_title);

    if ($delete_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'You have been removed from the list.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error removing participant.']);
    }
}

if ($action === 'add_to_notifications') {
    $email = $data['email'];
    $event_title = isset($data['event_title']) ? $data['event_title'] : 'Unknown Event';
    $team_name = isset($data['team_name']) ? $data['team_name'] : 'Default Team';

    if ($email && $event_title && $team_name) {

        $check_query = "SELECT * FROM notification WHERE email = ? AND event_title = ? AND team_name = ? AND status = 'Pending'";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("sss", $email, $event_title, $team_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
           
            $query = "INSERT INTO notification (email, event_title, team_name, status) VALUES (?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $email, $event_title, $team_name);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Notification added successfully.✅']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error adding notification.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Notification already exists.❗']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing email, event title, or team name.']);
    }
}



?>
