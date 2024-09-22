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

    // Funktion zum Löschen des vorherigen Main-Bildes
    function deletePreviousMainImage($house_id) {
        global $conn, $upload_dir;

        // Finde das vorherige Main-Bild
        $query = "SELECT image_url FROM house_images WHERE house_id = '$house_id' AND is_main_image = 1";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = '../' . $row['image_url'];  // Korrigiere den relativen Pfad

            // Lösche das Bild von der Festplatte
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            // Lösche den Eintrag aus der Datenbank
            $delete_query = "DELETE FROM house_images WHERE house_id = '$house_id' AND is_main_image = 1";
            $conn->query($delete_query);
        }
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

    // Überprüfe, ob ein Main-Bild hochgeladen wurde und lösche das vorherige
    if (isset($_FILES['main_img']) && $_FILES['main_img']['error'] === UPLOAD_ERR_OK) {
        // Lösche das vorhandene Main-Bild
        deletePreviousMainImage($house_id);
        
        // Lade das neue Main-Bild hoch
        uploadImage($_FILES['main_img'], 'main', $house_id, true);
    }

    // Indoor-Bilder hochladen (optional, kein Fehler, wenn keine vorhanden sind)
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

    // Outdoor-Bilder hochladen (optional, kein Fehler, wenn keine vorhanden sind)
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
