<?php
session_start();
require_once 'connection.php';

// ✅ تهيئة مصفوفة الدعوات إذا لم تكن موجودة
if (!isset($_SESSION['invited_emails'])) {
    $_SESSION['invited_emails'] = [];
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ جلب اسم المستخدم والإيميل من profile.php
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

// ✅ تعريف المتغيرات الأساسية
$team_name = '';
$team_idea = '';
$event_title = isset($_GET['event_title']) ? urldecode($_GET['event_title']) : '';

if (!empty($event_title)) {
    $_SESSION['event_title'] = $event_title;
}

// ✅ جلب الحد الأقصى للمشاركين من الحدث
$query_event = "SELECT Max_Participants FROM event WHERE Title = ?";
$stmt_event = $conn->prepare($query_event);
$stmt_event->bind_param("s", $event_title);
$stmt_event->execute();
$result_event = $stmt_event->get_result();

if ($result_event->num_rows > 0) {
    $row_event = $result_event->fetch_assoc();
    $max_members = $row_event['Max_Participants'];
} else {
    $max_members = 10; // افتراضي في حال عدم العثور على الحدث
}

// ✅ التحقق من إرسال الدعوة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $invite_email = trim($_POST['invite_email']);
    $team_name = trim($_POST['team_name']);
    $event_title = $_SESSION['event_title'] ?? '';
    $sender_email = $_SESSION['email'];

    // ✅ التأكد من كتابة اسم الفريق
    if (empty($team_name)) {
        echo "<script>alert('Please enter the team name before sending the request. ❗');</script>";
    } elseif (empty($event_title)) {
        echo "<script>alert('Event not found. ❗');</script>";
    } else {
        // ✅ التحقق إذا كان الإيميل موجود في قاعدة البيانات
        $check_user_query = "SELECT Email FROM User WHERE Email = ?";
        $stmt_check_user = $conn->prepare($check_user_query);
        $stmt_check_user->bind_param("s", $invite_email);
        $stmt_check_user->execute();
        $result_check_user = $stmt_check_user->get_result();

        if ($result_check_user->num_rows == 0) {
            echo "<script>alert('User not found. Please enter a valid email. ❗');</script>";
        } else {
            // ✅ التحقق من عدم تكرار الدعوة
            $check_notification_query = "SELECT * FROM notification WHERE email = ? AND team_name = ? AND event_title = ? AND status = 'Pending'";
            $stmt_check_notification = $conn->prepare($check_notification_query);
            $stmt_check_notification->bind_param("sss", $invite_email, $team_name, $event_title);
            $stmt_check_notification->execute();
            $result_notification = $stmt_check_notification->get_result();

            if ($result_notification->num_rows == 0) {
                // ✅ إدخال الدعوة إلى جدول notification
                $insert_notification_query = "INSERT INTO notification (email, event_title, team_name, status) VALUES (?, ?, ?, 'Pending')";
                $stmt_notification = $conn->prepare($insert_notification_query);
                $stmt_notification->bind_param("sss", $invite_email, $event_title, $team_name);

                // ✅ تنفيذ الإدخال إذا لم تكن الدعوة موجودة مسبقًا
                if ($stmt_notification->execute()) {
                    $_SESSION['invited_emails'][] = $invite_email; // ✅ حفظ الإيميل بعد الدعوة
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

// ✅ معالجة طلب Submit الفريق
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_team'])) {
    $team_name = $_POST['team_name'];
    $team_idea = $_POST['team_idea'];
    $leader_email = $_SESSION['email'];

    // ✅ التحقق إذا الفريق موجود مسبقًا
    $check_team_query = "SELECT * FROM team WHERE Team_Name = ?";
    $check_team_stmt = $conn->prepare($check_team_query);
    $check_team_stmt->bind_param("s", $team_name);
    $check_team_stmt->execute();
    $check_team_result = $check_team_stmt->get_result();

    if ($check_team_result->num_rows == 0) {
        // ✅ إدخال الفريق الجديد
        $insert_query = "INSERT INTO team (Team_Name, Team_Members, Team_Idea, Max_Members, Status, Title, Leader_Email) VALUES (?, 1, ?, ?, 'Pending', ?, ?)";
        $insert_team_stmt = $conn->prepare($insert_query);
        $insert_team_stmt->bind_param("ssiss", $team_name, $team_idea, $max_members, $event_title, $leader_email);

        if ($insert_team_stmt->execute()) {
            // ✅ إدخال الدعوات المرسلة
            foreach ($_SESSION['invited_emails'] as $invite_email) {
                $insert_invite_query = "INSERT INTO team_member (Team_Name, Member_Email, Status) VALUES (?, ?, 'Pending')";
                $invite_stmt = $conn->prepare($insert_invite_query);
                $invite_stmt->bind_param("ss", $team_name, $invite_email);
                $invite_stmt->execute();
            }

            // ✅ تفريغ المصفوفة بعد إرسال الدعوات
            $_SESSION['invited_emails'] = [];

            // ✅ إعادة التوجيه لصفحة تفاصيل الحدث
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
          <li class="link"><a href="Home_page.php">Home</a></li>
          <li class="link"><a href="event.php">Events</a></li>
          <li class="link"><a href="profile.php">Profile</a></li>
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
              id="email"
              name="email"
              class="input-field"
              value="<?php echo $_SESSION['email']; ?>"
              readonly
            />
          </section>

          <!-- ✅ أزرار Look for Team و I have a Team -->
          <div class="team-options">
            <a
              href="Looking_For_Team.php?event_title=<?= urlencode($event_title) ?>&team_name=<?= urlencode($team_name) ?>"
              class="team-button-look"
              >Look for Team</a
            >
          </div>

          <div class="team-section">
            <h2 class="section-title">Add Team Members :</h2>

            <!-- ✅ حقل الإيميل -->
            <div class="email-section">
              <label for="team-email" class="input-label">Email :</label>
              <input
                type="email"
                id="team-email"
                name="invite_email"
                class="input-field"
                required
              />
            </div>

            <button type="submit" name="send_request" class="send-button">
              Send Request
            </button>
          </div>

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

      <footer class="container">
        <span class="blur"></span>
        <span class="blur"></span>
        <div class="column">
          <div class="logo">
            <img src="images/logo_ruaa.png" alt="Ruaa Logo" />
          </div>
          <p>
            Connecting innovators, fostering collaboration, and hosting
            top-tier hackathons & workshops worldwide.
          </p>
        </div>
        <div class="column">
          <h4>Explore</h4>
          <a href="#">Events</a>
          <a href="#">Workshops</a>
          <a href="#">Hackathons</a>
        </div>
        <div class="column">
          <h4>About</h4>
          <a href="#">Mission</a>
          <a href="#">Contact</a>
        </div>
        <div class="column">
          <h4>Legal</h4>
          <a href="#">Privacy Policy</a>
          <a href="#">Terms of Service</a>
        </div>
      </footer>

      <div class="copyright">
        Copyright © 2024 Ruaa. All Rights Reserved.
      </div>
    </div>
  </body>
</html>
