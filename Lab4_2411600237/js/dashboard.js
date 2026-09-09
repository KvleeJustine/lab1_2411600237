// ================================
// DASHBOARD.JS — KyleFitTrack Lab 4
// Main controller: DOM updates,
// filters, search, alerts, simulation
// ================================

// ================================
// 1. AUTH GUARD
// ================================
if (localStorage.getItem("loggedIn") !== "true") {
    window.location.href = "index.html";
}

// ================================
// 2. APP STATE
// ================================
const AppState = {
    searchQuery:    "",
    categoryFilter: "all",
    statusFilter:   "all",
    simInterval:    null,
    deleteTargetId: null
};

// ================================
// 3. GREETING
// ================================
function setGreeting() {
    const username = localStorage.getItem("username") || "User";
    const hour     = new Date().getHours();
    let   timeMsg  = "Good morning";
    if (hour >= 12 && hour < 17) timeMsg = "Good afternoon";
    if (hour >= 17)              timeMsg = "Good evening";

    const heading  = document.getElementById("welcomeHeading");
    const navGreet = document.getElementById("navGreeting");
    if (heading)  heading.textContent  = `${timeMsg}, ${username}! 👋`;
    if (navGreet) navGreet.textContent = `Hi, ${username}`;
}

// ================================
// 4. UPDATE STAT CARDS
// ================================
function updateStatCards() {
    const stats = getStockStatistics();

    // Steps
    setText("statSteps",    stats.todaySteps.toLocaleString());
    setText("stepsCount",   stats.todaySteps.toLocaleString());

    // Calories
    setText("statCalories", stats.totalCalories.toLocaleString());
    setText("calCount",     stats.totalCalories.toLocaleString());

    // Workouts
    setText("statWorkouts", stats.totalWorkouts);
    setText("workoutCount", stats.totalWorkouts);

    // Goal %
    setText("statGoal",     stats.goalPct + "%");

    // Steps progress bar
    const stepsPct = Math.min(Math.round((stats.todaySteps / 10000) * 100), 100);
    setProgress("stepsProgress", stepsPct);

    // Goals section progress bars
    const calPct  = Math.min(Math.round((stats.totalCalories / 2000)   * 100), 100);
    const wrkPct  = Math.min(Math.round((stats.totalWorkouts / 4)      * 100), 100);
    setProgress("calorieGoalBar",  calPct,  calPct  + "%");
    setProgress("workoutGoalBar",  wrkPct,  wrkPct  + "%");
    setProgress("stepsGoalBar",    stepsPct, stepsPct + "%");
}

// ================================
// 5. RENDER WORKOUT TABLE
// ================================
function renderWorkoutTable() {
    const filters = {
        category: AppState.categoryFilter,
        status:   AppState.statusFilter,
        search:   AppState.searchQuery
    };

    const filtered = applyFilters(filters);
    const tbody    = document.getElementById("workoutTableBody");
    const empty    = document.getElementById("emptyState");
    const fCount   = document.getElementById("filteredCount");

    if (!tbody) return;

    if (fCount) fCount.textContent = filtered.length;

    if (filtered.length === 0) {
        tbody.innerHTML = "";
        if (empty) empty.classList.remove("d-none");
        return;
    }

    if (empty) empty.classList.add("d-none");

    tbody.innerHTML = filtered.map(w => {
        const intensity   = getIntensity(w.calories);
        const isLowCal    = Number(w.calories) < 100;
        const rowClass    = isLowCal ? "low-activity-row" : "";
        const nameDisplay = highlightMatch(w.name, AppState.searchQuery);

        return `
        <tr class="${rowClass}" data-id="${w.id}">
            <td>${formatDate(w.date)}</td>
            <td>${nameDisplay}${isLowCal ? ' <span title="Low activity">⚠️</span>' : ""}</td>
            <td>${w.duration} min</td>
            <td>${w.calories} kcal</td>
            <td><span class="${intensity.badge}">${intensity.label}</span></td>
            <td>
                <button class="btn btn-outline-danger btn-sm py-0 px-2"
                    onclick="openDeleteModal(${w.id})" title="Delete workout">
                    🗑
                </button>
            </td>
        </tr>`;
    }).join("");
}

// ================================
// 6. LOW ACTIVITY ALERTS
// ================================
function checkLowActivityAlert() {
    const lowItems = getLowStockProducts();
    const alertBox = document.getElementById("lowActivityAlert");
    const alertMsg = document.getElementById("lowActivityMessage");

    if (!alertBox) return;

    if (lowItems.length > 0) {
        const names = lowItems.map(w => w.name).join(", ");
        alertMsg.textContent = ` ${lowItems.length} workout(s) with very low calories burned: ${names}. Consider increasing intensity!`;
        alertBox.classList.remove("d-none");
    } else {
        alertBox.classList.add("d-none");
    }
}

// ================================
// 7. ADD WORKOUT
// ================================
document.getElementById("saveWorkoutBtn").addEventListener("click", function () {
    const name     = document.getElementById("workoutName").value;
    const duration = document.getElementById("workoutDuration").value;
    const calories = document.getElementById("workoutCalories").value;
    const errorDiv = document.getElementById("modalError");

    errorDiv.classList.add("d-none");

    // Validation
    if (!name || name === "") {
        showModalError("Please select a workout name.");
        return;
    }
    if (!duration || Number(duration) < 1) {
        showModalError("Please enter a valid duration (min 1).");
        return;
    }
    if (!calories || Number(calories) < 1) {
        showModalError("Please enter valid calories burned.");
        return;
    }

    // Save via dataManager
    addWorkout(name, duration, calories);

    // Reset form
    document.getElementById("addWorkoutForm").reset();
    errorDiv.classList.add("d-none");

    // Close modal
    bootstrap.Modal.getInstance(document.getElementById("addWorkoutModal")).hide();

    // Refresh UI
    refreshDashboard();
    showToast("✅ Workout added successfully!");
});

// ================================
// 8. DELETE WORKOUT
// ================================
function openDeleteModal(id) {
    AppState.deleteTargetId = id;
    const modal = new bootstrap.Modal(document.getElementById("deleteModal"));
    modal.show();
}

document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
    if (AppState.deleteTargetId !== null) {
        deleteWorkout(AppState.deleteTargetId);
        AppState.deleteTargetId = null;
        bootstrap.Modal.getInstance(document.getElementById("deleteModal")).hide();
        refreshDashboard();
        showToast("🗑️ Workout deleted.");
    }
});

// ================================
// 9. FILTERS & SEARCH
// ================================

// Search input (live)
document.getElementById("workoutSearch").addEventListener("input", function () {
    AppState.searchQuery = this.value;
    renderWorkoutTable();
});

// Category filter
document.getElementById("categoryFilter").addEventListener("change", function () {
    AppState.categoryFilter = this.value;
    renderWorkoutTable();
});

// Status filter
document.getElementById("statusFilter").addEventListener("change", function () {
    AppState.statusFilter = this.value;
    renderWorkoutTable();
});

// Reset filters
document.getElementById("resetFiltersBtn").addEventListener("click", function () {
    AppState.searchQuery    = "";
    AppState.categoryFilter = "all";
    AppState.statusFilter   = "all";

    document.getElementById("workoutSearch").value  = "";
    document.getElementById("categoryFilter").value = "all";
    document.getElementById("statusFilter").value   = "all";

    renderWorkoutTable();
    showToast("🔄 Filters reset.");
});

// ================================
// 10. CSV EXPORT
// ================================
document.getElementById("exportCsvBtn").addEventListener("click", function () {
    const filters = {
        category: AppState.categoryFilter,
        status:   AppState.statusFilter,
        search:   AppState.searchQuery
    };
    const data    = applyFilters(filters);
    const csv     = exportToCSV(data);
    const now     = new Date().toISOString().split("T")[0];
    downloadCSV(csv, `kylefittrack_workouts_${now}.csv`);
    showToast("📥 CSV exported successfully!");
});

// ================================
// 11. LOGOUT
// ================================
document.getElementById("logoutBtn").addEventListener("click", function () {
    localStorage.removeItem("loggedIn");
    localStorage.removeItem("username");
    window.location.href = "index.html";
});

// ================================
// 12. REAL-TIME SIMULATION
// ================================
const WORKOUT_NAMES = ["Push Ups", "Plank", "High Knees", "Jumping Jacks", "Squats"];

document.getElementById("startSimBtn").addEventListener("click", function () {
    if (AppState.simInterval) return;

    document.getElementById("simStatus").textContent    = "Running";
    document.getElementById("simStatus").className      = "badge bg-success align-self-center";
    document.getElementById("startSimBtn").disabled     = true;
    document.getElementById("stopSimBtn").disabled      = false;

    AppState.simInterval = setInterval(() => {
        // Generate a random workout entry
        const name     = WORKOUT_NAMES[Math.floor(Math.random() * WORKOUT_NAMES.length)];
        const duration = Math.floor(Math.random() * 30) + 10;  // 10–40 min
        const calories = Math.floor(Math.random() * 350) + 50; // 50–400 kcal

        addWorkout(name, duration, calories);
        refreshDashboard();
        showToast(`⚡ Simulated: ${name} — ${calories} kcal`);
    }, 5000); // every 5 seconds
});

document.getElementById("stopSimBtn").addEventListener("click", function () {
    clearInterval(AppState.simInterval);
    AppState.simInterval = null;

    document.getElementById("simStatus").textContent = "Stopped";
    document.getElementById("simStatus").className   = "badge bg-secondary align-self-center";
    document.getElementById("startSimBtn").disabled  = false;
    document.getElementById("stopSimBtn").disabled   = true;

    showToast("⏹ Simulation stopped.");
});

// ================================
// 13. TOAST NOTIFICATION
// ================================
function showToast(message) {
    const toastEl = document.getElementById("liveToast");
    const msgEl   = document.getElementById("toastMessage");
    if (!toastEl || !msgEl) return;

    msgEl.textContent = message;
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}

// ================================
// 14. HELPERS
// ================================

/** Set text content safely */
function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

/** Set progress bar width and label */
function setProgress(id, pct, label) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.width       = pct + "%";
    el.setAttribute("aria-valuenow", pct);
    if (label) el.textContent = label;
}

/** Format ISO date string to locale string */
function formatDate(dateStr) {
    if (!dateStr) return "—";
    const d = new Date(dateStr);
    return isNaN(d) ? dateStr : d.toLocaleDateString("en-US", {
        month: "short", day: "numeric", year: "numeric"
    });
}

/** Wrap matching substring in a highlight span */
function highlightMatch(text, query) {
    if (!query || query.trim() === "") return text;
    const regex = new RegExp(`(${escapeRegex(query.trim())})`, "gi");
    return text.replace(regex, '<span class="search-highlight">$1</span>');
}

/** Escape special regex characters in a user-supplied string */
function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

/** Show error inside the Add Workout modal */
function showModalError(msg) {
    const err = document.getElementById("modalError");
    if (!err) return;
    err.textContent = msg;
    err.classList.remove("d-none");
}

// ================================
// 15. REFRESH ALL
// ================================
function refreshDashboard() {
    updateStatCards();
    renderWorkoutTable();
    updateAllCharts();
    checkLowActivityAlert();
}

// ================================
// 16. INIT ON PAGE LOAD
// ================================
document.addEventListener("DOMContentLoaded", function () {
    setGreeting();
    refreshDashboard();
    renderAllCharts();
});
