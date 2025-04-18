
class TeamRequestSender {
    constructor() {
        this.sendButton = document.querySelector(".send-button");
        this.teamEmailInput = document.querySelector("#team-email");
        this.teamMembersContainer = document.querySelector(".team-column-left");

        this.sendButton.addEventListener("click", () => this.sendRequest());
        this.renderTeamMembers(); 
    }

    sendRequest() {
        const email = this.teamEmailInput.value.trim();
        if (email) {
            let teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];

            if (!teamRequests.some(request => request.email === email)) {
                teamRequests.push({ email: email });
                localStorage.setItem("teamRequests", JSON.stringify(teamRequests));

                this.renderTeamMembers(); 
                alert(`Request sent to ${email}!`);
            } else {
                alert(`You have already sent a request to ${email}.`);
            }

            this.teamEmailInput.value = ""; 
        }
    }

    renderTeamMembers() {
        this.teamMembersContainer.innerHTML = ""; 

        let teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];

        teamRequests.forEach((request, index) => {
            const newField = document.createElement("div");
            newField.classList.add("team-field");
            newField.innerHTML = `
                <span class="team-email-text">${request.email}</span>
                <button class="remove-btn" data-index="${index}">&times;</button>
            `;

            newField.querySelector(".remove-btn").addEventListener("click", () => {
                this.removeTeamMember(index);
            });

            this.teamMembersContainer.appendChild(newField);
        });
    }

  removeTeamMember(index) {
    let teamRequests = JSON.parse(localStorage.getItem("teamRequests")) || [];
    
    
    let removedEmail = teamRequests[index].email;

    teamRequests.splice(index, 1);
    localStorage.setItem("teamRequests", JSON.stringify(teamRequests));

    let notifications = JSON.parse(localStorage.getItem("teamRequests")) || [];
    let updatedNotifications = notifications.filter(request => request.email !== removedEmail);
    localStorage.setItem("teamRequests", JSON.stringify(updatedNotifications));

    this.renderTeamMembers();
}

}

document.addEventListener("DOMContentLoaded", function () {
    new TeamRequestSender();
});

document.addEventListener("DOMContentLoaded", function () {
    new TeamRequestSender();
});

document.addEventListener("DOMContentLoaded", function () {
    const teamLookButton = document.querySelector(".team-button-look");
    const teamHaveButton = document.querySelector(".team-button-have");
    const sendButton = document.querySelector(".send-button");
    const teamEmailInput = document.querySelector("#team-email");
    const teamMembersContainer = document.querySelector(".team-column-left");

    sendButton.addEventListener("click", function () {
        const email = teamEmailInput.value.trim();
        if (email) {
            const newField = document.createElement("div");
            newField.classList.add("team-field");
            newField.innerHTML = `
  <span>${email}</span>
  <button class="remove-btn">&times;</button>
`;

            
            newField.querySelector(".remove-btn").addEventListener("click", function () {
              teamMembersContainer.removeChild(newField);
            });
            
            teamMembersContainer.appendChild(newField);
            teamEmailInput.value = "";
        }
    });

    teamHaveButton.classList.add("selected");
    teamHaveButton.style.backgroundColor = "white";
    teamHaveButton.style.color = "#8940d3";

    teamHaveButton.addEventListener("click", function () {
        teamHaveButton.classList.add("selected");
        teamHaveButton.style.backgroundColor = "white";
        teamHaveButton.style.color = "#8940d3";
        teamLookButton.classList.remove("selected");
        teamLookButton.style.backgroundColor = "#8940d3";
        teamLookButton.style.color = "white";
    });

    teamLookButton.addEventListener("click", function () {
        window.location.href = "Looking_For_Team.html";
    });
});
