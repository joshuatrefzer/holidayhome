<?php
require 'db_connection.php';

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Debugging: Daten prüfen
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }
    
    if (isset($data['username']) && !empty($data['username'])) {
        $username = $conn->real_escape_string($data['username']);
        $password = $conn->real_escape_string($data['password']);
        $role = $data['role'] ?? 'user';

        // Passwort setzen, wenn es nicht leer ist
        if (!empty($password)) {
            // Hier könntest du eine Hash-Funktion verwenden
        }

        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            // User ID abrufen
            $userId = $conn->insert_id;

            // User-Objekt erstellen
            $user = [
                'id' => $userId,
                'username' => $username,
                'role' => $role
            ];

            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Username is required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
