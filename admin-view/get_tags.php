<?php
require "../db_connection.php";

header('Content-Type: application/json');

$query = "SELECT id, tag_name FROM tags";
$result = $conn->query($query);

$tags = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tags[] = $row;
    }
}

echo json_encode($tags);

