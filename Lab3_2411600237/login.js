document.getElementById("loginButton").addEventListener("click", function () {

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;
    let loginAlert = document.getElementById("loginAlert");

    if (username === "admin" && password === "password123") {

        localStorage.setItem("username", username);
        localStorage.setItem("loggedIn", "true");

        window.location.href = "dashboard.html";

    } else {

        loginAlert.classList.remove("d-none");

    }

});