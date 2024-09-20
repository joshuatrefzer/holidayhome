<?php
require "../../db_connection.php";

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob die house_id in der GET-Anfrage enthalten ist
if (isset($_GET['house_id'])) {
    $house_id = intval($_GET['house_id']); // sicherstellen, dass house_id ein Integer ist

    // SQL-Abfrage, um Buchungen für das entsprechende Haus abzurufen
    $query = "SELECT check_in, check_out FROM bookings WHERE house_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $house_id); // Parameter binden
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        
        // Alle Buchungen durchlaufen und dem Array hinzufügen
        while ($row = $result->fetch_assoc()) {
            $bookings[] = [
                'check_in' => $row['check_in'],
                'check_out' => $row['check_out']
            ];
        }

        // Antwort senden
        echo json_encode(['success' => true, 'bookings' => $bookings]);
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing SQL statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing house_id parameter']);
}

$conn->close();
