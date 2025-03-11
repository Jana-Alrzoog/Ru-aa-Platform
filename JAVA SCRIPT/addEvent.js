document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); 

        
        const eventName = document.querySelector('input[placeholder="Enter Event Name"]').value.trim();
        const eventDate = document.querySelector('input[type="date"]').value;
        const eventTime = document.querySelector('input[type="time"]').value;
        const eventLocation = document.querySelector('input[placeholder="Enter Event Location"]').value.trim();
        const maxParticipants = document.querySelector('input[placeholder="Enter Maximum Number of Participants per team"]').value.trim();
        const registrationDeadline = document.querySelectorAll('input[type="date"]')[1].value;
        const eventDescription = document.querySelector('textarea[name="event_description"]').value.trim();
        const eventImageInput = document.getElementById("eventImage");
        
        let eventType = "";
        if (document.getElementById("dot-1").checked) {
            eventType = "Hackathon";
        } else if (document.getElementById("dot-2").checked) {
            eventType = "Workshop";
        }

        
        if (!eventName || !eventDate || !eventTime || !eventLocation || !maxParticipants || 
            !registrationDeadline || !eventDescription || !eventType || !eventImageInput.files.length) {
            alert("Please fill out all required fields before submitting.");
            return; 
        }

       
        const file = eventImageInput.files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            const eventImage = reader.result; 

            
            let confirmAddEvent = confirm(`Are you sure you want to add this event?\n\nEvent Name: ${eventName}\nEvent Type: ${eventType}`);
            if (!confirmAddEvent) {
                return; 
            }

          
            const newEvent = {
                eventName,
                eventDate,
                eventTime,
                eventLocation,
                maxParticipants,
                registrationDeadline,
                eventDescription,
                eventType,
                eventImage
            };

            
            let events = JSON.parse(localStorage.getItem("events")) || [];

         
            events.push(newEvent);

         
            localStorage.setItem("events", JSON.stringify(events));

           
            alert("Event added successfully!");

         
            window.location.href = "event.html";
        };

        reader.readAsDataURL(file);
    });
});


document.getElementById("eventImage").addEventListener("change", function(event) {
    const file = event.target.files[0]; // Get the uploaded file
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("uploadIcon").src = e.target.result; // Replace the upload icon with the uploaded image
        };
        reader.readAsDataURL(file);
    }
});