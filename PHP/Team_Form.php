<?php
session_start();
require_once 'connection.php';


if (!isset($_SESSION['invited_emails'])) {
    $_SESSION['invited_emails'] = [];
}

function isAlreadyInTeam($conn, $email, $event_title) {
    $leaderQuery = "SELECT * FROM team WHERE Leader_Email = ? AND Title = ?";
    $stmtLeader = $conn->prepare($leaderQuery);
    $stmtLeader->bind_param("ss", $email, $event_title);
    $stmtLeader->execute();
    $resultLeader = $stmtLeader->get_result();
    if ($resultLeader->num_rows > 0) {
        return true;
    }

    $memberQuery = "SELECT tm.Team_Name 
                    FROM team_member tm
                    JOIN team t ON tm.Team_Name = t.Team_Name
                    WHERE tm.Member_Email = ? AND t.Title = ?";
    $stmtMember = $conn->prepare($memberQuery);
    $stmtMember->bind_param("ss", $email, $event_title);
    $stmtMember->execute();
    $resultMember = $stmtMember->get_result();
    if ($resultMember->num_rows > 0) {
        return true;
    }

    return false;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$profile_query = "SELECT Name, Email FROM User WHERE Email = ?";
$profile_stmt = $conn->prepare($profile_query);
$profile_stmt->bind_param("s", $_SESSION['email']);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();

if ($profile_result->num_rows > 0) {
    $profile_data = $profile_result->fetch_assoc();
    $_SESSION['user'] = $profile_data['Name'];
    $_SESSION['email'] = $profile_data['Email'];
}

$team_name = '';
$team_idea = '';
$event_title = isset($_GET['event_title']) ? urldecode($_GET['event_title']) : '';

if (!empty($event_title)) {
    $_SESSION['event_title'] = $event_title;
}

$query_event = "SELECT Max_Participants FROM event WHERE Title = ?";
$stmt_event = $conn->prepare($query_event);
$stmt_event->bind_param("s", $event_title);
$stmt_event->execute();
$result_event = $stmt_event->get_result();

if ($result_event->num_rows > 0) {
    $row_event = $result_event->fetch_assoc();
    $max_members = $row_event['Max_Participants'];
} else {
    $max_members = 10; 
}

// ✅ التحقق من إرسال الدعوة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $invite_email = trim($_POST['invite_email']);
    $team_name = trim($_POST['team_name']);
    $event_title = $_SESSION['event_title'] ?? '';
    $sender_email = $_SESSION['email'];

    if (empty($team_name)) {
        echo "<script>alert('Please enter the team name before sending the request. ❗');</script>";
    } elseif (empty($event_title)) {
        echo "<script>alert('Event not found. ❗');</script>";
    } else {
        $check_team_query = "SELECT * FROM team WHERE Team_Name = ?";
        $stmt_check_team = $conn->prepare($check_team_query);
        $stmt_check_team->bind_param("s", $team_name);
        $stmt_check_team->execute();
        $result_team = $stmt_check_team->get_result();

        if ($result_team->num_rows == 0) {
            echo "<script>alert('Team does not exist. Please create the team first. ❗');</script>";
        } else {
            $check_user_query = "SELECT Email FROM User WHERE Email = ?";
            $stmt_check_user = $conn->prepare($check_user_query);
            $stmt_check_user->bind_param("s", $invite_email);
            $stmt_check_user->execute();
            $result_check_user = $stmt_check_user->get_result();

            if ($result_check_user->num_rows == 0) {
                echo "<script>alert('User not found. Please enter a valid email. ❗');</script>";
            } else {
                $check_notification_query = "SELECT * FROM notification WHERE email = ? AND team_name = ? AND event_title = ? AND status = 'Pending'";
                $stmt_check_notification = $conn->prepare($check_notification_query);
                $stmt_check_notification->bind_param("sss", $invite_email, $team_name, $event_title);
                $stmt_check_notification->execute();
                $result_notification = $stmt_check_notification->get_result();

                if ($result_notification->num_rows == 0) {
                    $insert_notification_query = "INSERT INTO notification (email, event_title, team_name, status) VALUES (?, ?, ?, 'Pending')";
                    $stmt_notification = $conn->prepare($insert_notification_query);
                    $stmt_notification->bind_param("sss", $invite_email, $event_title, $team_name);

                    if ($stmt_notification->execute()) {
                        $_SESSION['invited_emails'][] = $invite_email;
                        echo "<script>alert('Request added successfully for $invite_email! ✅');</script>";
                    } else {
                        echo "<script>alert('Error while sending the request. ❗');</script>";
                    }
                } else {
                    echo "<script>alert('This user has already been invited to the team. ❗');</script>";
                }
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_team'])) {
    $team_name = $_POST['team_name'];
    $team_idea = $_POST['team_idea'];
    $leader_email = $_SESSION['email'];

    $check_membership = "SELECT * FROM team t 
                     LEFT JOIN team_member tm ON t.Team_Name = tm.Team_Name
                     WHERE t.Title = ? AND (t.Leader_Email = ? OR tm.Member_Email = ?)";

    $check_stmt = $conn->prepare($check_membership);
    $check_stmt->bind_param("sss", $event_title, $leader_email, $leader_email);
    $check_stmt->execute();
    $membership_result = $check_stmt->get_result();

    if ($membership_result->num_rows > 0) {
        echo "<script>
    alert('You are already part of a team in this event. ❗');
    window.location.href = 'event.php';
</script>";
        exit;
    }


    $check_team_query = "SELECT * FROM team WHERE Team_Name = ?";
    $check_team_stmt = $conn->prepare($check_team_query);
    $check_team_stmt->bind_param("s", $team_name);
    $check_team_stmt->execute();
    $check_team_result = $check_team_stmt->get_result();

    if ($check_team_result->num_rows == 0) {
        $insert_query = "INSERT INTO team (Team_Name, Team_Members, Team_Idea, Max_Members, Status, Title, Leader_Email) VALUES (?, 1, ?, ?, 'Pending', ?, ?)";
        $insert_team_stmt = $conn->prepare($insert_query);
        $insert_team_stmt->bind_param("ssiss", $team_name, $team_idea, $max_members, $event_title, $leader_email);

        if ($insert_team_stmt->execute()) {
            $insert_registration_query = "INSERT INTO registration (Team_Name, Idea, Name, Email) VALUES (?, ?, ?, ?)";
            $stmt_registration = $conn->prepare($insert_registration_query);
            $stmt_registration->bind_param("ssss", $team_name, $team_idea, $_SESSION['user'], $_SESSION['email']);
            $stmt_registration->execute();

            $insert_leader_query = "INSERT INTO team_member (Team_Name, Member_Email) VALUES (?, ?)";
            $stmt_leader = $conn->prepare($insert_leader_query);
            $stmt_leader->bind_param("ss", $team_name, $leader_email);
            $stmt_leader->execute();

            $insert_shape_stmt = $conn->prepare("INSERT INTO ShapeParticipant (Email, Title, Status) VALUES (?, ?, 'Pending')");
            $insert_shape_stmt->bind_param("ss", $leader_email, $event_title);
            $insert_shape_stmt->execute();

            foreach ($_SESSION['invited_emails'] as $invite_email) {
             
                $check_member_query = "SELECT * FROM team_member WHERE Team_Name = ? AND Member_Email = ?";
                $stmt_check_member = $conn->prepare($check_member_query);
                $stmt_check_member->bind_param("ss", $team_name, $invite_email);
                $stmt_check_member->execute();
                $result_member = $stmt_check_member->get_result();

                $check_shape_stmt = $conn->prepare("SELECT * FROM ShapeParticipant WHERE Email = ? AND Title = ?");
                $check_shape_stmt->bind_param("ss", $invite_email, $event_title);
                $check_shape_stmt->execute();
                $shape_result = $check_shape_stmt->get_result();

                if ($shape_result->num_rows == 0) {
                    $insert_shape_stmt = $conn->prepare("INSERT INTO ShapeParticipant (Email, Title, Status) VALUES (?, ?, 'Pending')");
                    $insert_shape_stmt->bind_param("ss", $invite_email, $event_title);
                    $insert_shape_stmt->execute();
                }


                if ($result_member->num_rows == 0) {
                    $insert_invite_query = "INSERT INTO team_member (Team_Name, Member_Email) VALUES (?, ?)";
                    $invite_stmt = $conn->prepare($insert_invite_query);
                    $invite_stmt->bind_param("ss", $team_name, $invite_email);
                    $invite_stmt->execute();
                }
               

                $get_user_query = "SELECT Name FROM User WHERE Email = ?";
                $stmt_get_user = $conn->prepare($get_user_query);
                $stmt_get_user->bind_param("s", $invite_email);
                $stmt_get_user->execute();
                $user_result = $stmt_get_user->get_result();

                if ($user_result->num_rows > 0) {
                    $user_data = $user_result->fetch_assoc();
                    $member_name = $user_data['Name'];

                    $stmt_registration->bind_param("ssss", $team_name, $team_idea, $member_name, $invite_email);
                    $stmt_registration->execute();
                }
            }

            $_SESSION['invited_emails'] = [];

            echo "<script>alert('Team registered successfully! 🎉'); window.location.href='event_details.php?title=" . urlencode($event_title) . "';</script>";
        } else {
            echo "<script>alert('Error while creating team. ❗');</script>";
        }
    } else {
        echo "<script>alert('This team name already exists! ❗');</script>";
    }
}
?>


<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Event Registration Form</title>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
            rel="stylesheet"
            />
        <link rel="stylesheet" href="Team_Form_Style.css" />
    </head>
    <body>
        <div class="app-container">
            <nav>
                <div class="nav-logo">
                    <a href="#">
                        <img src="images/logo_ruaa.png" alt="Ruaa Logo" />
                    </a>
                </div>

                <ul class="nav-links">
                    <li class="link"><a href="event.php">Events</a></li>
                    <li class="link"><a href="profile.php">Profile</a></li>
                    <li class="link"><a href="logout.php">Log out</a></li>
                    <li class="link"><a href="notification.php">🔔</a></li>
                </ul>
            </nav>

            <main class="main-content">
                <form class="form-container" method="POST" action="">
                    <h1 class="form-title">Register for the EVENT</h1>

                    <section class="form-section">
                        <label for="username" class="input-label">User Name :</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="input-field"
                            value="<?php echo $_SESSION['user']; ?>"
                            readonly
                            />
                    </section>

                    <section class="form-section">
                        <label for="email" class="input-label">Email :</label>
                        <input
                            type="email"
                            id="team-email"
                            name="invite_email"
                            class="input-field"
                            value="<?php echo $_SESSION['email']; ?>"
                            readonly
                            />


                    </section>

                    <div class="team-options">
                        <a
                            href="Looking_For_Team.php?event_title=<?= urlencode($event_title) ?>&team_name=<?= urlencode($team_name) ?>"
                            class="team-button-look"
                            >Look for Team</a
                        >

                    </div>

                    <small style="color: #ccc; display: block; margin-top: 10px; text-align: center;">
                        ※ Please create the team first using "Submit", then come back to invite members using the same team name.※
                    </small>

                    <br>
                    <section class="form-section">
                        <h2 class="section-title" style="text-align: center;">Add Team Members :</h2>

                        <label for="team-email" class="input-label">Email :</label>
                        <input
                            type="email"
                            id="team-email"
                            name="invite_email"
                            class="input-field"
                            required
                            />

                        <button type="submit" name="send_request" class="send-button" style="display: block; margin: 15px auto;">
                            Send Request
                        </button>

                    </section>


                    <div class="idea-section">
                        <div class="form-section">
                            <label for="team_name" class="input-label">Team Name :</label>
                            <input
                                type="text"
                                id="team_name"
                                name="team_name"
                                class="input-field"
                                value="<?php echo htmlspecialchars($team_name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required
                                />
                        </div>
                        <h2 class="section-title">Our Idea:</h2>
                        <textarea
                            name="team_idea"
                            class="idea-field"
                            required
                            ><?php echo htmlspecialchars($team_idea ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <button type="submit" name="submit_team" class="submit-button">
                        Submit
                    </button>

                </form>
            </main>

            

    <div class="copyright">
        Copyright © 2024 Ruaa. All Rights Reserved.
    </div>
        </div>
    </body>
</html>