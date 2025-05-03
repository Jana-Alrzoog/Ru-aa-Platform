const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});
document.addEventListener("DOMContentLoaded", function () {
    const signUpForm = document.querySelector(".sign-up form");

    signUpForm.addEventListener("submit", function (event) {
        event.preventDefault(); 

        const role = document.getElementById("role").value; 
        
        if (role === "organizer") {
            window.location.href = "organizer_page.html"; 
        } else if (role === "participant") {
            window.location.href = "Home page.html"; 
        } else {
            alert("Please select a role before signing up!");
        }
    });
});
