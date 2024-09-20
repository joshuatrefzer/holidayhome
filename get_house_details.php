<?php

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Content-Type für JSON-Antworten setzen
header('Content-Type: application/json');

// Datenbankverbindung importieren (MySQLi statt PDO)
require "db_connection.php";

// Haus-ID aus der Anfrage holen
$house_id = isset($_GET['house_id']) ? intval($_GET['house_id']) : 0;

if ($house_id === 0) {
    echo json_encode(['error' => 'Ungültige Haus-ID']);
    exit();
}

try {
    // 1. Alle Bilder des Hauses
    $stmt = $conn->prepare("
        SELECT image_url, image_type, is_main_image
        FROM house_images
        WHERE house_id = ?
    ");
    $stmt->bind_param("i", $house_id);
    $stmt->execute();
    $images_result = $stmt->get_result();
    $images = $images_result->fetch_all(MYSQLI_ASSOC);

    // 2. Alle Facilities des Hauses
    $stmt = $conn->prepare("
        SELECT f.facility_name
        FROM house_facilities hf
        JOIN facilities f ON hf.facility_id = f.id
        WHERE hf.house_id = ?
    ");
    $stmt->bind_param("i", $house_id);
    $stmt->execute();
    $facilities_result = $stmt->get_result();
    $facilities = [];
    while ($row = $facilities_result->fetch_assoc()) {
        $facilities[] = $row['facility_name'];
    }

    // 3. Alle Tags des Hauses
    $stmt = $conn->prepare("
        SELECT t.tag_name
        FROM house_tags ht
        JOIN tags t ON ht.tag_id = t.id
        WHERE ht.house_id = ?
    ");
    $stmt->bind_param("i", $house_id);
    $stmt->execute();
    $tags_result = $stmt->get_result();
    $tags = [];
    while ($row = $tags_result->fetch_assoc()) {
        $tags[] = $row['tag_name'];
    }

    // 4. Alle Activities des Hauses
    $stmt = $conn->prepare("
        SELECT a.activity_name
        FROM house_activities ha
        JOIN activities a ON ha.activity_id = a.id
        WHERE ha.house_id = ?
    ");
    $stmt->bind_param("i", $house_id);
    $stmt->execute();
    $activities_result = $stmt->get_result();
    $activities = [];
    while ($row = $activities_result->fetch_assoc()) {
        $activities[] = $row['activity_name'];
    }

    // 5. Haus-Details aus der Tabelle 'houses' abrufen
    $stmt = $conn->prepare("
        SELECT id, price_per_day, name, country, street, house_number, postal_code, landlord
        FROM houses
        WHERE id = ?
    ");
    $stmt->bind_param("i", $house_id);
    $stmt->execute();
    $house_result = $stmt->get_result();
    $house = $house_result->fetch_assoc();  // Nur eine Zeile wird erwartet

    // Zusammenfügen der Ergebnisse
    $response = [
        'house' => $house,           // Hinzufügen der Hausdaten
        'images' => $images,
        'facilities' => $facilities,
        'tags' => $tags,
        'activities' => $activities
    ];

    // Rückgabe als JSON
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => 'Abfrage fehlgeschlagen: ' . $e->getMessage()]);
    exit();
}
