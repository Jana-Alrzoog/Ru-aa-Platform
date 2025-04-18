document.addEventListener("DOMContentLoaded", function () {
    // Get page elements
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

    // Profile image upload: trigger file input click
    uploadTrigger.addEventListener("click", function () {
        profileImageInput.click();
    });

    profileImagePreview.addEventListener("click", function () {
        profileImageInput.click();
    });

    // Display selected image preview
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

    // Toggle registered events display
    showEventsBtn.addEventListener("click", function () {
        registeredEvents.style.display = (registeredEvents.style.display === "block") ? "none" : "block";
        updateEventsVisibility();
    });

   
    // Make event details clickable
    document.addEventListener("click", function (e) {
        if (e.target.closest(".event-details")) {
            const eventItem = e.target.closest(".event-item");
            const title = eventItem.querySelector(".event-name").textContent;
window.location.href = `event.php?title=${encodeURIComponent(title)}`;
        }
    });

    // Function to update events visibility
    function updateEventsVisibility() {
        const hasEvents = eventsList.querySelectorAll(".event-item").length > 0;
        eventsList.style.display = hasEvents ? "block" : "none";
        noEventsMessage.style.display = hasEvents ? "none" : "block";
    }

    // Show edit profile modal
    editProfileBtn.addEventListener("click", function () {
        editProfileModal.style.display = "flex";
    });

    // Close edit profile modal on close button click
    closeModal.addEventListener("click", function () {
        editProfileModal.style.display = "none";
    });

    // Close modal if clicked outside of it
    window.addEventListener("click", function (e) {
        if (e.target === editProfileModal) {
            editProfileModal.style.display = "none";
        }
    });

    // Update user profile via AJAX: send updated username and experiences to update_profile.php
    editProfileForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const username = document.getElementById("username").value;
        const experiences = document.getElementById("experiences").value;
        const postData = "username=" + encodeURIComponent(username) +
                         "&experiences=" + encodeURIComponent(experiences);
        fetch("update_profile.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: postData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("username-display").textContent = username;
                document.getElementById("experiences-display").innerHTML = experiences.replace(/\n/g, "<br>");
                editProfileModal.style.display = "none";
                alert("Profile updated successfully!");
            } else {
                alert("Failed to update profile: " + data.error);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while updating profile.");
        });
    });

    // Initialize events visibility on page load
    updateEventsVisibility();
});

