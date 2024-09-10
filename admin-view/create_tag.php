<?php
require "../db_connection.php";  // Verbindung zur Datenbank

header('Content-Type: application/json');  // Header für JSON-Antwort setzen

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['tag_name']) && !empty($data['tag_name'])) {
    $tagName = htmlspecialchars($data['tag_name']);  // Benutzereingaben sichern

    // SQL-Anfrage zum Einfügen eines neuen Tags
    $query = "INSERT INTO tags (tag_name) VALUES (?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $tagName);  // Den Tag-Namen als Parameter binden

        // Ausführen der Anfrage
        if ($stmt->execute()) {
            // Letzte eingefügte ID abrufen
            $insertedId = $conn->insert_id;

            // Abfrage, um das eingefügte Tag anhand der ID zu holen
            $selectQuery = "SELECT id, tag_name FROM tags WHERE id = ?";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param("i", $insertedId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            // Überprüfen, ob der Datensatz gefunden wurde
            if ($result->num_rows > 0) {
                $createdTag = $result->fetch_assoc();
                echo json_encode(['success' => true, 'message' => 'Tag erfolgreich hinzugefügt', 'tag' => $createdTag]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tag konnte nicht gefunden werden']);
            }

            $selectStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen des Tags']);
        }

        $stmt->close();  // Schließe das Statement
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Vorbereiten der SQL-Anfrage']);
    }
} else {
    // Fehler, falls kein 'tag_name' übergeben wurde
    echo json_encode(['success' => false, 'message' => 'Tag-Name ist leer oder ungültig']);
}

$conn->close();  // Schließe die Datenbankverbindung
