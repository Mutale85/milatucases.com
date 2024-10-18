document.getElementById('seePasswordBtn').addEventListener('click', function() {
    let passwordField = document.getElementById('memberPassword');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        this.textContent = 'Hide';
    } else {
        passwordField.type = 'password';
        this.textContent = 'See';
    }
});

document.getElementById('generatePasswordBtn').addEventListener('click', function() {
    let passwordField = document.getElementById('memberPassword');
    let generatedPassword = Math.random().toString(36).slice(-8);
    passwordField.value = generatedPassword;
});

function fetchTeamMembers() {
    $.ajax({
        url: 'settings/getTeamMembers',
        type: 'GET',
        success: function(response) {
            $("#teamMembersContainer").html(response);
        }
    })
}
fetchTeamMembers();

$(document).ready(function() {
    $('#addMemberForm').on('submit', function(event) {
        event.preventDefault();
        
        // Check if at least one permission is selected
        var permissionSelected = false;
        $('.permission-checkbox').each(function() {
            if ($(this).is(':checked')) {
                permissionSelected = true;
                return false; // Break the loop
            }
        });

        if (!permissionSelected) {
            $('.invalid-feedback').show();
            return; // Stop form submission
        } else {
            $('.invalid-feedback').hide();
        }

        $.ajax({
            url: 'settings/createUser', 
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $("#submitMember").prop("disabled", true).html("Processing...");
            },
            success: function(response) {
                sweetSuccess(response);
                $("#submitMember").prop("disabled", false).html("Add Member");
                $("#answerDiv").html(response);
                setTimeout(function(){
                     $('#addMemberModal').modal('hide');
                }, 1500);
                fetchTeamMembers();
                
            },
            error: function(xhr, status, error) {
                sweetError('Error: ' + error);
                $("#submitMember").prop("disabled", false).html("Add Member");
            }
        });
    });

    // Ensure only one permission checkbox can be checked at a time
    $('.permission-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('.permission-checkbox').not(this).prop('checked', false);
        }
    });
   
    $(document).on("click", ".editMemberData", function(e) {
        e.preventDefault();
        var lawFirmUserId = $(this).data('id');
        $.ajax({
            url: 'settings/fetchSelectedMember',
            type: 'POST',
            data: { id: lawFirmUserId },
            dataType: 'json',
            success: function(response) {
                if (response.member) {
                    var data = response.member;
                    // Prefill the form with the fetched data
                    $('#addMemberModalLabel').text('Edit Member');
                    $('#memberName').val(data.names);
                    $('#memberEmail').val(data.email);
                    $('#memberPhone').val(data.phonenumber);
                    $('#desgination').val(data.job);
                    $('#title').val(data.title);
                    $('#memberPassword').val('').removeAttr('required');
                    
                    // Update the permissions radio buttons
                    $('.permission-checkbox').prop('checked', false);
                    if (data.userRole) {
                        $(`input[name="permissions"][value="${data.userRole}"]`).prop('checked', true);
                    }
                    
                    // Add a hidden input for the user ID
                    if ($('#lawFirmUserId').length === 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'lawFirmUserId',
                            name: 'lawFirmUserId',
                            value: lawFirmUserId
                        }).appendTo('#addMemberForm');
                    } else {
                        $('#lawFirmUserId').val(lawFirmUserId);
                    }
                    
                    // Change the submit button text
                    $('#submitMember').text('Update Member');
                    
                    // Show the modal
                    $('#addMemberModal').modal('show');
                } else {
                    sweetError('Error: Invalid response from server');
                }
            },
            error: function() {
                sweetError('Error fetching member data');
            }
        });
    });

    
    $(document).on("click", ".deleteMember", function(e){
    	e.preventDefault();
    	var lawFirmUserId = $(this).data('id');
    	var phonenumber = $(this).data('phone');
    	var email = $(this).data('email');
    	var names = $(this).data('names');
    	if(confirm("You are about to remove " + names + " from the system, you can add them later if you wish so")){
	    	$.ajax({
	            url: 'base/deleteLawFirmUser', 
	            type: 'POST',
	            data: {lawFirmUserId:lawFirmUserId, phonenumber:phonenumber, email:email, names:names},
	            beforeSend:function() {
	            	$(this).prop("disabled", true).html("Processing...");
	            },
	            success: function(response) {
	                sweetSuccess(response);
	                // location.reload();
                    fetchTeamMembers();
	            },
	            error: function() {
	                sweetError('Error deleting member');
	                // location.reload();
                    fetchTeamMembers();
	            }
	        });
	    }else{
	    	return false;
	    }
    }) 
});


$(document).ready(function() {
    $(document).on('click', '.toggleAccess', function() {
        var memberId = $(this).data('id');
        var memberName = $(this).data('name');
        var currentStatus = $(this).data('status');
        var action = currentStatus == 1 ? 'suspend' : 'grant access to';
        
        Swal.fire({
            title: `Do you want to ${action} ${memberName}?`,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Yes, ${action}`,
            denyButtonText: `No, cancel`
        }).then((result) => {
            if (result.isConfirmed) {
                if (navigator.onLine) {
                    $.ajax({
                        url: 'settings/toggleMemberAccess',
                        type: 'POST',
                        data: { 
                            memberId: memberId,
                            action: currentStatus == 1 ? 'suspend' : 'grant'
                        },
                        beforeSend: function() {
                            $("#staticBackdrop").modal("show");
                        },
                        success: function(response) {
                            if (response.success) {
                                sweetSuccess(response.message);
                                
                            } else {
                                sweetError(response.message);
                            }
                            $("#staticBackdrop").modal("hide");
                            fetchTeamMembers();
                        },
                        error: function(xhr, status, error) {
                            sweetError('Error updating member access:', error);
                            $("#staticBackdrop").modal("hide");
                            fetchTeamMembers();
                        }
                    });
                } else {
                    Swal.fire("Check your internet connection", "", "error");
                }
            } else if (result.isDenied) {
                Swal.fire("Action cancelled", "", "info");
            }
        });
    });
});