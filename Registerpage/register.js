document.getElementById("signupForm").addEventListener("submit", function(e) {

    let name = document.querySelector("input[name='name']").value.trim();
    let email = document.querySelector("input[name='email']").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm_password").value;

    let oldError = document.querySelector(".js-error");
    if (oldError) oldError.remove();

    if (name === "" || email === "" || password === "" || confirmPassword === "") {
        showError("All fields are required!");
        e.preventDefault();
        return;
    }

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.match(emailPattern)) {
        showError("Enter a valid email address!");
        e.preventDefault();
        return;
    }

    if (password.length < 6) {
        showError("Password must be at least 6 characters!");
        e.preventDefault();
        return;
    }

    if (password !== confirmPassword) {
        showError("Passwords do not match!");
        e.preventDefault();
        return;
    }
});

function showError(message) {
    let div = document.createElement("div");
    div.className = "error-msg js-error";
    div.innerText = message;

    let form = document.getElementById("signupForm");
    form.prepend(div);
}