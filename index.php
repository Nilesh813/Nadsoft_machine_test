<?php
include 'db.php';

$result = $conn->query("SELECT * FROM members ORDER BY ParentId ASC, Name ASC");
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

function displayTree($parentId, $members) {
    $hasChildren = false;

    foreach ($members as $member) {
        if ($member['ParentId'] == $parentId) {
            if (!$hasChildren) {
                echo "<ul>";
                $hasChildren = true;
            }

            if (!empty(trim($member['Name']))) { 
                echo "<li>" . htmlspecialchars($member['Name']); 
                displayTree($member['Id'], $members);
                echo "</li>";
            }
        }
    }

    if ($hasChildren) {
        echo "</ul>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Tree</title>

    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; }
        #addMemberModal { display: none; padding: 20px; background: white; width: 300px; border-radius: 10px; }
        #addMemberBtn { 
            padding: 8px 16px;
            background: #007BFF; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 14px;
        }
        button { margin-top: 10px; padding: 8px 12px; }
    </style>
</head>
<body>

    <h2>Member List</h2>
    <?php displayTree(null, $members); ?>

    <button id="addMemberBtn">Add Member</button>

<div id="addMemberModal" style="display: none; background: white; padding: 20px; width: 350px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);">
    <h2 style="font-size: 18px; margin-bottom: 10px;">Add Member</h2>

    <label for="parent" style="display: block; font-weight: bold; margin-top: 10px;">Parent:</label>
    <select id="parent" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">
        <option value="">Select Parent</option>
    </select>

    <label for="memberName" style="display: block; font-weight: bold; margin-top: 10px;">Name:</label>
    <input type="text" id="memberName" placeholder="Enter Name" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">

    <div style="display: flex; justify-content: flex-end; margin-top: 15px;">
        <button id="saveMember" style="padding: 8px 15px; border: none; border-radius: 5px; background: #007BFF; color: white; font-size: 14px; cursor: pointer;">Save changes</button>
        <button id="closeModal" style="padding: 8px 15px; border: none; border-radius: 5px; background: #6c757d; color: white; font-size: 14px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>
</div>

    <script>
        $(document).ready(function () {

            $("#addMemberBtn").click(function () {
                $.fancybox.open({
                    src: '#addMemberModal',
                    type: 'inline'
                });

                $.ajax({
                    url: "get_members.php",
                    method: "GET",
                    success: function (data) {
                        let members = JSON.parse(data);
                        let options = '<option value="">Select Parent</option>';
                        let uniqueNames = new Set();
                        members.forEach(member => {
                            if (!uniqueNames.has(member.Name)) {
                                options += `<option value="${member.Id}">${member.Name}</option>`;
                                uniqueNames.add(member.Name);
                            }
                        });
                        $("#parent").html(options);
                    }
                });
            });

            $("#closeModal").click(function () {
                $.fancybox.close();
            });

            $("#saveMember").click(function () {
                let parentId = $("#parent").val();
                let memberName = $("#memberName").val();

                if (memberName.trim() === "") {
                    alert("Name cannot be empty!");
                    return;
                }

                $.ajax({
                    url: "add_member.php",
                    method: "POST",
                    data: { parentId: parentId, memberName: memberName },
                    success: function (response) {
                        let res = JSON.parse(response);
                        if (res.status === "success") {
                            alert("Member added successfully!");
                            location.reload(); 
                        } else {
                            alert("Error: " + res.message);
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>
