function validateForm() {

    let username = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    if (username === "" || email === "" || password === "" || confirmPassword === "") {
        alert("All fields are required.");
        return false;
    }

    if (!email.match(emailPattern)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters long.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
}
