<?php
require "../db_connection.php";

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob es sich um einen POST-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Die empfangenen Daten auslesen
    $data = json_decode(file_get_contents('php://input'), true);

    // Überprüfen, ob alle erforderlichen Felder vorhanden sind
    if (isset($data['house_id'], $data['activity_id'])) {

        // Daten sicher in Variablen speichern und SQL-Injection vermeiden
        $house_id = $conn->real_escape_string($data['house_id']);
        $activity_id = $conn->real_escape_string($data['activity_id']);

        // SQL-Befehl zum Einfügen der Daten
        $query = "INSERT INTO house_activities (house_id, activity_id) 
                  VALUES ('$house_id', '$activity_id')";

        // Ausführen der SQL-Abfrage
        if ($conn->query($query) === TRUE) {
            echo json_encode([
                'success' => true, 
                'message' => 'Activity successfully added to house'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }

    } else {
        // Fehlermeldung, falls nicht alle Felder vorhanden sind
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }

} else {
    // Wenn es sich nicht um einen POST-Request handelt
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();

