<?php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}


include("connection.php");


$query = "SELECT * FROM Event";
$result = mysqli_query($conn, $query);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Events Platform</title>
  <link rel="stylesheet" href="event.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Inter:wght@400;700&display=swap" rel="stylesheet" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css">
</head>
<body>

<div class="app-container">
  <header class="navigation">
    <div class="nav-content">
      <div class="search-filter-container">
        <div class="search-bar">
          <i class="ti ti-search search-icon"></i>
          <input type="text" placeholder="Search" class="search-input" />
        </div>
         <div class="filter-container">
            <button class="filter-btn" onclick="toggleFilter()">
            <i class="ti ti-adjustments"></i>
             </button>

          <div class="filter-menu">
            <div class="filter-group">
              <label class="filter-label">Event Type</label>
              <div class="filter-option">
                <input type="checkbox" id="hackathon" />
                <label for="hackathon">Hackathon</label>
              </div>
              <div class="filter-option">
                <input type="checkbox" id="workshop" />
                <label for="workshop">Workshop</label>
              </div>
            </div>
            <div class="filter-group">
              <label class="filter-label">Date Range</label>
              <div class="filter-option">
                <input type="checkbox" id="upcoming" />
                <label for="upcoming">Upcoming</label>
              </div>
              <div class="filter-option">
                <input type="checkbox" id="ongoing"/>
                <label for="ongoing">Ongoing</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <nav class="nav-links">
        
        <a href="event.php" class="nav-link">Events</a>
        <a href="profile.php" class="nav-link">Profile</a>
        <a href="logout.php" class="nav-link">Log out</a>
         <li class="link"><a href="notification.php">🔔</a></li>
      </nav>
    </div>
  </header>

  <main class="content-section">
    <section class="content-background">
      <div class="event-wrapper">
        <div class="event-cards">

          <?php while ($row = mysqli_fetch_assoc($result)) : ?>
  <div class="event-card"
       data-title="<?= strtolower($row['Title']) ?>"
       data-type="<?= strtolower($row['Type']) ?>"
       data-date="<?= $row['Date'] ?>"
       data-deadline="<?= $row['Registration_Deadline'] ?>"
       onclick="window.location.href='Event_Details.php?title=<?= urlencode($row['Title']) ?>';"
       style="cursor: pointer;">
    <img src="<?= htmlspecialchars($row['Banner_Image']) ?>" alt="Event Image" class="event-image">
    <h3 class="event-title"><?= htmlspecialchars($row['Title']) ?></h3>
    <p><strong>Type:</strong> <?= htmlspecialchars($row['Type']) ?></p>
    <p><strong>Start Date:</strong> <?= htmlspecialchars($row['Date']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($row['Location']) ?></p>
    <p><strong>Registration Deadline:</strong> <?= htmlspecialchars($row['Registration_Deadline']) ?></p>
<button class="register-btn" onclick="event.stopPropagation(); window.location.href='Team_Form.php?event_title=<?= urlencode($row['Title']) ?>';">Register</button>

  </div>
<?php endwhile; ?>


        </div>
          
<p id="no-results" style="text-align:center; margin-top:20px; display:none; color:white;">No events found.</p>

      </div>
    </section>
  </main>
<footer>
  <div class="container">
    <div class="column">
      <div class="logo">
        <img src="images/logo_ruaa.png" alt="Ruaa Logo" />
      </div>
      <p>Connecting innovators, fostering collaboration, and hosting top-tier hackathons & workshops worldwide.</p>
      <div class="socials">
        <a href="#"><i class="ri-linkedin-box-line"></i></a>
        <a href="#"><i class="ri-twitter-line"></i></a>
        <a href="#"><i class="ri-discord-line"></i></a>
      </div>
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
  </div>
</footer>

<div class="copyright">
  Copyright © 2024 Ruaa. All Rights Reserved.
</div>

</div>

<script>
  function toggleFilter() {
    document.querySelector(".filter-container").classList.toggle("active");
  }
</script>
<script src="event.js"></script>

<script>
function filterEvents() {
  const searchInput = document.querySelector(".search-input").value.toLowerCase();
  const hackathonChecked = document.getElementById("hackathon").checked;
  const workshopChecked = document.getElementById("workshop").checked;
  const upcomingChecked = document.getElementById("upcoming").checked;
  const ongoingChecked = document.getElementById("ongoing").checked;

  const eventCards = document.querySelectorAll(".event-card");
  let anyVisible = false;
  const today = new Date();

  eventCards.forEach(card => {
    const title = card.dataset.title.toLowerCase();
    const type = card.dataset.type;
    const date = new Date(card.dataset.date);
    const deadline = new Date(card.dataset.deadline);

  console.log(" title:", card.dataset.title, "| search:", searchInput);


  let matchesSearch = searchInput === "" || title.includes(searchInput);


   
    let matchesType = true;
    if (hackathonChecked || workshopChecked) {
      matchesType = false;
      if (hackathonChecked && type === "hackathon") matchesType = true;
      if (workshopChecked && type === "workshop") matchesType = true;
    }


    let matchesDate = true;
    if (upcomingChecked || ongoingChecked) {
      matchesDate = false;
      if (upcomingChecked && date > today) matchesDate = true;
      if (ongoingChecked && today >= date && today <= deadline) matchesDate = true;
    }

    const shouldShow = matchesSearch && matchesType && matchesDate;
    card.style.display = shouldShow ? "block" : "none";
    if (shouldShow) anyVisible = true;
  });

  document.getElementById("no-results").style.display = anyVisible ? "none" : "block";
}


document.querySelector(".search-input").addEventListener("input", filterEvents);


document.querySelectorAll(".filter-option input").forEach(input => {
  input.addEventListener("change", filterEvents);
});


window.addEventListener("DOMContentLoaded", filterEvents);

</script>



</body>
</html>
