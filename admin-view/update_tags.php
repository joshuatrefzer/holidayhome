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
    if (isset($data['house_id'], $data['tag_id']) && is_array($data['tag_id'])) {

        // Daten sicher in Variablen speichern und SQL-Injection vermeiden
        $house_id = $conn->real_escape_string($data['house_id']);
        $tag_ids = $data['tag_id']; // Dies ist ein Array von Tag-IDs

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
            // 1. Existierende Tags für das Haus löschen
            $deleteQuery = "DELETE FROM house_tags WHERE house_id = '$house_id'";
            if (!$conn->query($deleteQuery)) {
                throw new Exception('Error deleting existing tags: ' . $conn->error);
            }

            // 2. Neue Tags einfügen
            foreach ($tag_ids as $tag_id) {
                $tag_id = $conn->real_escape_string($tag_id);

                // Überprüfen, ob der Tag existiert
                $tagCheckQuery = "SELECT id FROM tags WHERE id = '$tag_id'";
                $tagCheckResult = $conn->query($tagCheckQuery);
                if ($tagCheckResult->num_rows == 0) {
                    throw new Exception('Tag not found: ' . $tag_id);
                }

                // Einfügen des neuen Tags für das Haus
                $insertQuery = "INSERT INTO house_tags (house_id, tag_id) 
                                VALUES ('$house_id', '$tag_id')";
                if (!$conn->query($insertQuery)) {
                    throw new Exception('Error inserting tag: ' . $conn->error);
                }
            }

            // Wenn alles gut geht, committen wir die Transaktion
            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Tags successfully updated'
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
        // Fehlermeldung, falls nicht alle Felder vorhanden sind oder "tag_id" kein Array ist
        echo json_encode(['success' => false, 'message' => 'Missing required fields or invalid data format']);
    }

} else {
    // Wenn es sich nicht um einen POST-Request handelt
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();

