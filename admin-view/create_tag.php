<?php
require "../db_connection.php";  

header('Content-Type: application/json');  

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['tag_name']) && !empty($data['tag_name'])) {
    $tagName = htmlspecialchars($data['tag_name']);  

    $query = "INSERT INTO tags (tag_name) VALUES (?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $tagName);  

        if ($stmt->execute()) {
            $insertedId = $conn->insert_id;

            $selectQuery = "SELECT id, tag_name FROM tags WHERE id = ?";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param("i", $insertedId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

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

        $stmt->close(); 
    } else {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Vorbereiten der SQL-Anfrage']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tag-Name ist leer oder ungültig']);
}

$conn->close();  
