document.addEventListener("DOMContentLoaded", function () {
    const participantsContainer = document.getElementById("participants-container");

    // إضافة Event Listener لجميع الأزرار
    participantsContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("save-email-btn")) {
            const email = e.target.getAttribute("data-email");
            saveEmail(email);
        }
    });

    // دالة لحفظ الإيميل في قاعدة البيانات
    function saveEmail(email) {
        fetch("API.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                action: "save_email",
                email: email,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                alert(data.message); // عرض رسالة التأكيد
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    }
});


const leaveTeamBtn = document.getElementById("leave-team-btn");

const currentUserId = 99; 
const currentUser = {
    id: currentUserId,
    name: "My Name",
    email: "myemail@example.com",
    avatar: "",
    profile: "/profile/me",
    selected: false,
};

document.addEventListener("DOMContentLoaded", function () {
    const state = {
        selectedParticipants: new Set(),
        showRequestSent: false,
        participants: [
            {
                id: 1,
                name: "Sarah Ahmed",
                avatar: "",
                profile: "/profile/sarah",
                selected: false,
            },
            {
                id: 2,
                name: "Mohammed Ali",
                avatar: "",
                profile: "/profile/mohammed",
                selected: false,
            },
            {
                id: 3,
                name: "Fatima Hassan",
                avatar: "",
                profile: "/profile/fatima",
                selected: false,
            },
            {
                id: 4,
                name: "Omar Khalid",
                avatar: "",
                profile: "/profile/omar",
                selected: false,
            },
        ],
    };

    const participantsContainer = document.getElementById(
            "participants-container",
            );
    const sendRequestBtn = document.getElementById("send-request-btn");
    const selectionInfo = document.getElementById("selection-info");
    const selectedCount = document.getElementById("selected-count");
    const pluralS = document.getElementById("plural-s");
    const joinTeamBtn = document.querySelector(".join-team-btn");

    // Render participants
    function renderParticipants() {
        participantsContainer.innerHTML = "";

        state.participants.forEach((participant) => {
            const card = document.createElement("div");
            card.className = "participant-card";
            card.dataset.id = participant.id;

           
            if (participant.id === currentUserId) {
                card.classList.add("current-user"); 
                card.innerHTML = `
            <div class="participant-avatar"></div>
            <a href="${participant.profile}" class="participant-name">${participant.name}</a>
            <button class="remove-btn">&times;</button>
          `;

               
                card.querySelector(".remove-btn").addEventListener("click", function () {
                    removeCurrentUser();
                });
            } else {
                card.innerHTML = `
            <div class="participant-avatar"></div>
            <p class="participant-name">${participant.name}</p>
          `;
                card.addEventListener("click", () => toggleSelection(participant.id));
            }

            participantsContainer.appendChild(card);
        });

        
        if (state.participants.length > 4) {
            participantsContainer.style.overflowY = "auto";
        } else {
            participantsContainer.style.overflowY = "hidden";
        }
    }
 function removeCurrentUser() {
    state.participants = state.participants.filter((p) => p.id !== currentUserId);
    updateUI();
    
    // إعادة التوجيه بعد الحذف
    setTimeout(() => {
        window.history.back(); // يعيد المستخدم إلى الصفحة السابقة
    }, 500); // بعد نصف ثانية لإعطاء وقت للحذف
}



    // Toggle participant selection
    function toggleSelection(participantId) {
        const participant = state.participants.find(
                (p) => p.id === participantId,
                );
        if (participant) {
            participant.selected = !participant.selected;

            if (participant.selected) {
                state.selectedParticipants.add(participantId);
            } else {
                state.selectedParticipants.delete(participantId);
            }

            updateUI();
        }
    }

    // Update UI based on state
    function updateUI() {
        renderParticipants();

        const count = state.selectedParticipants.size;
        selectedCount.textContent = count;

        if (count > 0) {
            selectionInfo.style.display = "block";
            pluralS.style.display = count !== 1 ? "inline" : "none";
        } else {
            selectionInfo.style.display = "none";
        }

        if (state.showRequestSent) {
            sendRequestBtn.textContent = "Request Sent!";
            sendRequestBtn.style.background = "#4CAF50";
        } else {
            sendRequestBtn.textContent = "Send Request";
            sendRequestBtn.style.background = "#8940D3";
        }
    }

    // Send request function
    function sendRequest() {
        if (state.selectedParticipants.size === 0) {
            return; 
        }

        let notifications = JSON.parse(localStorage.getItem("teamRequests")) || [];

        const selectedUsers = state.participants
                .filter((p) => state.selectedParticipants.has(p.id))
                .map((p) => ({
                        id: p.id,
                        name: p.name,
                        email: p.name.toLowerCase().replace(" ", "") + "@example.com",
                        status: "pending"
                    }));

        notifications.push(...selectedUsers);
        localStorage.setItem("teamRequests", JSON.stringify(notifications));

        alert(`Request sent to: ${selectedUsers.map((p) => p.name).join(", ")}`);

        
        sendRequestBtn.textContent = "Request Sent!";
    }


    // Join team function
    function joinTeam() {
        alert("Request to join team sent!");
    }

    document.getElementById("leave-team-btn").addEventListener("click", function () {
        window.location.href = "Team_Form.html"; 
    });


    // Event listeners
    sendRequestBtn.addEventListener("click", sendRequest);
    joinTeamBtn.addEventListener("click", joinTeam);

    // Initial render
    renderParticipants();
});


// ✅ إرسال الإيميل عند الضغط على Be One Of Them
document.getElementById("beOneOfThemBtn").addEventListener("click", function () {
    const email = document.getElementById("userEmail").value; // استخدم الإيميل من الفورم
    const eventTitle = "AI Hackathon"; // اسم الحدث الافتراضي

    if (email) {
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
            location.reload(); // إعادة تحميل الصفحة بعد الإضافة
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    } else {
        alert("Please enter a valid email.");
    }
});

// ✅ إرسال الدعوات عند الضغط على Send Request
document.getElementById("sendRequestBtn").addEventListener("click", function () {
    const selectedEmails = getSelectedEmails(); // جلب الإيميلات المحددة
    const teamName = document.getElementById("teamName").value;

    if (selectedEmails.length > 0 && teamName) {
        fetch("API.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                action: "send_request",
                emails: selectedEmails,
                team_name: teamName,
            }),
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
            location.reload(); // إعادة تحميل الصفحة بعد الإرسال
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    } else {
        alert("Please select members and enter a team name.");
    }
});

// ✅ جلب الإيميلات المحددة من القائمة
function getSelectedEmails() {
    const selected = [];
    document.querySelectorAll('input[name="participant"]:checked').forEach((checkbox) => {
        selected.push(checkbox.value);
    });
    return selected;
}
