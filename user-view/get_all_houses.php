<?php
require "../db_connection.php";

header('Content-Type: application/json');

$query = "SELECT * FROM houses";
$result = $conn->query($query);

$houses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $houses[] = $row;
    }
}

echo json_encode($houses);

