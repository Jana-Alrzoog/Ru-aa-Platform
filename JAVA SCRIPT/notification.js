// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Get all user cards
    const userCards = document.querySelectorAll(".user-card");
  
    // Add event listeners to each card's buttons
    userCards.forEach((card, index) => {
      const acceptBtn = card.querySelector(".accept-btn");
      const rejectBtn = card.querySelector(".reject-btn");
      const username = card.querySelector(".username").textContent;
  
      // Add click event for Accept button
      acceptBtn.addEventListener("click", function () {
        handleAccept(card, username, index);
      });
  
      // Add click event for Reject button
      rejectBtn.addEventListener("click", function () {
        handleReject(card, username, index);
      });
    });
  
    // Function to handle Accept button click
    function handleAccept(card, username, index) {
      // Create notification
      showNotification(`Accepted ${username}`, "success");
  
      // Visual feedback
      card.style.transition = "all 0.5s ease";
      card.style.backgroundColor = "rgba(144, 238, 144, 0.3)";
      card.style.borderLeft = "5px solid #4CAF50";
  
      // Disable buttons
      disableButtons(card);
  
      // Log action (in a real app, this would be an API call)
      console.log(`User ${username} (ID: ${index + 1}) has been accepted`);
    }
  
    // Function to handle Reject button click
    function handleReject(card, username, index) {
      // Create notification
      showNotification(`Rejected ${username}`, "error");
  
      // Visual feedback - fade out and reduce height
      card.style.transition = "all 0.8s ease";
      card.style.opacity = "0.6";
      card.style.backgroundColor = "rgba(255, 99, 71, 0.1)";
      card.style.borderLeft = "5px solid #FF6347";
  
      // Disable buttons
      disableButtons(card);
  
      // Log action (in a real app, this would be an API call)
      console.log(`User ${username} (ID: ${index + 1}) has been rejected`);
    }
  
    // Function to disable buttons after action
    function disableButtons(card) {
      const buttons = card.querySelectorAll("button");
      buttons.forEach((button) => {
        button.disabled = true;
        button.style.opacity = "0.5";
        button.style.cursor = "default";
      });
    }
  
    // Function to show notification
    function showNotification(message, type) {
      // Create notification element
      const notification = document.createElement("div");
      notification.className = `notification ${type}`;
      notification.textContent = message;
  
      // Style based on type
      if (type === "success") {
        notification.style.backgroundColor = "rgba(76, 175, 80, 0.9)";
      } else if (type === "error") {
        notification.style.backgroundColor = "rgba(255, 99, 71, 0.9)";
      }
  
      // Add styles
      notification.style.color = "white";
      notification.style.padding = "12px 20px";
      notification.style.borderRadius = "4px";
      notification.style.position = "fixed";
      notification.style.top = "20px";
      notification.style.right = "20px";
      notification.style.zIndex = "1000";
      notification.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
      notification.style.fontFamily = "Poppins, sans-serif";
  
      // Add to DOM
      document.body.appendChild(notification);
  
      // Remove after 3 seconds
      setTimeout(() => {
        notification.style.opacity = "0";
        notification.style.transition = "opacity 0.5s ease";
  
        // Remove from DOM after fade out
        setTimeout(() => {
          document.body.removeChild(notification);
        }, 500);
      }, 3000);
    }
  });
  
  
  
 class TeamRequestsManager {
    constructor() {
        this.requestContainer = document.querySelector(".card-container");
        this.teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];
        this.teamMembers = JSON.parse(localStorage.getItem("teamMembers")) || [];
        this.renderRequests();
    }
    
    

    renderRequests() {
        this.requestContainer.innerHTML = ""; // تفريغ الطلبات السابقة

        this.teamRequests.forEach((request, index) => {
            const requestCard = document.createElement("article");
            requestCard.classList.add("user-card");

            requestCard.innerHTML = `
                <div class="user-info">
                    <img src="https://via.placeholder.com/50" alt="User" class="user-avatar" />
                    <h3 class="username">${request.email}</h3>
                </div>
                <div class="action-buttons">
                    <button class="accept-btn" data-index="${index}">Accept</button>
                    <button class="reject-btn" data-index="${index}">Reject</button>
                </div>
            `;

            this.requestContainer.appendChild(requestCard);
        });

        document.querySelectorAll(".accept-btn").forEach(button => {
            button.addEventListener("click", (event) => {
                this.acceptRequest(event.target.dataset.index);
            });
        });

        document.querySelectorAll(".reject-btn").forEach(button => {
            button.addEventListener("click", (event) => {
                this.rejectRequest(event.target.dataset.index);
            });
        });
    }

    acceptRequest(index) {
        let acceptedUser = this.teamRequests[index];

        // إضافة العضو المقبول إلى قائمة الفريق
        let teamMembers = JSON.parse(localStorage.getItem("teamMembers")) || [];
        teamMembers.push(acceptedUser);
        localStorage.setItem("teamMembers", JSON.stringify(teamMembers));

        // تحديث قائمة الفريق في الملف الشخصي
        let profileMembers = JSON.parse(localStorage.getItem("profileTeamMembers")) || [];
        profileMembers.push(acceptedUser);
        localStorage.setItem("profileTeamMembers", JSON.stringify(profileMembers));

        // حذف الطلب بعد القبول
        this.teamRequests.splice(index, 1);
        localStorage.setItem("teamRequests", JSON.stringify(this.teamRequests));

        this.renderRequests();
    }

    rejectRequest(index) {
        this.teamRequests.splice(index, 1);
        localStorage.setItem("teamRequests", JSON.stringify(this.teamRequests));
        this.renderRequests();
    }
}
document.addEventListener("DOMContentLoaded", function () {
    const requestContainer = document.querySelector(".card-container");

    function renderRequests() {
        let teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];

        requestContainer.innerHTML = "";
        if (teamRequests.length === 0) {
            requestContainer.innerHTML = "<p style='color: white; text-align: center;'>No team requests yet.</p>";
            return;
        }

        teamRequests.forEach((request, index) => {
            const requestCard = document.createElement("article");
            requestCard.classList.add("user-card");

            requestCard.innerHTML = `
                <div class="user-info">
                    <img src="https://via.placeholder.com/50" alt="User" class="user-avatar" />
                    <h3 class="username">${request.name}</h3>
                    <p class="user-email">${request.email}</p>
                </div>
                <div class="action-buttons">
                    <button class="accept-btn" data-index="${index}">Accept</button>
                    <button class="reject-btn" data-index="${index}">Reject</button>
                </div>
            `;

            requestContainer.appendChild(requestCard);
        });

        document.querySelectorAll(".accept-btn").forEach(button => {
            button.addEventListener("click", (event) => {
                acceptRequest(event.target.dataset.index);
            });
        });

        document.querySelectorAll(".reject-btn").forEach(button => {
            button.addEventListener("click", (event) => {
                rejectRequest(event.target.dataset.index);
            });
        });
    }

    function acceptRequest(index) {
        let teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];
        let acceptedUser = teamRequests[index];

        let teamMembers = JSON.parse(localStorage.getItem("profileTeamMembers")) || [];
        teamMembers.push(acceptedUser);
        localStorage.setItem("profileTeamMembers", JSON.stringify(teamMembers));

        teamRequests.splice(index, 1);
        localStorage.setItem("teamRequests", JSON.stringify(teamRequests));

        renderRequests();
    }

    function rejectRequest(index) {
        let teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];
        teamRequests.splice(index, 1);
        localStorage.setItem("teamRequests", JSON.stringify(teamRequests));

        renderRequests();
    }

    renderRequests();
});

document.addEventListener("DOMContentLoaded", function () {
    new TeamRequestsManager();
});