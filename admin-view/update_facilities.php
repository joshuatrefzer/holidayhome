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
    if (isset($data['house_id'], $data['facilities']) && is_array($data['facilities'])) {

        // Daten sicher in Variablen speichern und SQL-Injection vermeiden
        $house_id = $conn->real_escape_string($data['house_id']);
        $facilities = $data['facilities']; // Dies ist ein Array von Facilities (z.B. facility_id)

        // Überprüfen, ob das Haus existiert
        $houseCheckQuery = "SELECT id FROM houses WHERE id = '$house_id'";
        $houseCheckResult = $conn->query($houseCheckQuery);

        if ($houseCheckResult->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'House not found']);
            exit;
        }

        // Beginne eine Transaktion, um sicherzustellen, dass entweder alle Änderungen durchgeführt werden oder keine
        $conn->begin_transaction();

        try {
            // 1. Existierende Facilities für das Haus löschen
            $deleteQuery = "DELETE FROM house_facilities WHERE house_id = '$house_id'";
            if (!$conn->query($deleteQuery)) {
                throw new Exception('Error deleting existing facilities: ' . $conn->error);
            }

            // 2. Neue Facilities einfügen
            foreach ($facilities as $facility_id) {
                $facility_id = $conn->real_escape_string($facility_id);
                
                // Überprüfen, ob die Facility existiert
                $facilityCheckQuery = "SELECT id FROM facilities WHERE id = '$facility_id'";
                $facilityCheckResult = $conn->query($facilityCheckQuery);
                if ($facilityCheckResult->num_rows == 0) {
                    throw new Exception('Facility not found: ' . $facility_id);
                }

                // Einfügen der neuen Facility für das Haus
                $insertQuery = "INSERT INTO house_facilities (house_id, facility_id) 
                                VALUES ('$house_id', '$facility_id')";
                if (!$conn->query($insertQuery)) {
                    throw new Exception('Error inserting facility: ' . $conn->error);
                }
            }

            // Wenn alles gut geht, committen wir die Transaktion
            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Facilities successfully updated'
            ]);

        } catch (Exception $e) {
            // Bei einem Fehler rollen wir die Transaktion zurück
            $conn->rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    } else {
        // Fehlermeldung, falls nicht alle Felder vorhanden sind oder "facilities" kein Array ist
        echo json_encode(['success' => false, 'message' => 'Missing required fields or invalid data format']);
    }

} else {
    // Wenn es sich nicht um einen POST-Request handelt
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
