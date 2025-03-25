<!DOCTYPE html>

<?php
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');
include("connection.php");
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$organizer_email = $_SESSION['email'];


$query = "SELECT e.* 
          FROM Event e 
          JOIN Organizer o ON e.Title = o.Created_Event 
          WHERE o.Email = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $organizer_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css" />
        <link rel="stylesheet" href="organizer_page_style.css" />
        <title>Ruaa</title>
    </head>

    <body>
        <div class="wrapper">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
        </div>
        <div class="banner"></div>

        <nav>
            <div class="nav-logo">
                <a href="#">
                    <img src="images/logo_ruaa.png" alt="Ruaa Logo" />
                </a>
            </div>

            <ul class="nav-links">
                <li class="link"><a href="organizer_page.php">Organizer Page</a></li>
                <li class="link"><a href="logout.php">Log out</a></li>
            </ul>
        </nav>

        <header class="container">
            <div class="content">
                <span class="blur"></span><span class="blur"></span>
                <h1>Welcome to <span>Ruaa</span>!<br>Ready to take your event to the next level?</h1>
                <p>Join "Ru'aa" today and showcase your tech event effortlessly! Reach the right audience, and manage your event seamlessly – all in one place.</p>
            </div>
            <div class="image">
                <img src="images/labtob.png" alt="Events Image" />
            </div>
            <div>
                <button class="button-87" onclick="window.location.href = 'Add_Event.php';" role="button">host a new event</button>
            </div>
        </header>

        <h2 id="Events">Your Events :</h2>

        <div class="table-main" id="table2">
            <table class="table-area">
                <thead>
                <th>Event Name</th>
                <th>Cancellation</th>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Title']) ?></td>
                            <td class="table-data">
                                <form method="POST" action="delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    <input type="hidden" name="event_title" value="<?= htmlspecialchars($row['Title']) ?>">
                                    <button class="button-62" type="submit">&#10006;</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>


            </table>
        </div>

        <footer class="container">
            <span class="blur"></span><span class="blur"></span>
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
        </footer>

        <div class="copyright">
            Copyright © 2024 Ruaa. All Rights Reserved.
        </div>
    </body>
</html>
