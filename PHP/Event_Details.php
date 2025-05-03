<?php
include 'connection.php';


if (isset($_GET['title'])) {
    $event_title = $_GET['title'];

    
    $query = "SELECT * FROM Event WHERE Title = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $event_title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = htmlspecialchars($row['Title']);
        $description = htmlspecialchars($row['Description']);
        $type = htmlspecialchars($row['Type']);
        $date = htmlspecialchars($row['Date']);
        $location = htmlspecialchars($row['Location']);
        $max_participants = htmlspecialchars($row['Max_Participants']);
        $deadline = htmlspecialchars($row['Registration_Deadline']);
        $image = htmlspecialchars($row['Banner_Image']);
    } else {
        $title = "Event not found";
        $description = "No event details available.";
        $type = $date = $location = $max_participants = $deadline = $image = "";
    }
    $stmt->close();

    
    $team_query = "SELECT Team_Name FROM Team WHERE Title = ?";
    $team_stmt = $conn->prepare($team_query);
    $team_stmt->bind_param("s", $event_title);
    $team_stmt->execute();
    $team_result = $team_stmt->get_result();
} else {
    $title = "Event not found";
    $description = "No event details available.";
    $type = $date = $location = $max_participants = $deadline = $image = "";
}
$conn->close();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title >Event Details</title> 
    <link rel="stylesheet" href="Event_Details.css">
    <style>
      .event-details-container,
      .description-content,
      .teams-container {
          text-align: left;
      }
    </style>
  </head>
  <body>
    <main class="event-page">
      <div class="page-container">
        <nav>
          <div class="nav-logo">
              <a href="#">
                  <img src="images/logo_ruaa.png" alt="Ruaa Logo">
              </a>
          </div>
          <ul class="nav-links">
              <li class="link"><a href="event.php">Events</a></li>
                <li class="link"><a href="profile.php">Profile</a></li>
                <li class="link"><a href="logout.php">Log out</a></li>
                <li class="link"><a href="notification.php">🔔</a></li>
          </ul>
        </nav>

       

<h1 class="page-title" style="display: flex; justify-content: center; align-items: center; text-align: center; margin-bottom: 30px;">Event Details</h1>

        

        <section class="event-info-container">
          <?php if ($image !== ""): ?>
          <img src="<?php echo $image; ?>" alt="Event Image" class="event-image" />
          <?php endif; ?>

          
          <div class="event-details-container">
            <h2 class="event-topic"><?php echo $title; ?></h2>
            <p class="event-metadata">
              <strong>Type:</strong> <?php echo $type; ?><br />
              <strong>Start Date:</strong> <?php echo $date; ?><br />
              <strong>Location:</strong> <?php echo $location; ?><br />
              <strong>Max Participants:</strong> <?php echo $max_participants; ?><br />
              <strong>Registration Deadline:</strong> <?php echo $deadline; ?>
            </p>

         
            <a href="Team_Form.php?event_title=<?php echo urlencode($title); ?>" class="register-button">
                Register
            </a>
          </div>
        </section>

        <h3 class="section-title description-title">Description:</h3>
        <div class="description-content">
          <?php echo nl2br($description); ?>
        </div>

        <h3 class="section-title teams-title">Teams:</h3>
        <div class="teams-container">
          <?php
          if (isset($team_result) && $team_result->num_rows > 0) {
              while ($team_row = $team_result->fetch_assoc()) {
                  echo "<div class='team-card'>" . htmlspecialchars($team_row['Team_Name']) . "</div>";
              }
          } else {
              echo "<p style='color:#8940d3;'>No teams registered yet for this event.</p>";
          }
          ?>
        </div>


    <div class="copyright">
        Copyright © 2024 Ruaa. All Rights Reserved.
    </div>
      </div>
    </main>
  </body>
</html>
