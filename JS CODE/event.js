document.addEventListener("DOMContentLoaded", function () {
    const eventContainer = document.querySelector(".event-cards");
    const searchInput = document.querySelector(".search-input");
    const hackathonFilter = document.getElementById("hackathon");
    const workshopFilter = document.getElementById("workshop");
    const upcomingFilter = document.getElementById("upcoming");
    const ongoingFilter = document.getElementById("ongoing");

  
    hackathonFilter.checked = false;
    workshopFilter.checked = false;
    upcomingFilter.checked = false;
    ongoingFilter.checked = false;

   
    let events = JSON.parse(localStorage.getItem("events")) || [];
    let registeredEvents = JSON.parse(localStorage.getItem("registeredEvents")) || [];

    function displayEvents(filteredEvents) {
        eventContainer.innerHTML = "";

        if (filteredEvents.length === 0) {
            eventContainer.innerHTML = "<p class='no-results'>No matching events found.</p>";
            return;
        }

        filteredEvents.forEach((event, index) => {
            const eventCard = document.createElement("div");
            eventCard.classList.add("event-card");

            let isRegistered = registeredEvents.includes(index);

            eventCard.innerHTML = `
                <img src="${event.eventImage}" alt="Event Image" class="event-image">
               <h3 class="event-title">${event.eventName}</h3>
                <p><strong>Type:</strong> ${event.eventType}</p>
                <p><strong>Start Date:</strong> ${event.eventDate} <strong> at ${event.eventTime}</strong></p>
                
                <p><strong>Location:</strong> ${event.eventLocation}</p>
                
                <p><strong>Registration Deadline:</strong> ${event.registrationDeadline}</p>
                <p class="event-description">${event.eventDescription}</p>
                <button class="register-btn ${isRegistered ? 'registered' : ''}" data-index="${index}">
                    ${isRegistered ? 'Registered' : 'Register'}
                </button>
            `;

            eventContainer.appendChild(eventCard);
        });

    
        document.querySelectorAll(".register-btn").forEach(button => {
            button.addEventListener("click", function () {
                let eventIndex = this.getAttribute("data-index");

                if (!registeredEvents.includes(eventIndex)) {
                    let confirmRegister = confirm("Are you sure you want to register for this event?");
                    if (!confirmRegister) return;

                    registeredEvents.push(eventIndex);
                    localStorage.setItem("registeredEvents", JSON.stringify(registeredEvents));

                    this.textContent = "Registered";
                    this.classList.add("registered");
                }
            });
        });
    }

    function filterEvents() {
        let query = searchInput.value.toLowerCase();

        let showAllTypes = !hackathonFilter.checked && !workshopFilter.checked;
        let showAllDates = !upcomingFilter.checked && !ongoingFilter.checked;

        let filteredEvents = events.filter(event => {
            let matchesSearch = event.eventName.toLowerCase().includes(query);

            let matchesType = showAllTypes || 
                              (hackathonFilter.checked && event.eventType === "Hackathon") ||
                              (workshopFilter.checked && event.eventType === "Workshop");

            let eventStartDate = new Date(event.eventDate);
            let eventEndDate = new Date(event.eventEndDate);
            let today = new Date();

            let matchesDate = showAllDates || 
                              (upcomingFilter.checked && eventStartDate > today) || 
                              (ongoingFilter.checked && eventStartDate <= today && eventEndDate >= today);

            return matchesSearch && matchesType && matchesDate;
        });

        displayEvents(filteredEvents);
    }

   
    displayEvents(events);

    
    searchInput.addEventListener("keyup", filterEvents);

   
    hackathonFilter.addEventListener("change", filterEvents);
    workshopFilter.addEventListener("change", filterEvents);
    upcomingFilter.addEventListener("change", filterEvents);
    ongoingFilter.addEventListener("change", filterEvents);
});
