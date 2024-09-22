
<?php
require "../db_connection.php";

// Fehleranzeige aktivieren (für Entwicklungszwecke)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob es sich um einen DELETE-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Die empfangenen Daten auslesen
    $data = json_decode(file_get_contents('php://input'), true);

    // Überprüfen, ob die ID vorhanden ist
    if (isset($data['id'])) {

        // Die empfangene ID sicher speichern und SQL-Injection vermeiden
        $id = $conn->real_escape_string($data['id']);

        // SQL-Befehl zum Löschen des Hauses basierend auf der ID
        $query = "DELETE FROM houses WHERE id = '$id'";

        // Ausführen der SQL-Abfrage
        if ($conn->query($query) === TRUE) {
            if ($conn->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'House successfully deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No house found with the given ID']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }

    } else {
        // Fehlermeldung, falls keine ID vorhanden ist
        echo json_encode(['success' => false, 'message' => 'Missing house ID']);
    }

} else {
    // Wenn es sich nicht um einen DELETE-Request handelt
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
