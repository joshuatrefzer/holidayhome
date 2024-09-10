<?php
require "../db_connection.php";

header('Content-Type: application/json');

$query = "SELECT id, facility_name FROM facilities";
$result = $conn->query($query);

$facilities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
}

echo json_encode($facilities);

