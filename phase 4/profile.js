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

    // Upload profile image
    uploadTrigger?.addEventListener("click", () => profileImageInput?.click());
    profileImagePreview?.addEventListener("click", () => profileImageInput?.click());

    profileImageInput?.addEventListener("change", function () {
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
    showEventsBtn?.addEventListener("click", function () {
        if (registeredEvents) {
            registeredEvents.style.display = (registeredEvents.style.display === "block") ? "none" : "block";
            updateEventsVisibility();
        }
    });





    // Click on event details redirects
    document.addEventListener("click", function (e) {
        if (e.target.closest(".event-details")) {
            const eventItem = e.target.closest(".event-item");
            const title = eventItem.querySelector(".event-name").textContent;
            window.location.href = `event.php?title=${encodeURIComponent(title)}`;
        }
    });

    // Show edit profile modal
    editProfileBtn?.addEventListener("click", () => {
        editProfileModal.style.display = "flex";
    });

    // Close modal
    closeModal?.addEventListener("click", () => {
        editProfileModal.style.display = "none";
    });

    window.addEventListener("click", function (e) {
        if (e.target === editProfileModal) {
            editProfileModal.style.display = "none";
        }
    });

    // Update profile info
    editProfileForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        document.getElementById("username-display").textContent = document.getElementById("username").value;
        document.getElementById("email-display").textContent = document.getElementById("email").value;
        document.getElementById("experiences-display").innerHTML = document.getElementById("experiences").value.replace(/\n/g, "<br>");
        editProfileModal.style.display = "none";
        alert("Profile updated successfully!");
    });



    // 🔁 Function to update events section visibility
    function updateEventsVisibility() {
        if (!eventsList || !noEventsMessage) return;

        const hasEvents = eventsList.querySelectorAll(".event-item").length > 0;
        eventsList.style.display = hasEvents ? "block" : "none";
        noEventsMessage.style.display = hasEvents ? "none" : "block";
    }

    // Run on load
    updateEventsVisibility();
});
