document.addEventListener("DOMContentLoaded", function () {
    // Page elements
    const profileImagePreview = document.getElementById("profileImagePreview");
    const profileImageInput = document.getElementById("profileImageInput");
    const uploadTrigger = document.getElementById("uploadTrigger");
    const showEventsBtn = document.getElementById("showEventsBtn");
    const editProfileBtn = document.getElementById("editProfileBtn");
    const registeredEvents = document.getElementById("registeredEvents");
    const eventsList = document.getElementById("eventsList");
    const noEventsMessage = document.getElementById("noEventsMessage");
    const editProfileModal = document.getElementById("editProfileModal");
    const closeModal = document.getElementById("closeModal");
    const editProfileForm = document.getElementById("editProfileForm");
    const teamContainer = document.getElementById("team-members");

    // Upload profile image
    uploadTrigger.addEventListener("click", function () {
        profileImageInput.click();
    });

    profileImagePreview.addEventListener("click", function () {
        profileImageInput.click();
    });

    profileImageInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profileImagePreview.innerHTML = `<img src="${e.target.result}" alt="Profile Image">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Show/hide registered events
    showEventsBtn.addEventListener("click", function () {
        registeredEvents.style.display = (registeredEvents.style.display === "block") ? "none" : "block";
        updateEventsVisibility();
    });

    // Remove event from list
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-event-btn")) {
            const eventItem = e.target.closest(".event-item");
            const title = e.target.getAttribute("data-title");

            if (confirm("Are you sure you want to unregister from this event?")) {
                fetch("remove_event.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "title=" + encodeURIComponent(title),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        eventItem.remove();
                        updateEventsVisibility();
                        
                        if (document.querySelectorAll(".event-item").length === 0) {
                            noEventsMessage.style.display = "block";
                        }
                    } else {
                        alert("Failed to remove event: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while removing the event.");
                });
            }
        }
    });

    // Remove team member
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("delete-member-btn")) {
            const memberEmail = e.target.getAttribute("data-email");
            const teamName = e.target.getAttribute("data-team");

            if (confirm("Are you sure you want to remove this team member?")) {
                fetch("remove_team_member.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `email=${encodeURIComponent(memberEmail)}&team=${encodeURIComponent(teamName)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to reflect changes
                        window.location.reload();
                    } else {
                        alert("Failed to remove team member: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while removing the team member.");
                });
            }
        }
    });

    // Make event details clickable
    document.addEventListener("click", function (e) {
        if (e.target.closest(".event-details")) {
            const eventItem = e.target.closest(".event-item");
            const title = eventItem.querySelector(".event-name").textContent;
            window.location.href = `event.php?title=${encodeURIComponent(title)}`;
        }
    });

    // Update events visibility
    function updateEventsVisibility() {
        const hasEvents = eventsList.querySelectorAll(".event-item").length > 0;
        eventsList.style.display = hasEvents ? "block" : "none";
        noEventsMessage.style.display = hasEvents ? "none" : "block";
    }

    // Show edit profile modal
    editProfileBtn.addEventListener("click", function () {
        editProfileModal.style.display = "flex";
    });

    // Close modal
    closeModal.addEventListener("click", function () {
        editProfileModal.style.display = "none";
    });

    window.addEventListener("click", function (e) {
        if (e.target === editProfileModal) {
            editProfileModal.style.display = "none";
        }
    });

    // Update user profile
    editProfileForm.addEventListener("submit", function (e) {
        e.preventDefault();
        document.getElementById("username-display").textContent = document.getElementById("username").value;
        document.getElementById("email-display").textContent = document.getElementById("email").value;
        document.getElementById("experiences-display").innerHTML = document.getElementById("experiences").value.replace(/\n/g, "<br>");
        editProfileModal.style.display = "none";
        alert("Profile updated successfully!");
    });

    // Initialize
    updateEventsVisibility();
});

document.addEventListener("DOMContentLoaded", function () {
    // Existing elements and event listeners...

    // Leave Team Functionality
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("leave-team-btn")) {
            const teamName = e.target.getAttribute("data-team");
            
            if (confirm("Are you sure you want to leave this team?")) {
                fetch("leave_team.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "team=" + encodeURIComponent(teamName)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert("Failed to leave team: " + data.error);
                    }
                });
            }
        }
    });

    // Delete Team Functionality
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("delete-team-btn")) {
            const teamName = e.target.getAttribute("data-team");
            
            if (confirm("Are you sure you want to delete this team? This cannot be undone!")) {
                fetch("delete_team.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "team=" + encodeURIComponent(teamName)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert("Failed to delete team: " + data.error);
                    }
                });
            }
        }
    });

    // Existing code...
});