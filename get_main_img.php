<?php
require "db_connection.php";

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob es sich um einen GET-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Überprüfen, ob die house_id übergeben wurde
    if (isset($_GET['house_id'])) {
        $house_id = $conn->real_escape_string($_GET['house_id']);

        // SQL-Abfrage vorbereiten, um das Bild mit der angegebenen house_id und 'main' als image_type zu finden
        $query = "SELECT * FROM house_images WHERE house_id = '$house_id' AND image_type = 'main' LIMIT 1";
        $result = $conn->query($query);

        // Überprüfen, ob ein Ergebnis vorliegt
        if ($result->num_rows > 0) {
            // Das Bild als Array aus der Abfrage holen
            $image = $result->fetch_assoc();

            // JSON-Antwort mit dem Bild zurückgeben
            echo json_encode(['success' => true, 'image' => $image]);
        } else {
            // Falls kein Bild gefunden wurde, eine entsprechende Nachricht zurückgeben
            echo json_encode(['success' => false, 'message' => 'Kein Hauptbild für dieses Haus gefunden.']);
        }
    } else {
        // Fehlermeldung, falls die house_id fehlt
        echo json_encode(['success' => false, 'message' => 'House ID fehlt.']);
    }

} else {
    // Wenn es sich nicht um einen GET-Request handelt
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfragemethode.']);
}

$conn->close();
