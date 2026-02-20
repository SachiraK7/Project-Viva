document.getElementById("updateForm").addEventListener("submit", function(e) {

    let newPassword = document.getElementById("new_password").value;
    let confirmPassword = document.getElementById("confirm_password").value;

    // Remove previous error
    let oldError = document.querySelector(".js-error");
    if (oldError) oldError.remove();

    if (newPassword === "" || confirmPassword === "") {
        showError("Both fields are required!");
        e.preventDefault();
        return;
    }

    if (newPassword !== confirmPassword) {
        showError("Passwords do not match!");
        e.preventDefault();
        return;
    }
    
    if (newPassword.length < 6) {
        showError("Password must be at least 6 characters long.");
        e.preventDefault();
        return;
    }
});

function showError(message) {
    let div = document.createElement("div");
    div.className = "error-text js-error";
    div.innerText = message;

    let form = document.getElementById("updateForm");
    form.prepend(div);
}