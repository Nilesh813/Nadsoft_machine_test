<?php
include 'db.php';

$result = $conn->query("SELECT DISTINCT Id, Name FROM members ORDER BY Name ASC");

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);
?>

