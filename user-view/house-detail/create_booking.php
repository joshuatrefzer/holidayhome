<?php
require "../../db_connection.php";

// Fehleranzeige aktivieren (für Entwicklungszwecke)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob es sich um einen POST-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Debugging: Empfangenes JSON protokollieren
    $input = file_get_contents('php://input');
    error_log("Empfangenes JSON: " . $input);

    // Die empfangenen Daten auslesen
    $data = json_decode($input, true);

    // Debugging: Empfangene Daten protokollieren
    error_log("Decoded Data: " . print_r($data, true));

    // Überprüfen, ob alle erforderlichen Felder vorhanden sind
    if (isset($data['user_id'], $data['house_id'], $data['check_in'], $data['check_out'], $data['total_price'], $data['facilities'])) {
        
        // Daten sicher in Variablen speichern und SQL-Injection vermeiden
        $user_id = $conn->real_escape_string($data['user_id']);
        $house_id = $conn->real_escape_string($data['house_id']);
        $check_in = $conn->real_escape_string($data['check_in']);
        $check_out = $conn->real_escape_string($data['check_out']);
        $total_price = $conn->real_escape_string($data['total_price']);
        $facilities = $data['facilities']; // Angenommen, dies ist ein Array von Facility-Namen

        // SQL-Befehl zum Einfügen der Buchung
        $query = "INSERT INTO bookings (user_id, house_id, check_in, check_out, total_price) 
                  VALUES ('$user_id', '$house_id', '$check_in', '$check_out', '$total_price')";

        // Ausführen der SQL-Abfrage und Debugging
        if ($conn->query($query) === TRUE) {
            $booking_id = $conn->insert_id;  // Die ID des neu erstellten Eintrags

            // Debugging: Buchung erfolgreich
            error_log("Buchung erfolgreich. Buchungs-ID: " . $booking_id);

            // Für jede Facility den Namen in eine ID umwandeln und verknüpfen
            foreach ($facilities as $facility_name) {
                $facility_query = "SELECT id FROM facilities WHERE facility_name = '$facility_name'";
                $result = $conn->query($facility_query);

                if ($result->num_rows > 0) {
                    $facility_row = $result->fetch_assoc();
                    $facility_id = $facility_row['id'];

                    // Verknüpfung von Facility-ID und Booking-ID
                    $link_query = "INSERT INTO booking_facilities (booking_id, facility_id) 
                                   VALUES ('$booking_id', '$facility_id')";

                    if (!$conn->query($link_query)) {
                        error_log("Fehler beim Verknüpfen der Facility: " . $conn->error);
                        echo json_encode(['success' => false, 'message' => 'Fehler beim Verknüpfen der Facilities: ' . $conn->error]);
                        $conn->close();
                        exit;
                    }
                } else {
                    error_log("Facility '$facility_name' nicht gefunden");
                    echo json_encode(['success' => false, 'message' => "Facility '$facility_name' nicht gefunden"]);
                    $conn->close();
                    exit;
                }
            }

            // Jetzt das Buchungsobjekt abrufen, um es als JSON zurückzugeben
            $booking_query = "SELECT * FROM bookings WHERE id = '$booking_id'";
            $booking_result = $conn->query($booking_query);

            if ($booking_result->num_rows > 0) {
                $booking_data = $booking_result->fetch_assoc();

                // Alle verknüpften Facilities für diese Buchung abrufen
                $facilities_query = "SELECT f.facility_name FROM booking_facilities bf
                                     JOIN facilities f ON bf.facility_id = f.id
                                     WHERE bf.booking_id = '$booking_id'";
                $facilities_result = $conn->query($facilities_query);
                $facility_names = [];

                if ($facilities_result->num_rows > 0) {
                    while ($row = $facilities_result->fetch_assoc()) {
                        $facility_names[] = $row['facility_name'];
                    }
                }

                // Erfolgreiche Buchung und Rückgabe des Buchungsobjekts
                echo json_encode([
                    'success' => true,
                    'message' => 'Buchung erfolgreich erstellt.',
                    'booking' => [
                        'booking_id' => $booking_data['id'],
                        'user_id' => $booking_data['user_id'],
                        'house_id' => $booking_data['house_id'],
                        'check_in' => $booking_data['check_in'],
                        'check_out' => $booking_data['check_out'],
                        'total_price' => $booking_data['total_price'],
                        'facilities' => $facility_names
                    ]
                ]);

            } else {
                echo json_encode(['success' => false, 'message' => 'Fehler beim Abrufen der Buchung']);
            }

        } else {
            error_log("Fehler beim Erstellen der Buchung: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Fehler beim Erstellen der Buchung: ' . $conn->error]);
        }

    } else {
        // Fehlermeldung, falls nicht alle Felder vorhanden sind
        error_log("Fehlende erforderliche Felder: " . print_r($data, true));
        echo json_encode(['success' => false, 'message' => 'Fehlende erforderliche Felder']);
    }

} else {
    // Ungültige Anfragemethode
    error_log("Ungültige Anfragemethode: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfragemethode']);
}

$conn->close();
