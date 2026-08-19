// ================================
// LOGIN CHECK
// ================================

if (localStorage.getItem("loggedIn") !== "true") {
    window.location.href = "index.html";
}


// ================================
// USER GREETING
// ================================

const greeting = document.getElementById("greeting");

if (greeting) {
    greeting.textContent = "Welcome, User!";
}


// ================================
// WORKOUT DATA
// ================================

let workouts = JSON.parse(localStorage.getItem("workouts")) || [];


// ================================
// DISPLAY WORKOUTS
// ================================

function displayWorkouts() {

    const activityTable = document.getElementById("activityTable");
    const workoutCount = document.getElementById("workoutCount");
    const workoutsValue = document.getElementById("workoutsValue");
    const caloriesValue = document.getElementById("caloriesValue");
    const caloriesSectionValue =
        document.getElementById("caloriesSectionValue");

    if (!activityTable) {
        return;
    }

    activityTable.innerHTML = "";

    // Get selected filter
    const filter =
        document.getElementById("workoutFilter")?.value || "all";

    let filteredWorkouts = workouts;

    if (filter !== "all") {
        filteredWorkouts = workouts.filter(function (workout) {
            return workout.name === filter;
        });
    }

    // Show message if there are no workouts
    if (filteredWorkouts.length === 0) {

        activityTable.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No workouts found.
                </td>
            </tr>
        `;

    } else {

        filteredWorkouts.forEach(function (workout) {

            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${workout.date}</td>
                <td>${workout.name}</td>
                <td>${workout.duration} min</td>
                <td>${workout.calories} kcal</td>
            `;

            activityTable.appendChild(row);
        });
    }

    // Update workout count
    if (workoutCount) {
        workoutCount.textContent = filteredWorkouts.length;
    }

    if (workoutsValue) {
        workoutsValue.textContent = workouts.length;
    }

    // Calculate calories
    let totalCalories = 0;

    workouts.forEach(function (workout) {
        totalCalories += Number(workout.calories);
    });

    if (caloriesValue) {
        caloriesValue.textContent = totalCalories;
    }

    if (caloriesSectionValue) {
        caloriesSectionValue.textContent = totalCalories;
    }
}


// ================================
// ADD WORKOUT
// ================================

const saveWorkoutButton =
    document.getElementById("saveWorkoutButton");

if (saveWorkoutButton) {

    saveWorkoutButton.addEventListener("click", function () {

        const workoutName =
            document.getElementById("workoutName").value;

        const workoutDuration =
            document.getElementById("workoutDuration").value;

        const workoutCalories =
            document.getElementById("workoutCalories").value;


        // Validate form
        if (
            workoutName === "" ||
            workoutDuration === "" ||
            workoutCalories === ""
        ) {

            alert("Please fill in all workout information.");

            return;
        }


        // Create workout object
        const newWorkout = {

            date: new Date().toLocaleDateString(),

            name: workoutName,

            duration: workoutDuration,

            calories: workoutCalories
        };


        // Add workout
        workouts.push(newWorkout);


        // Save workout
        localStorage.setItem(
            "workouts",
            JSON.stringify(workouts)
        );


        // Update dashboard
        displayWorkouts();


        // Clear form
        document.getElementById("workoutName").value = "";

        document.getElementById("workoutDuration").value = "";

        document.getElementById("workoutCalories").value = "";


        // Close Bootstrap modal
        const modalElement =
            document.getElementById("workoutModal");

        if (modalElement) {

            const modal =
                bootstrap.Modal.getInstance(modalElement);

            if (modal) {
                modal.hide();
            }
        }

    });
}


// ================================
// FILTER WORKOUTS
// ================================

const workoutFilter =
    document.getElementById("workoutFilter");

if (workoutFilter) {

    workoutFilter.addEventListener("change", function () {

        displayWorkouts();

    });
}


// ================================
// LOGOUT
// ================================

const logoutButton =
    document.getElementById("logoutButton");

if (logoutButton) {

    logoutButton.addEventListener("click", function (event) {

        event.preventDefault();

        localStorage.removeItem("loggedIn");
        localStorage.removeItem("username");

        window.location.href = "index.html";

    });
}


// ================================
// LOAD SAVED DATA
// ================================

displayWorkouts();