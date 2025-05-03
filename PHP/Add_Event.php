<!DOCTYPE html>

<?php
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');
session_start();
include("connection.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organizer_email = $_SESSION['email'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];
    $max_participants = $_POST['max_participants'];
    $registration_deadline = $_POST['registration_deadline'];
    $event_description = $_POST['event_description'];
    $event_type = $_POST['event_type'];

    $image_path = '';
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $image_name = time() . "_" . basename($_FILES['event_image']['name']);
        $target_dir = "images/";
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    
    $check_query = "SELECT COUNT(*) FROM Event WHERE Title = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $event_name);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $count);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);

    if ($count > 0) {
        echo "<script>alert('An event with this name already exists. Please choose a different name.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO Event (Title, Location,  Max_Participants, Date,  Description, Banner_Image, Registration_Deadline, Type  )
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $registration_deadline)) {
        die("Invalid date format for registration deadline.");
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssisssss",
            $event_name,
            $event_location,
            $max_participants,
            $event_date,
            $event_description,
            $image_path,
            $registration_deadline,
            $event_type
    );

    if (mysqli_stmt_execute($stmt)) {
        // ربط الحدث بالمنظم
        $insert_organizer = "INSERT INTO Organizer (Email, Created_Event) VALUES (?, ?)";
        $org_stmt = mysqli_prepare($conn, $insert_organizer);
        mysqli_stmt_bind_param($org_stmt, "ss", $organizer_email, $event_name);
        mysqli_stmt_execute($org_stmt);

        echo "<script>alert('Event added successfully!'); window.location.href='organizer_page.php';</script>";
        exit();
    }
}
?>



<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css">
        <link rel="stylesheet" href="add_event_style.css">
        <title>Ruaa</title>
    </head>

    <body>
        <div class="wrapper">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="banner"> 
        </div>


        <nav>
            <div class="nav-logo">
                <a href="#">
                    <img src="images/logo_ruaa.png" alt="Ruaa Logo">
                </a>
            </div>

            <ul class="nav-links">
                <li class="link"><a href="organizer_page.php">Organizer Page</a></li>
                <li class="link"><a href="logout.php">Log out</a></li>

            </ul>
        </nav>

        <header class="container">
            <div class="content">
                <span class="blur"></span>
                <span class="blur"></span>



            </div>

        </header>

        <div class="add_event">
            <div class="container2">
                <div class="title">Add an Event!</div>
                <div class="content">
                    <form action="Add_Event.php" method="POST" enctype="multipart/form-data">
                        <div class="user-details">
                            <div class="input-box">
                                <span class="details">Event Name</span>
                                <input type="text" name="event_name" placeholder="Enter Event Name" required />
                            </div>

                            <div class="input-box">
                                <span class="details">Event Location</span>
                                <input type="text" name="event_location" placeholder="Enter Event Location" required />
                            </div>

                            <div class="input-box">
                                <span class="details">Maximum Number of Participants per team</span>
                                <input type="number" name="max_participants" placeholder="Max Num of Participants per team" required />
                            </div>

                            <div class="input-box">
                                <span class="details" style="margin-top: 20px;">Date</span>
                                <input type="date" name="event_date" required />
                            </div>

                            <div class="input-box">
                                <span class="details">Event Description</span>
                                <textarea name="event_description" rows="6" cols="40" required></textarea>
                            </div>

                            <div class="input-box">
                                <span class="details">Event Banner/Image</span>
                                <label for="eventImage" id="uploadLabel">
                                    <img src="images/upload3.png" alt="Upload Icon" id="uploadIcon" />
                                </label>
                                <input type="file" name="event_image" id="eventImage" accept="image/*" required />
                            </div>

                            <div class="input-box">
                                <span class="details">Registration Deadline</span>
                                <input type="date" name="registration_deadline" required />
                            </div>



                            <div class="input-box">
                                <span class="details">Event Type</span>


                                <div class="category">
                                    <label>
                                        <input type="radio" name="event_type" id="dot-1" value="Hackathon">
                                        <span class="dot one"></span>
                                        <span class="Event_type">Hackathon</span>
                                    </label>
                                    <label>
                                        <input type="radio" name="event_type" id="dot-2" value="Workshop">
                                        <span class="dot two"></span>
                                        <span class="Event_type">Workshop</span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="button">
                            <input type="submit" value="Register" />
                        </div>
                    </form>
                </div>
            </div>
        </div>




        <br>
        <!--    <div class="add_event">
                <div class="container2">
                     Title section 
                    <div class="title">Add an Event!</div>
                    <div class="content">
        
        
        
                         Registration form 
                        <form action="Add_Event.php" method="POST" enctype="multipart/form-data">
                            <div class="user-details">
                                <div class="input-box">
                                    <span class="details">Event Name</span>
                                    <input type="text" name="event_name" placeholder="Enter Event Name" required>
                                </div>
        
                                <div class="input-box">
                                    <span class="details">Event Location</span>
                                    <input type="text" name="event_location" placeholder="Enter Event Location" required>
                                </div>
        
                                <div class="input-box">
                                    <span class="details">Maximum Number of Participants per team</span>
                                    <input type="number" name="max_participants" placeholder="Max Num of Participants per team" required>
                                </div>
        
                                <div class="input-box">
                                    <span class="details">Date</span>
                                    <input type="date" name="event_date" required>
                                </div>
        
                                <div class="input-box">
                                    <span class="details">Event Description</span>
                                    <textarea name="event_description" rows="6" cols="40" required></textarea>
                                </div>
        
                                <div class="input-box">
                                    <span class="details">Registration Deadline</span>
                                    <input type="date" name="registration_deadline" required>
                                </div>
        
                                 ✅ خانة رفع الصورة بعد "Registration Deadline" 
                                <div class="input-box">
                                    <span class="details">Event Banner/Image</span>
                                    <label for="eventImage" id="uploadLabel">
                                        <img src="images/upload3.png" alt="Upload Icon" id="uploadIcon">
                                    </label>
                                    <input type="file" name="event_image" id="eventImage" accept="image/*" required>
                                </div>
                            </div>
        
                            <div class="type-details">
                                <input type="radio" name="event_type" id="dot-1" value="Hackathon">
                                <input type="radio" name="event_type" id="dot-2" value="Workshop">
                                <span class="Event-title">Event Type</span>
                                <div class="category">
                                    <label for="dot-1">
                                        <span class="dot one"></span>
                                        <span class="Event_type">Hackathon</span>
                                    </label>
                                    <label for="dot-2">
                                        <span class="dot two"></span>
                                        <span class="Event_type">Workshop</span>
                                    </label>
                                </div>
                            </div>
        
                            <div class="button">
                                <input type="submit" value="Register">
                            </div>
                        </form>
        
        
        
                    </div>
                </div>
            </div>-->



        <footer class="container">
            <span class="blur"></span>
            <span class="blur"></span>
            <div class="column">
                <div class="logo">
                    <img src="images/logo_ruaa.png" alt="Ruaa Logo">
                </div>
                <p>
                    Connecting innovators, fostering collaboration, and hosting top-tier hackathons & workshops worldwide.
                </p>
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

        <script src="Script Home Page.js"></script>
        <script src="addEvent.js"></script>

    </body>

</html>
