<?php
session_start();
include 'connection.php'; // الاتصال بقاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبال البيانات من النموذج
    $leader_name = $_POST['username'];
    $leader_email = $_POST['email'];
    $team_name = $_POST['team_name'];
    $team_idea = $_POST['team_idea'];
    $event_title = $_POST['event_title']; // بنضيفه في النموذج لاحقًا
    $invited_emails = isset($_POST['invited_emails']) ? $_POST['invited_emails'] : [];

foreach ($invited_emails as $email) {
    $query_invite = "INSERT INTO teaminvitation (Email, Team_Name, Status) VALUES (?, ?, 'Pending')";
    $stmt_invite = $conn->prepare($query_invite);
    $stmt_invite->bind_param("ss", $email, $team_name);
    $stmt_invite->execute();
    $query_notify = "INSERT INTO notifications (email, message, status) VALUES (?, 'You have a new team invitation!', 'unread')";
    $stmt_notify = $conn->prepare($query_notify);
    $stmt_notify->bind_param("s", $email);
    $stmt_notify->execute();
}

    // إنشاء الفريق
    $insertTeam = "INSERT INTO Team (Team_Name, Team_Idea, Title, Leader_Email, Status, Max_Members)
                   VALUES (?, ?, ?, ?, 'Active', 10)";

    $stmt = $conn->prepare($insertTeam);
    $stmt->bind_param("ssss", $team_name, $team_idea, $event_title, $leader_email);

    if ($stmt->execute()) {
        // إضافة القائد إلى جدول Team_Member كعضو مقبول
        $insertLeader = "INSERT INTO Team_Member (Team_Name, Member_Email, Status)
                         VALUES (?, ?, 'Accepted')";
        $stmt_leader = $conn->prepare($insertLeader);
        $stmt_leader->bind_param("ss", $team_name, $leader_email);
        $stmt_leader->execute();

        // إضافة الأعضاء المدعوين كـ Pending
        $insertMember = "INSERT INTO Team_Member (Team_Name, Member_Email, Status)
                         VALUES (?, ?, 'Pending')";
        $stmt_member = $conn->prepare($insertMember);

        foreach ($invited_emails as $email) {
            $stmt_member->bind_param("ss", $team_name, $email);
            $stmt_member->execute();
        }

        // تحويل المستخدم إلى صفحة النجاح أو تفاصيل الحدث
        header("Location: Event_Details.php?title=" . urlencode($event_title));
        exit();
    } else {
        echo "خطأ في إنشاء الفريق: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "طلب غير صالح.";
}
?>
