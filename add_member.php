<?php
include 'db.php';

$parentId = isset($_POST['parentId']) ? $_POST['parentId'] : NULL;
$memberName = isset($_POST['memberName']) ? trim($_POST['memberName']) : "";

if ($memberName === "") {
    echo json_encode(["status" => "error", "message" => "Member name cannot be empty!"]);
    exit;
}

// SQL Injection Prevention
$stmt = $conn->prepare("INSERT INTO members (Name, ParentId, CreatedDate) VALUES (?, ?, NOW())");
$stmt->bind_param("si", $memberName, $parentId);
$stmt->execute();
$stmt->close();

echo json_encode(["status" => "success"]);
?>
