// ================================
// DATAMANAGER.JS — KyleFitTrack Lab 4
// Handles all data operations:
// fetch, filter, search, export, stats
// ================================

// ---- Workout intensity thresholds (calories) ----
const INTENSITY = {
    high:   { min: 300, label: "High",   badge: "badge-high"   },
    medium: { min: 150, label: "Medium", badge: "badge-medium" },
    low:    { min: 0,   label: "Low",    badge: "badge-low"    }
};

// ---- Default sample data (used on first load) ----
const DEFAULT_WORKOUTS = [
    { id: 1, date: "2026-09-01", name: "Push Ups",      duration: 20, calories: 120 },
    { id: 2, date: "2026-09-02", name: "Squats",        duration: 30, calories: 200 },
    { id: 3, date: "2026-09-03", name: "High Knees",    duration: 25, calories: 310 },
    { id: 4, date: "2026-09-04", name: "Plank",         duration: 15, calories: 80  },
    { id: 5, date: "2026-09-05", name: "Jumping Jacks", duration: 20, calories: 160 },
    { id: 6, date: "2026-09-06", name: "Push Ups",      duration: 30, calories: 180 },
    { id: 7, date: "2026-09-07", name: "Squats",        duration: 40, calories: 260 },
    { id: 8, date: "2026-09-08", name: "High Knees",    duration: 30, calories: 370 }
];

// ---- Steps sample data (last 7 days) ----
const STEPS_DATA = {
    labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    values: [6200, 8400, 7100, 9500, 5800, 11200, 7600]
};

// ================================
// DATA INITIALIZATION
// ================================

/**
 * Initialize data: load from localStorage or use defaults.
 * Returns the workouts array.
 */
function initializeData() {
    const stored = localStorage.getItem("workouts_v4");
    if (stored) {
        return JSON.parse(stored);
    }
    // First time: seed with sample data
    localStorage.setItem("workouts_v4", JSON.stringify(DEFAULT_WORKOUTS));
    return DEFAULT_WORKOUTS;
}

/**
 * Save workouts array to localStorage.
 */
function saveData(workouts) {
    localStorage.setItem("workouts_v4", JSON.stringify(workouts));
}

// ================================
// GETTERS
// ================================

/**
 * Returns all workouts.
 */
function getProducts() {
    return initializeData();
}

/**
 * Returns a single workout by ID.
 */
function getProductById(id) {
    return getProducts().find(w => w.id === id) || null;
}

/**
 * Returns workouts filtered by name/category.
 */
function getProductsByCategory(category) {
    if (!category || category === "all") return getProducts();
    return getProducts().filter(w => w.name === category);
}

/**
 * Determine intensity level for a workout based on calories.
 */
function getIntensity(calories) {
    const cal = Number(calories);
    if (cal >= INTENSITY.high.min)   return INTENSITY.high;
    if (cal >= INTENSITY.medium.min) return INTENSITY.medium;
    return INTENSITY.low;
}

/**
 * Returns workouts that qualify as "low activity" (calories < 100).
 */
function getLowStockProducts() {
    return getProducts().filter(w => Number(w.calories) < 100);
}

// ================================
// STATISTICS
// ================================

/**
 * Returns key statistics object.
 */
function getStockStatistics() {
    const workouts = getProducts();
    const totalCalories  = workouts.reduce((sum, w) => sum + Number(w.calories), 0);
    const totalDuration  = workouts.reduce((sum, w) => sum + Number(w.duration), 0);
    const totalSteps     = STEPS_DATA.values.reduce((a, b) => a + b, 0);
    const todaySteps     = STEPS_DATA.values[STEPS_DATA.values.length - 1];
    const lowActivity    = getLowStockProducts().length;
    const goalPct        = Math.min(Math.round((workouts.length / 4) * 100), 100);

    return {
        totalWorkouts: workouts.length,
        totalCalories,
        totalDuration,
        totalSteps,
        todaySteps,
        lowActivity,
        goalPct
    };
}

/**
 * Returns grouped data: workout counts by name (for doughnut chart).
 */
function getCategorySummary() {
    const workouts = getProducts();
    const summary  = {};
    workouts.forEach(w => {
        summary[w.name] = (summary[w.name] || 0) + 1;
    });
    return summary;
}

/**
 * Returns calories grouped by workout name (for bar chart).
 */
function getCaloriesByCategory() {
    const workouts = getProducts();
    const summary  = {};
    workouts.forEach(w => {
        summary[w.name] = (summary[w.name] || 0) + Number(w.calories);
    });
    return summary;
}

/**
 * Returns top N workouts sorted by calories.
 */
function getTopWorkouts(n = 5) {
    return [...getProducts()]
        .sort((a, b) => Number(b.calories) - Number(a.calories))
        .slice(0, n);
}

/**
 * Returns intensity distribution counts: {high, medium, low}
 */
function getIntensityDistribution() {
    const workouts = getProducts();
    const dist = { High: 0, Medium: 0, Low: 0 };
    workouts.forEach(w => {
        const level = getIntensity(w.calories).label;
        dist[level]++;
    });
    return dist;
}

/**
 * Returns steps data object (labels + values).
 */
function getStepsData() {
    return STEPS_DATA;
}

// ================================
// FILTERING
// ================================

/**
 * Filter workouts by category.
 */
function filterByCategory(workouts, category) {
    if (!category || category === "all") return workouts;
    return workouts.filter(w => w.name === category);
}

/**
 * Filter workouts by intensity status.
 */
function filterByStockStatus(workouts, status) {
    if (!status || status === "all") return workouts;
    return workouts.filter(w => getIntensity(w.calories).label.toLowerCase() === status);
}

/**
 * Filter workouts by calorie range (min / max).
 */
function filterByPriceRange(workouts, min, max) {
    return workouts.filter(w => {
        const cal = Number(w.calories);
        return cal >= Number(min) && cal <= Number(max);
    });
}

/**
 * Apply all active filters at once.
 * @param {Object} filters — { category, status, search }
 */
function applyFilters(filters = {}) {
    let result = getProducts();
    if (filters.category) result = filterByCategory(result, filters.category);
    if (filters.status)   result = filterByStockStatus(result, filters.status);
    if (filters.search)   result = searchProducts(result, filters.search);
    return result;
}

// ================================
// SEARCH
// ================================

/**
 * Search workouts by name (case-insensitive).
 * @param {Array}  workouts — array to search within
 * @param {string} query    — search term
 */
function searchProducts(workouts, query) {
    if (!query || query.trim() === "") return workouts;
    const q = query.trim().toLowerCase();
    return workouts.filter(w => w.name.toLowerCase().includes(q));
}

// ================================
// ADD / DELETE
// ================================

/**
 * Add a new workout and persist it.
 * Returns the updated workouts array.
 */
function addWorkout(name, duration, calories) {
    const workouts = getProducts();
    const newId    = workouts.length > 0
        ? Math.max(...workouts.map(w => w.id)) + 1
        : 1;
    const newWorkout = {
        id:       newId,
        date:     new Date().toISOString().split("T")[0],
        name:     name,
        duration: Number(duration),
        calories: Number(calories)
    };
    workouts.push(newWorkout);
    saveData(workouts);
    return workouts;
}

/**
 * Delete a workout by ID and persist.
 * Returns the updated workouts array.
 */
function deleteWorkout(id) {
    const updated = getProducts().filter(w => w.id !== id);
    saveData(updated);
    return updated;
}

// ================================
// CSV EXPORT
// ================================

/**
 * Convert an array of workouts to CSV string.
 */
function exportToCSV(data) {
    const headers = ["ID", "Date", "Workout", "Duration (min)", "Calories", "Intensity"];
    const rows = data.map(w => {
        const intensity = getIntensity(w.calories).label;
        return [w.id, w.date, w.name, w.duration, w.calories, intensity].join(",");
    });
    return [headers.join(","), ...rows].join("\n");
}

/**
 * Trigger a CSV file download in the browser.
 */
function downloadCSV(csvContent, filename = "workouts_export.csv") {
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url  = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href     = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}
