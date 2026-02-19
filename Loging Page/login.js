document.getElementById("loginForm").addEventListener("submit", function(e) {

    let email = document.querySelector("input[name='email']").value.trim();
    let password = document.getElementById("password").value;

    // Remove previous error
    let oldError = document.querySelector(".js-error");
    if (oldError) oldError.remove();

    if (email === "" || password === "") {
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
});

function showError(message) {
    let div = document.createElement("div");
    div.className = "error-msg js-error";
    div.innerText = message;

    let form = document.getElementById("loginForm");
    form.prepend(div);
}
