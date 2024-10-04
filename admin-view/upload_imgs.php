<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['house_id'])) {
        $house_id = $_POST['house_id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'house_id missing']);
        exit;
    }

    $upload_dir = '../uploads/houses/';
    
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create directory']);
            exit;
        }
    }

    function generateUniqueFilename($filename) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $hash = substr(md5(uniqid(rand(), true)), 0, 4); // 4-stelliger Hash
        return $basename . '_' . $hash . '.' . $extension;
    }

    function deletePreviousMainImage($house_id) {
        global $conn, $upload_dir;

        $query = "SELECT image_url FROM house_images WHERE house_id = '$house_id' AND is_main_image = 1";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = '../' . $row['image_url']; 

            if (file_exists($image_path)) {
                unlink($image_path);
            }

            $delete_query = "DELETE FROM house_images WHERE house_id = '$house_id' AND is_main_image = 1";
            $conn->query($delete_query);
        }
    }

    function uploadImage($file, $image_type, $house_id, $is_main = false) {
        global $conn, $upload_dir;

        $filename = generateUniqueFilename($file['name']);
        $target_file = $upload_dir . $filename;
        $image_url = '/uploads/houses/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $is_main_image = $is_main ? 1 : 0;
            $query = "INSERT INTO house_images (house_id, image_url, image_type, is_main_image) 
                      VALUES ('$house_id', '$image_url', '$image_type', '$is_main_image')";
            $conn->query($query);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload ' . $filename]);
        }
    }

    if (isset($_FILES['main_img']) && $_FILES['main_img']['error'] === UPLOAD_ERR_OK) {
        deletePreviousMainImage($house_id);        
        uploadImage($_FILES['main_img'], 'main', $house_id, true);
    }

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
