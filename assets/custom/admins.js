
function DisplayAdmins(){
    
    $.ajax({
        url: "settings/fetchUsers",
        type: "GET",
        
        success: function(response) {
            $("#fetchUsersTable").html(response);
        },
    });
}

DisplayAdmins()
$(document).on("click", ".edit-btn", function(e){
    e.preventDefault();
    var user_id = $(this).data("id");
    $.ajax({
        url: "settings/fetchSelectedUser",
        method: "post",
        data: { user_id: user_id },
        dataType: 'json',
        success: function(response) {
            $("#adminBtn").click();  // Assuming this triggers the modal
            $("#admin_id").val(response.id);
            $("#email").val(response.email);
            $("#user_role").val(response.userRole);
            $("#submit-btn").html("Submit Edited Data");
            // Hide the password div
            $("#password-div").hide();
            // Make the user role div col-md-6
            $("#user_role-div").removeClass("col-md-12").addClass("col-md-6");
        }
    });
});


document.getElementById('generatePassword').addEventListener('click', function() {
    var passwordField = document.getElementById('password');
    var password = generateRandomPassword();
    passwordField.value = password;
    passwordField.type = 'text'; // Show password temporarily
    setTimeout(function() {
        passwordField.type = 'password'; // Hide password after 1 second
    }, 1000);
});

function generateRandomPassword() {
    var length = 10;
    var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var password = "";
    for (var i = 0; i < length; i++) {
        var charIndex = Math.floor(Math.random() * charset.length);
        password += charset.charAt(charIndex);
    }
    return password;
}

$("#adminBtn").click(function(){
    $("#addUserForm")[0].reset(); 
    $("#password-div").show();
    $("#user_role-div").removeClass("col-md-6").addClass("col-md-12");
    $("#submit-btn").html("Submit Data");
})

$(document).ready(function() {
    $("#addUserForm").on("submit", function(e) {
        e.preventDefault();
        var formData = $("#addUserForm").serialize();
      
        $.ajax({
            url: "settings/createUser",
            type: "POST",
            data: formData,
            beforeSend:function(){
                $("#submit-btn").html("Processing ...");
            },
            success: function(response) {
                // Handle the response if needed
                alert(response);
                DisplayAdmins();
                $("#submit-btn").html("Submit Admin");
                $("#addUserForm")[0].reset();
            },
            error: function(xhr) {
                // Handle the error if needed
                console.log(xhr.responseText);
            }
        });
    });
});


$(document).on('click', '.access-control', function() {
    var userId = $(this).data('id');
    var action = $(this).data('action');
    var confirmMessage = action == 'deny' ? "You wish to deny access to the system" : "You wish to grant access to the system";

    $('#confirmMessage').text(confirmMessage);
    $('#confirmActionBtn').data('userid', userId).data('action', action);

    $('#confirmModal').modal('show');
});

$('#confirmActionBtn').on('click', function() {
    var userId = $(this).data('userid');
    var action = $(this).data('action');
    
    $('#confirmModal').modal('hide');

    $.ajax({
        type: 'POST',
        url: 'settings/controlAccess',
        data: { 'userid': userId, 'action': action },
        beforeSend: function() {
            $('#loadingIndicator').show(); // Show a loading indicator if you have one
        },
        success: function(response) {
            if (response.includes("Success")) {
                alert("Operation Successful!");
                DisplayAdmins();
            } else {
                alert("Error: " + response);
            }
        },
        error: function() {
            alert("An error occurred. Please try again.");
        },
        complete: function() {
            $('#loadingIndicator').hide(); // Hide the loading indicator
        }
    });
});


$(document).on("click", ".delete-admin", function(e){
    e.preventDefault();
    var userId = $(this).data("id");
    if(!confirm("You wish to remove this personnel from system admins")){
        return false;
    }
    $.ajax({
        url: "settings/deleteAdminUser",
        method:"post",
        data:{userId:userId},
        
        success:function(response){
            alert(response);
            DisplayAdmins();
        }
    })
})