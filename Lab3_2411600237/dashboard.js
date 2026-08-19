// Check if the user is logged in
const loggedIn = localStorage.getItem("loggedIn");

if (loggedIn !== "true") {
    window.location.href = "index.html";
}

// Get the saved username
let username = localStorage.getItem("username");

// Show User in the navigation
document.getElementById("navUsername").textContent = "User";

// Change greeting depending on the time
let hour = new Date().getHours();

let greeting = document.getElementById("greeting");

if (hour < 12) {
    greeting.textContent = "Good morning, User!";
} else if (hour < 18) {
    greeting.textContent = "Good afternoon, User!";
} else {
    greeting.textContent = "Good evening, User!";
}
// Fitness statistics
let steps = 7500;
let calories = 420;
let workouts = 4;
let heartRate = 78;

// Show the statistics on the dashboard
document.getElementById("stat1-value").textContent = steps;
document.getElementById("stat2-value").textContent = calories;
document.getElementById("stat3-value").textContent = workouts;
document.getElementById("stat4-value").textContent = heartRate;

// Daily step goal
let stepGoal = 10000;
let stepPercentage = (steps / stepGoal) * 100;

// Keep the progress from going over 100%
if (stepPercentage > 100) {
    stepPercentage = 100;
}

// Update the progress bar
let stepProgress = document.getElementById("stepProgress");

stepProgress.style.width = stepPercentage + "%";
stepProgress.textContent = Math.round(stepPercentage) + "%";

// Recent fitness activities
let activities = [
    {
        date: "Aug 19",
        activity: "Push Ups",
        duration: "15 min",
        calories: 80
    },
    {
        date: "Aug 18",
        activity: "Plank",
        duration: "10 min",
        calories: 50
    },
    {
        date: "Aug 17",
        activity: "Jumping Jacks",
        duration: "20 min",
        calories: 120
    },
    {
        date: "Aug 16",
        activity: "Squats",
        duration: "15 min",
        calories: 90
    }
];

// Add activities to the table
let activityTable = document.getElementById("activityTable");

activities.forEach(function(activity) {

    let row = document.createElement("tr");

    row.innerHTML = `
        <td>${activity.date}</td>
        <td>${activity.activity}</td>
        <td>${activity.duration}</td>
        <td>${activity.calories} kcal</td>
    `;

    activityTable.appendChild(row);
});

// Logout
document.getElementById("logoutButton").addEventListener("click", function (event) {

    event.preventDefault();

    localStorage.removeItem("username");
    localStorage.removeItem("loggedIn");

    window.location.href = "index.html";
});