// ================================
// CHARTS.JS — KyleFitTrack Lab 4
// Chart.js configurations and renderers
// ================================

// ---- Shared color palette — exact Lab 3 green monochromatic scheme ----
const CHART_COLORS = {
    // Shades built from Lab 3 hex values: #14532D, #1a936f, #198754, #d4ece4, #f3a712
    green:  ["#14532D", "#1a936f", "#198754", "#2db37a", "#6dcca4", "#d4ece4"],
    status: {
        High:   "#dc3545",   // danger red
        Medium: "#f3a712",   // Lab 3 accent amber (EXACT)
        Low:    "#1a936f"    // Lab 3 secondary green (EXACT)
    },
    accent:  "#f3a712",      // Lab 3 accent (EXACT)
    border:  "rgba(255,255,255,0.8)"
};

// Keep chart instances so we can update/destroy them
const chartInstances = {};

// ---- Default Chart.js global options ----
Chart.defaults.font.family = "'Segoe UI', Arial, sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = "#4B5563";

// ================================
// 1. WORKOUT DISTRIBUTION (Doughnut)
// ================================
function renderWorkoutDistributionChart() {
    const summary = getCategorySummary();
    const labels  = Object.keys(summary);
    const values  = Object.values(summary);

    const ctx = document.getElementById("workoutDistributionChart");
    if (!ctx) return;

    if (chartInstances.doughnut) chartInstances.doughnut.destroy();

    chartInstances.doughnut = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels,
            datasets: [{
                data:            values,
                backgroundColor: CHART_COLORS.green,
                borderColor:     CHART_COLORS.border,
                borderWidth:     2
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels:   { boxWidth: 12, padding: 10, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.raw} workout(s)`
                    }
                }
            }
        }
    });
}

// ================================
// 2. WEEKLY CALORIES BURNED (Bar)
// ================================
function renderWeeklyCaloriesChart() {
    const calByCategory = getCaloriesByCategory();
    const labels = Object.keys(calByCategory);
    const values = Object.values(calByCategory);

    const ctx = document.getElementById("weeklyCaloriesChart");
    if (!ctx) return;

    if (chartInstances.bar) chartInstances.bar.destroy();

    chartInstances.bar = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [{
                label:           "Total Calories",
                data:            values,
                backgroundColor: CHART_COLORS.green,
                borderColor:     CHART_COLORS.green,
                borderWidth:     1,
                borderRadius:    6
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.raw} kcal`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "rgba(0,0,0,0.05)" },
                    ticks: { font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

// ================================
// 3. STEPS TREND (Line)
// ================================
function renderStepsTrendChart() {
    const stepsData = getStepsData();

    const ctx = document.getElementById("stepsTrendChart");
    if (!ctx) return;

    if (chartInstances.line) chartInstances.line.destroy();

    chartInstances.line = new Chart(ctx, {
        type: "line",
        data: {
            labels: stepsData.labels,
            datasets: [{
                label:           "Steps",
                data:            stepsData.values,
                borderColor:     "#16A34A",
                backgroundColor: "rgba(22, 163, 74, 0.12)",
                borderWidth:     2.5,
                pointBackgroundColor: "#14532D",
                pointRadius:     4,
                pointHoverRadius: 6,
                tension:         0.4,
                fill:            true
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.raw.toLocaleString()} steps`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: { color: "rgba(0,0,0,0.05)" },
                    ticks: {
                        font: { size: 11 },
                        callback: v => v.toLocaleString()
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

// ================================
// 4. TOP WORKOUTS BY CALORIES (Horizontal Bar)
// ================================
function renderTopWorkoutsChart() {
    const top    = getTopWorkouts(5);
    const labels = top.map(w => `${w.name} (${w.date})`);
    const values = top.map(w => w.calories);
    const colors = top.map(w => getIntensity(w.calories).label === "High"
        ? "#EF4444"
        : getIntensity(w.calories).label === "Medium"
            ? "#F59E0B"
            : "#22C55E"
    );

    const ctx = document.getElementById("topWorkoutsChart");
    if (!ctx) return;

    if (chartInstances.hbar) chartInstances.hbar.destroy();

    chartInstances.hbar = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [{
                label:           "Calories",
                data:            values,
                backgroundColor: colors,
                borderRadius:    6
            }]
        },
        options: {
            indexAxis:           "y",
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.raw} kcal`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: "rgba(0,0,0,0.05)" },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
}

// ================================
// 5. ACTIVITY STATUS DISTRIBUTION (Pie)
// ================================
function renderActivityStatusChart() {
    const dist   = getIntensityDistribution();
    const labels = Object.keys(dist);
    const values = Object.values(dist);
    const colors = labels.map(l => CHART_COLORS.status[l]);

    const ctx = document.getElementById("activityStatusChart");
    if (!ctx) return;

    if (chartInstances.pie) chartInstances.pie.destroy();

    chartInstances.pie = new Chart(ctx, {
        type: "pie",
        data: {
            labels,
            datasets: [{
                data:            values,
                backgroundColor: colors,
                borderColor:     CHART_COLORS.border,
                borderWidth:     2
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels:   { boxWidth: 12, padding: 10, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.raw} workout(s)`
                    }
                }
            }
        }
    });
}

// ================================
// RENDER ALL CHARTS
// ================================
function renderAllCharts() {
    renderWorkoutDistributionChart();
    renderWeeklyCaloriesChart();
    renderStepsTrendChart();
    renderTopWorkoutsChart();
    renderActivityStatusChart();
}

// ================================
// UPDATE CHARTS WITH FRESH DATA
// ================================
function updateAllCharts() {
    renderAllCharts();
}
