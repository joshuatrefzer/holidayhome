<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['house_id'], $data['activity_id'])) {

        $house_id = $conn->real_escape_string($data['house_id']);
        $activity_id = $conn->real_escape_string($data['activity_id']);

        $query = "INSERT INTO house_activities (house_id, activity_id) 
                  VALUES ('$house_id', '$activity_id')";

        if ($conn->query($query) === TRUE) {
            echo json_encode([
                'success' => true, 
                'message' => 'Activity successfully added to house'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();

