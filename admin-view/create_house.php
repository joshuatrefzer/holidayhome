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
    if (isset($data['name'], $data['street'], $data['house_number'], $data['postal_code'], $data['country'], $data['price_per_day'], $data['landlord'])) {

        // Daten sicher in Variablen speichern und SQL-Injection vermeiden
        $name = $conn->real_escape_string($data['name']);
        $street = $conn->real_escape_string($data['street']);
        $house_number = $conn->real_escape_string($data['house_number']);
        $postal_code = $conn->real_escape_string($data['postal_code']);
        $country = $conn->real_escape_string($data['country']);
        $price_per_day = $conn->real_escape_string($data['price_per_day']);
        $landlord = $conn->real_escape_string($data['landlord']);

        // SQL-Befehl zum Einfügen der Daten (name wurde wieder aufgenommen)
        $query = "INSERT INTO houses (name, street, house_number, postal_code, country, price_per_day, landlord) 
                  VALUES ('$name', '$street', '$house_number', '$postal_code', '$country', '$price_per_day', '$landlord')";

        // Ausführen der SQL-Abfrage
        if ($conn->query($query) === TRUE) {
            // ID des neu eingefügten Datensatzes abrufen
            $last_id = $conn->insert_id;
            echo json_encode([
                'success' => true, 
                'message' => 'House data successfully inserted',
                'id' => $last_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }

    } else {
        // Fehlermeldung, falls nicht alle Felder vorhanden sind
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }

} else {
    // Wenn es sich nicht um einen POST-Request handelt
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();

