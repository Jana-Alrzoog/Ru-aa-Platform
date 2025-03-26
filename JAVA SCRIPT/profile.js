document.addEventListener("DOMContentLoaded", function () {
    // عناصر الصفحة
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

    // تحميل صورة الملف الشخصي
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

    // إظهار/إخفاء الأحداث المسجلة
    showEventsBtn.addEventListener("click", function () {
        registeredEvents.style.display = (registeredEvents.style.display === "block") ? "none" : "block";
        updateEventsVisibility();
    });

    // حذف حدث من القائمة
   document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-event-btn")) {
        const eventItem = e.target.closest(".event-item");
        const title = eventItem.querySelector(".event-name").textContent;

        if (confirm("Are you sure you want to remove this event?")) {
            // Send fetch request to remove_event.php
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
                } else {
                    alert("Failed to remove event: " + data.error);
                }
            });
        }
    }
});


    // تحديث ظهور قائمة الأحداث
    function updateEventsVisibility() {
        eventsList.style.display = (eventsList.children.length === 0) ? "none" : "block";
        noEventsMessage.style.display = (eventsList.children.length === 0) ? "block" : "none";
    }

    // عرض نافذة تعديل الملف الشخصي
    editProfileBtn.addEventListener("click", function () {
        editProfileModal.style.display = "flex";
    });

    closeModal.addEventListener("click", function () {
        editProfileModal.style.display = "none";
    });

    window.addEventListener("click", function (e) {
        if (e.target === editProfileModal) {
            editProfileModal.style.display = "none";
        }
    });

    // تحديث معلومات المستخدم
    editProfileForm.addEventListener("submit", function (e) {
        e.preventDefault();
        document.getElementById("username-display").textContent = document.getElementById("username").value;
        document.getElementById("email-display").textContent = document.getElementById("email").value;
        document.getElementById("experiences-display").innerHTML = document.getElementById("experiences").value.replace(/\n/g, "<br>");
        editProfileModal.style.display = "none";
        alert("Profile updated successfully!");
    });

    updateEventsVisibility();
});
document.addEventListener("DOMContentLoaded", function () {
    // عناصر الصفحة
    const teamContainer = document.getElementById("team-members");

    class ProfileManager {
        constructor() {
            this.teamMembers = JSON.parse(localStorage.getItem("profileTeamMembers")) || [];
            this.renderTeamMembers();
        }

        renderTeamMembers() {
            teamContainer.innerHTML = "";

            if (this.teamMembers.length === 0) {
                teamContainer.innerHTML = "<p style='color: white; text-align: center;'>No team members yet.</p>";
                return;
            }

            this.teamMembers.forEach((member, index) => {
                const memberCard = document.createElement("div");
                memberCard.classList.add("team-member-card");

                memberCard.innerHTML = `
                    <div class="team-member-avatar"></div>
                    <div class="team-member-info">
                        <p class="team-member-name">${member.name}</p>
                        <p class="team-member-email">${member.email}</p>
                        <button class="delete-member-btn" data-index="${index}">Remove</button>
                    </div>
                `;

                teamContainer.appendChild(memberCard);
            });

            // إعادة إضافة الأحداث لأزرار الحذف بعد تحديث القائمة
            document.querySelectorAll(".delete-member-btn").forEach((button) => {
                button.addEventListener("click", function () {
                    const index = this.getAttribute("data-index");
                    removeTeamMember(index);
                });
            });
        }
    }

    function removeTeamMember(index) {
        let teamMembers = JSON.parse(localStorage.getItem("profileTeamMembers")) || [];

        // عرض رسالة تأكيد قبل الحذف
        const confirmDelete = confirm(`Are you sure you want to remove ${teamMembers[index].name}?`);

        if (confirmDelete) {
            teamMembers.splice(index, 1); // حذف العضو من المصفوفة
            localStorage.setItem("profileTeamMembers", JSON.stringify(teamMembers)); // تحديث البيانات في localStorage
            renderTeamMembers(); // إعادة عرض القائمة بدون العضو المحذوف
        }
    }

    function addTeamMember(name, email) {
        let teamMembers = JSON.parse(localStorage.getItem("profileTeamMembers")) || [];
        teamMembers.push({ name, email });
        localStorage.setItem("profileTeamMembers", JSON.stringify(teamMembers));
        renderTeamMembers();
    }

    function renderTeamMembers() {
        let teamMembers = JSON.parse(localStorage.getItem("profileTeamMembers")) || [];
        teamContainer.innerHTML = "";

        if (teamMembers.length === 0) {
            teamContainer.innerHTML = "<p style='color: white; text-align: center;'>No team members yet.</p>";
            return;
        }

        teamMembers.forEach((member, index) => {
            const memberCard = document.createElement("div");
            memberCard.classList.add("team-member-card");

            memberCard.innerHTML = `
                <div class="team-member-avatar"></div>
                <div class="team-member-info">
                    <p class="team-member-name">${member.name}</p>
                    <p class="team-member-email">${member.email}</p>
                    <button class="delete-member-btn" data-index="${index}">Remove</button>
                </div>
            `;

            teamContainer.appendChild(memberCard);
        });

        document.querySelectorAll(".delete-member-btn").forEach((button) => {
            button.addEventListener("click", function () {
                const index = this.getAttribute("data-index");
                removeTeamMember(index);
            });
        });
    }

    renderTeamMembers();
});