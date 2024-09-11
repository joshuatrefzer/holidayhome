<?php
require "../db_connection.php";

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Überprüfen, ob es sich um einen POST-Request handelt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Die house_id wird aus den POST-Daten entnommen
    if (isset($_POST['house_id'])) {
        $house_id = $_POST['house_id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'house_id missing']);
        exit;
    }

    // Verzeichnis, in dem die Bilder gespeichert werden
    $upload_dir = '../uploads/houses/';
    
    // Stelle sicher, dass das Verzeichnis existiert
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create directory']);
            exit;
        }
    }

    // Funktion zum Erzeugen eines eindeutigen Dateinamens mit Hash
    function generateUniqueFilename($filename) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $hash = substr(md5(uniqid(rand(), true)), 0, 4); // 4-stelliger Hash
        return $basename . '_' . $hash . '.' . $extension;
    }

    // Funktion zum Hochladen und Speichern eines Bildes
    function uploadImage($file, $image_type, $house_id, $is_main = false) {
        global $conn, $upload_dir;

        $filename = generateUniqueFilename($file['name']);
        $target_file = $upload_dir . $filename;
        $image_url = '/uploads/houses/' . $filename;

        // Versuche das Bild hochzuladen
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Wenn es hochgeladen wurde, speichere die Bilddaten in der Datenbank
            $is_main_image = $is_main ? 1 : 0;
            $query = "INSERT INTO house_images (house_id, image_url, image_type, is_main_image) 
                      VALUES ('$house_id', '$image_url', '$image_type', '$is_main_image')";
            $conn->query($query);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload ' . $filename]);
        }
    }

    // Hauptbild hochladen
    if (isset($_FILES['main_img']) && $_FILES['main_img']['error'] === UPLOAD_ERR_OK) {
        uploadImage($_FILES['main_img'], 'main', $house_id, true);
    }

    // Indoor-Bilder hochladen
    if (isset($_FILES['indoor_img'])) {
        foreach ($_FILES['indoor_img']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['indoor_img']['error'][$index] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['indoor_img']['name'][$index],
                    'tmp_name' => $tmp_name
                ];
                uploadImage($file, 'indoor', $house_id);
            }
        }
    }

    // Outdoor-Bilder hochladen
    if (isset($_FILES['outdoor_img'])) {
        foreach ($_FILES['outdoor_img']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['outdoor_img']['error'][$index] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['outdoor_img']['name'][$index],
                    'tmp_name' => $tmp_name
                ];
                uploadImage($file, 'outdoor', $house_id);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Images successfully uploaded']);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
