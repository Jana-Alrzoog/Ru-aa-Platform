<?php
session_start();
require_once 'connection.php';

// ✅ استقبال اسم الحدث واسم التيم بشكل صحيح
$event_title = isset($_GET['event_title']) ? urldecode($_GET['event_title']) : (isset($_SESSION['event_title']) ? $_SESSION['event_title'] : '');
$team_name = isset($_GET['team_name']) ? urldecode($_GET['team_name']) : (isset($_SESSION['team_name']) ? $_SESSION['team_name'] : 'Default Team');

// ✅ تخزين القيم في الجلسة
if (!empty($event_title)) {
    $_SESSION['event_title'] = $event_title;
}

if (!empty($event_title)) {
    $_SESSION['event_title'] = $event_title;
}


// ✅ جلب المشاركين الذين يبحثون عن فريق حسب اسم الحدث
$query = "SELECT Email FROM shapeparticipant WHERE Title = ? AND Status = 'Pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $event_title);
$stmt->execute();

$result = $stmt->get_result();

$participants = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $participants[] = $row['Email'];
    }
}


?>


<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Participants Application</title>
        <link rel="stylesheet" href="Looking_For_Team_Style.css" />
    </head>
    <body>
        <div class="app">
            <header class="header">
                <nav>
                    <div class="nav-logo">
                        <a href="#">
                            <img src="images/logo_ruaa.png" alt="Ruaa Logo">
                        </a>
                    </div>
                    <ul class="nav-links">
                        <li class="link"><a href="organizer_page.php">Home</a></li>
                        <li class="link"><a href="event.php">Events</a></li>
                        <li class="link"><a href="profile.php">Profile</a></li>
                    </ul>
                </nav>
            </header>

            <main class="main-content">
                <section class="participants-section">
                    <h2 class="section-title">Participants ready to work with you:</h2>

                    <button class="join-team-btn" id="join-team-btn">Be One Of Them</button>
                    <button class="leave-team-btn" id="leave-team-btn">I Have a Team</button>

                    <input type="hidden" id="eventTitle" value="<?php echo htmlspecialchars($event_title); ?>" />
                    <input type="hidden" id="userEmail" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" />
                    </br>
<div class="team-name-section">
    <label for="teamNameInput">Enter your Team Name:</label>
    <input
        type="text"
        id="teamNameInput"
        class="input-field"
        value=""
        placeholder="Enter Team Name"
    />
</div>

                    <div class="participants-container" id="participants-container">
                        <?php if (empty($participants)) : ?>
                            <p style="color: #fff;">No participants found for this event.</p>
                        <?php else : ?>
                            <?php foreach ($participants as $email) : ?>
                                <div class="participant-card">
                                    <div class="participant-avatar"></div>
                                    <p class="participant-name"><?php echo htmlspecialchars($email); ?></p>
                                </div>


                                <!-- ✅ الزر خارج الكرت هنا -->
                                <button class="add-to-notifications-btn" onclick="addToNotifications('<?php echo htmlspecialchars($email); ?>')">
    Send Request
</button>



                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </main>
        </div>

        <script>
         function addToNotifications(email) {
    const eventTitle = document.getElementById("eventTitle").value; // ✅ جلب اسم الهاكاثون
    const teamName = document.getElementById("teamNameInput").value; // ✅ جلب اسم الفريق من الحقل

    if (!email || email === "") {
        alert("Please select a valid participant.");
        return;
    }

    if (!teamName || teamName === "") {
        alert("Please enter a team name before sending the request.");
        return;
    }

    fetch("API.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            action: "add_to_notifications",
            email: email,
            event_title: eventTitle, // ✅ تمرير اسم الايفنت الصحيح
            team_name: teamName,
        }),
    })
    .then((response) => response.json())
    .then((data) => {
        alert(data.message);
        if (data.status === "success") {
            location.reload();
        }
    })
    .catch((error) => {
        console.error("Error:", error);
    });
}

document.getElementById("join-team-btn").addEventListener("click", function () {
    const email = document.getElementById("userEmail").value;
    const eventTitle = document.getElementById("eventTitle").value; // ✅ الحدث يتم إرساله هنا

    if (email && eventTitle) {
        fetch("API.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                action: "add_participant",
                email: email,
                event_title: eventTitle,
            }),
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            if (data.status === "success") {
                location.reload();
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    } else {
        alert("Error: Missing email or event title.");
    }
});



// ✅ إزالة المستخدم عند الضغط على I Have a Team
document.getElementById("leave-team-btn").addEventListener("click", function () {
    const email = document.getElementById("userEmail").value;
    const eventTitle = document.getElementById("eventTitle").value;

    if (email && eventTitle) {
        fetch("API.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                action: "remove_participant",
                email: email,
                event_title: eventTitle,
            }),
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            if (data.status === "success") {
                // ✅ إعادة توجيه إلى Team_Form بعد الحذف بنجاح
                window.location.href = `Team_Form.php?event_title=${encodeURIComponent(eventTitle)}`;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    } else {
        alert("Error: Missing email or event title.");
    }
});

        </script>
    </body>
</html>
