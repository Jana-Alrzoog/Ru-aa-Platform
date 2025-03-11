document.addEventListener("DOMContentLoaded", function () {
  
    document.getElementById("eventImage").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("uploadIcon").src = e.target.result; 
            };
            reader.readAsDataURL(file);
        }
    });

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
            !registrationDeadline || !eventDescription || !eventType || eventImageInput.files.length === 0) {
            alert("⚠️ Please fill out all required fields before submitting.");
            return; 
        }

      
        window.location.href = "organizer_page.html";
    });
});
