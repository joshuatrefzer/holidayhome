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
    if (isset($data['house_id'], $data['activities']) && is_array($data['activities'])) {

        // Daten sicher in Variablen speichern und SQL-Injection vermeiden
        $house_id = $conn->real_escape_string($data['house_id']);
        $activities = $data['activities']; // Dies ist ein Array von Aktivitäten (z.B. activity_id)

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
            // 1. Existierende Aktivitäten für das Haus löschen
            $deleteQuery = "DELETE FROM house_activities WHERE house_id = '$house_id'";
            if (!$conn->query($deleteQuery)) {
                throw new Exception('Error deleting existing activities: ' . $conn->error);
            }

            // 2. Neue Aktivitäten einfügen
            foreach ($activities as $activity_id) {
                $activity_id = $conn->real_escape_string($activity_id);
                
                // Überprüfen, ob die Aktivität existiert
                $activityCheckQuery = "SELECT id FROM activities WHERE id = '$activity_id'";
                $activityCheckResult = $conn->query($activityCheckQuery);
                if ($activityCheckResult->num_rows == 0) {
                    throw new Exception('Activity not found: ' . $activity_id);
                }

                // Einfügen der neuen Aktivität für das Haus
                $insertQuery = "INSERT INTO house_activities (house_id, activity_id) 
                                VALUES ('$house_id', '$activity_id')";
                if (!$conn->query($insertQuery)) {
                    throw new Exception('Error inserting activity: ' . $conn->error);
                }
            }

            // Wenn alles gut geht, committen wir die Transaktion
            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Activities successfully updated'
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
        // Fehlermeldung, falls nicht alle Felder vorhanden sind oder "activities" kein Array ist
        echo json_encode(['success' => false, 'message' => 'Missing required fields or invalid data format']);
    }

} else {
    // Wenn es sich nicht um einen POST-Request handelt
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
