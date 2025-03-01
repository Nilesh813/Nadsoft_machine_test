<?php

include_once 'db.php';

function insertMember($name, $parentId = 0) {
    global $conn; 

    $query = "INSERT INTO members (Name, ParentId) VALUES (?, ?)";

    $stmt = $conn->prepare($query);

    $stmt->bind_param("si", $name, $parentId); 
   
    if ($stmt->execute()) {
        return $conn->insert_id; 
    } else {
        return false;
    }
}

$parentId = insertMember('Parent Member');
if ($parentId) {
    echo "Parent member inserted with ID: " . $parentId . "<br>";
    insertMember('Child Member 1', $parentId);
    insertMember('Child Member 2', $parentId);
} else {
    echo "Error inserting parent member.";
}
?>
