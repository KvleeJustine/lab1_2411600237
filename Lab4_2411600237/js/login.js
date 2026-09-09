// ================================
// LOGIN.JS — KyleFitTrack Lab 4
// ================================

// If already logged in, skip login page
if (localStorage.getItem("loggedIn") === "true") {
    window.location.href = "dashboard.html";
}

// Login button event listener
document.getElementById("loginBtn").addEventListener("click", function () {
    const username  = document.getElementById("username").value.trim();
    const password  = document.getElementById("password").value.trim();
    const errorBox  = document.getElementById("loginError");

    // Hide previous error
    errorBox.classList.add("d-none");

    // Basic validation
    if (username === "" || password === "") {
        errorBox.textContent = "Please enter both username and password.";
        errorBox.classList.remove("d-none");
        return;
    }

    // Check credentials
    if (username === "admin" && password === "password123") {
        localStorage.setItem("loggedIn", "true");
        localStorage.setItem("username", username);
        window.location.href = "dashboard.html";
    } else {
        errorBox.textContent = "Invalid username or password. Please try again.";
        errorBox.classList.remove("d-none");
    }
});

// Allow pressing Enter to submit
document.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        document.getElementById("loginBtn").click();
    }
});
