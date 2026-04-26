<?php
/*
    File: get_trip.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: JSON API endpoint used by admin.php (CHANGE #14).
                 When the admin types a trip ID into the update form,
                 server.js fires a fetch() request here.
                 Returns that trip's data as JSON so JS can auto-fill
                 all the update form fields without a page reload.
                 Protected by session auth — only logged-in admins can call it.
*/

session_start();

// Only admins can access this endpoint
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'db.php';

// Read and validate the id from the query string
$raw_id = $_GET['id'] ?? '';

if (!ctype_digit($raw_id) || (int)$raw_id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$stmt = $pdo->prepare("SELECT trip_id, trip_name, trip_type, description, price_per_person, max_travelers FROM trips WHERE trip_id = ?");
$stmt->execute([(int)$raw_id]);
$trip = $stmt->fetch();

if (!$trip) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

// Return the trip data as JSON
header('Content-Type: application/json');
echo json_encode($trip);
