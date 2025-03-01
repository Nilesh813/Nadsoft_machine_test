$(document).ready(function() {
    function loadMembers() {
        $("#memberList").html(""); 

        $.ajax({
            url: "getMembers.php",
            type: "GET",
            dataType: "json",
            success: function(data) {
                let seenNames = new Set(); 

                data.forEach(member => {
                    if (!seenNames.has(member.Name)) {
                        seenNames.add(member.Name);
                        $("#memberList").append(`<li id="member-${member.Id}">${member.Name}</li>`);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log("Error loading members:", error);
            }
        });
    }

    loadMembers(); 

    $("#saveMember").click(function() {
        let parentId = $("#parentId").val();
        let memberName = $("#memberName").val().trim();

        if (memberName === "") {
            alert("Name cannot be empty!");
            return;
        }

        $.ajax({
            url: "add_member.php",
            type: "POST",
            data: JSON.stringify({ parentId: parentId, memberName: memberName }),
            dataType: "json",
            contentType: "application/json; charset=UTF-8",
            processData: false,
            success: function(response) {
                console.log("Server Response:", response);

                if (response.status === "success") {
                    let newId = response.id;

                    
                    loadMembers(); 

                    $("#addMemberPopup").hide();
                    $("#memberName").val("");
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
                console.log("XHR Response:", xhr.responseText);
            }
        });
    });
});
