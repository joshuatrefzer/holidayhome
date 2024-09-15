<?php
require "../../db_connection.php";

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob es sich um einen GET-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Überprüfen, ob die landlord ID übergeben wurde
    if (isset($_GET['landlord'])) {
        $landlord = $conn->real_escape_string($_GET['landlord']);

        // SQL-Abfrage vorbereiten, um alle Häuser mit der angegebenen landlord ID zu finden
        $query = "SELECT * FROM houses WHERE landlord = '$landlord'";
        $result = $conn->query($query);

        // Überprüfen, ob Ergebnisse vorliegen
        if ($result->num_rows > 0) {
            $houses = [];
            while($row = $result->fetch_assoc()) {
                $houses[] = $row;
            }

            // JSON-Antwort zurückgeben mit den gefundenen Häusern
            echo json_encode(['success' => true, 'houses' => $houses]);
        } else {
            // Falls keine Häuser gefunden wurden, eine spezifische Nachricht zurückgeben
            echo json_encode(['success' => false, 'message' => 'Es gibt noch kein Haus für diesen Vermieter.']);
        }
    } else {
        // Fehlermeldung, falls die landlord ID fehlt
        echo json_encode(['success' => false, 'message' => 'Landlord ID fehlt.']);
    }

} else {
    // Wenn es sich nicht um einen GET-Request handelt
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfragemethode.']);
}

$conn->close();
