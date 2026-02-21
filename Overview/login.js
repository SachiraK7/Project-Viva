document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("loginForm");
    
    form.addEventListener("submit", function(event) {
        const username = form.username.value.trim();
        const password = form.password.value.trim();
        
        if (username === "" || password === "") {
            event.preventDefault(); // Stop submission
            alert("Please fill in both fields!");
        }
    });

    // Optional: Google button click alert
    const googleBtn = document.querySelector(".google-btn");
    googleBtn.addEventListener("click", function() {
        alert("Google Login functionality not connected yet.");
    });
});