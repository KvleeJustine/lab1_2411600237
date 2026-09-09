<?php
// ================================
// API/WORKOUTS.PHP — KyleFitTrack Lab 4
// Simple PHP REST API for workouts
// ================================

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---- Data file path ----
$dataFile = __DIR__ . '/data/workouts.json';

// ---- Ensure data directory exists ----
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// ---- Load data ----
function loadWorkouts($file) {
    if (!file_exists($file)) {
        // Seed with sample data on first run
        $sample = [
            ["id" => 1, "date" => "2026-09-01", "name" => "Push Ups",      "duration" => 20, "calories" => 120],
            ["id" => 2, "date" => "2026-09-02", "name" => "Squats",        "duration" => 30, "calories" => 200],
            ["id" => 3, "date" => "2026-09-03", "name" => "High Knees",    "duration" => 25, "calories" => 310],
            ["id" => 4, "date" => "2026-09-04", "name" => "Plank",         "duration" => 15, "calories" => 80 ],
            ["id" => 5, "date" => "2026-09-05", "name" => "Jumping Jacks", "duration" => 20, "calories" => 160],
            ["id" => 6, "date" => "2026-09-06", "name" => "Push Ups",      "duration" => 30, "calories" => 180],
            ["id" => 7, "date" => "2026-09-07", "name" => "Squats",        "duration" => 40, "calories" => 260],
            ["id" => 8, "date" => "2026-09-08", "name" => "High Knees",    "duration" => 30, "calories" => 370]
        ];
        file_put_contents($file, json_encode($sample, JSON_PRETTY_PRINT));
        return $sample;
    }
    return json_decode(file_get_contents($file), true) ?? [];
}

// ---- Save data ----
function saveWorkouts($file, $data) {
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT));
}

// ================================
// ROUTE HANDLING
// ================================
$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {

    // ---- GET: return all workouts ----
    case 'GET':
        $workouts = loadWorkouts($dataFile);

        // Optional: filter by name
        if (!empty($_GET['name'])) {
            $name     = strtolower($_GET['name']);
            $workouts = array_filter($workouts, fn($w) => strtolower($w['name']) === $name);
        }

        echo json_encode(["success" => true, "data" => array_values($workouts)]);
        break;

    // ---- POST: add a new workout ----
    case 'POST':
        $workouts = loadWorkouts($dataFile);

        // Validate
        if (empty($input['name']) || empty($input['duration']) || empty($input['calories'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Missing required fields: name, duration, calories"]);
            exit();
        }

        // Generate new ID
        $maxId = count($workouts) > 0 ? max(array_column($workouts, 'id')) : 0;

        $newWorkout = [
            "id"       => $maxId + 1,
            "date"     => date("Y-m-d"),
            "name"     => htmlspecialchars(trim($input['name'])),
            "duration" => (int) $input['duration'],
            "calories" => (int) $input['calories']
        ];

        $workouts[] = $newWorkout;
        saveWorkouts($dataFile, $workouts);

        http_response_code(201);
        echo json_encode(["success" => true, "data" => $newWorkout]);
        break;

    // ---- DELETE: remove a workout by ID ----
    case 'DELETE':
        $workouts = loadWorkouts($dataFile);
        $id       = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid ID"]);
            exit();
        }

        $before   = count($workouts);
        $workouts = array_filter($workouts, fn($w) => $w['id'] !== $id);

        if (count($workouts) === $before) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Workout not found"]);
            exit();
        }

        saveWorkouts($dataFile, $workouts);
        echo json_encode(["success" => true, "message" => "Workout deleted"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed"]);
        break;
}
?>
