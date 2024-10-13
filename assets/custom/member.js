$(document).ready(function() {
    // Add Personnel Accompanying button click event
    $('#addPersonnelBtn').click(function() {
        var newPersonnelField = `
            <div class="input-group mb-3">
                <input type="text" name="family_name[]" class="form-control" placeholder="Family Member Name">
                <input type="text" name="family_relation[]" class="form-control" placeholder="Relation">
                <input type="text" name="family_phone[]" class="form-control" value="260" placeholder="Phone">
                <button type="button" class="btn btn-danger removePersonnelBtn"><i class="bi bi-person-x"></i></button>
            </div>
        `;

        $('#familyFieldsContainer').append(newPersonnelField);
    });

    // Remove Personnel Accompanying button click event
    $(document).on('click', '.removePersonnelBtn', function() {
        $(this).closest('.input-group').remove();
    });
});


function callAddedMembers() {
    var eventData = "getMembers";
    $.ajax({
        url: 'members/fetchMembersDetails', // Your PHP script to viewDetails process the event
        type: 'POST',
        data: {eventData: eventData},
        success: function(response) {
            $("#memberTable").html(response);
        }
    });
}


$(function() {
    $("#memberForm").submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        var memberForm = $(this).serialize();
        var submitBtn = $("#submitBtn");
        
        if (navigator.onLine) {
            $.ajax({
                url: 'members/createMember',
                method: 'post',
                data: memberForm,
                beforeSend: function() {
                    submitBtn.prop("disabled", true); // Disable the submit button
                    submitBtn.html("Processing..."); // Change button text to indicate processing
                },
                success: function(response) {
                    alert(response);
                    submitBtn.prop("disabled", false); // Re-enable the submit button
                    submitBtn.html("Save changes"); // Restore original button text
                    $("#memberForm")[0].reset();
                    callAddedMembers();
                },
                error: function(xhr, textStatus, errorThrown) {
                    if (textStatus === 'error' && xhr.status === 0) {
                        // No internet connection
                        alert("There is no internet connection.");
                    } else {
                        // Handle other errors if needed
                    }
                },
                complete: function() {
                    submitBtn.prop("disabled", false); // Re-enable the submit button
                    submitBtn.html("Save changes"); // Restore original button text
                }
            });
        } else {
            // No internet connection
            alert("There is no internet connection.");
        }
    });
});


function successToast(msg) {
    var result = `
        <div class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${msg} 
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    return result;
}


$(document).ready(function() {
    // Event listener for clicking on "View Attendees" link
    $(document).on('click','.viewMemberDetails', function(e) {
        e.preventDefault(); // Prevent default link behavior

        var memberId = $(this).data('id'); // Get the event ID from the data attribute

        // Fetch event attendees using AJAX
        $.ajax({
            url: 'members/fetchFamilyMembers', // Update with the correct URL
            type: 'POST',
            data: { memberId: memberId },
            success: function(response) {
                $('#familyDiv').html(response);
                $('#familyModal').modal('show'); // Show the modal
            },
            error: function(xhr, status, error) {
                console.error('Error fetching event attendees:', error);
            }
        });
    });
});


$(document).ready(function() {
    // Show the message form when at least one checkbox is checked
    $(document).on('change', '.attendeeCheckbox', function() {
        if ($(this).prop('checked')) {
            if ($('.attendeeCheckbox:checked').length === $('.attendeeCheckbox').length) {
                $('#selectAll').prop('checked', true);
            }
        } else {
            $('#selectAll').prop('checked', false);
        }

        if ($('.attendeeCheckbox:checked').length > 0) {
            $('#sendMessageForm').show();
        } else {
            $('#sendMessageForm').hide();
        }
    });

    // Handle the "Check All" checkbox separately
    $(document).on('change', '#selectAll', function() {
        $('.attendeeCheckbox').prop('checked', $(this).prop('checked'));
        if ($(this).prop('checked')) {
            $('#sendMessageForm').show();
        } else {
            $('#sendMessageForm').hide();
        }
    });

    // Submit message form via AJAX
    $('#messageForm').submit(function(e) {
        e.preventDefault();
        var message = $('#message').val();
        var selectedAttendees = [];

        $('.attendeeCheckbox:checked').each(function() {
            selectedAttendees.push($(this).val());
        });

        $.ajax({
            url: 'members/send_message', // Update with your PHP file handling the message sending
            type: 'POST',
            data: { message: message, attendees: selectedAttendees },
            success: function(response) {
                // Handle success response
                alert('Message sent successfully');
                // Optionally, clear the form or display a success message
                $('#messageForm')[0].reset();
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error('Error sending message:', error);
                // Optionally, display an error message to the user
            }
        });
    });
});

$(document).on("click", ".editMember-btn", function(e) {
    e.preventDefault();
    var memberId = $(this).data('id');
    $.ajax({
        url: 'members/fetchSelectedMember',
        method: 'POST',
        data: { member_id: memberId },
        dataType: 'json',
        success: function(response) {
            // Prefill form fields with member data
            $('#title').val(response.member.title);
            $('#firstname').val(response.member.firstname);
            $('#lastname').val(response.member.lastname);
            $('#email').val(response.member.email);
            $('#gender').val(response.member.gender);
            $('#birthdate').val(response.member.birthdate);
            $('#occupation').val(response.member.occupation);
            $('#phone').val(response.member.phone);
            $('#address').val(response.member.address);
            $('#member_id').val(memberId);
            $('#memberModal').modal('show');

            // Create and prefill family member input fields
            $('#familyFieldsContainer').empty();
            if (Array.isArray(response.family) && response.family.length > 0) {
                response.family.forEach(function(familyMember) {
                    var familyField = `
                        <div class="input-group mb-3">
                            <input type="text" name="family_name[]" class="form-control" value="${familyMember.name}" placeholder="Enter Family Member Name">
                            <input type="text" name="family_relation[]" class="form-control" value="${familyMember.relation}" placeholder="Enter Relation">
                            <input type="text" name="family_phone[]" class="form-control" value="${familyMember.phone}" placeholder="Enter Phone Number">
                            <button type="button" class="btn btn-danger removePersonnelBtn"><i class="bi bi-person-x"></i></button>
                        </div>`;
                    $('#familyFieldsContainer').append(familyField);
                });
            } else {
                // No family members found, show a message or handle accordingly
                $('#familyFieldsContainer').append('<p>No family members found</p>');
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log('Error:', errorThrown);
        }
    });
})


$(document).on("click", ".deleteMember-btn", function (e) {
    e.preventDefault();
    var memberId = $(this).data("id");
    if (confirm("Are you sure you want to delete this member?")) {
        $.ajax({
            url: 'members/deleteMember',
            method: 'POST',
            data: { member_id: memberId },
            success: function(response) {
                alert(response);
                callAddedMembers();
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(xhr.responseText); // Log error message to console
                alert("Error deleting member. Please try again."); // Display error message to user
            }
        });
    }
})


$(document).on('change', '.memberStatusCheckbox', function() {
    var eventId = $(this).data('eventid');
    var isChecked = $(this).prop('checked');
    var table = $(this).data('table');
    if (!confirm('Are you sure you want to change the status?')) {
        $(this).prop('checked', false);
        return;
    }

    $.ajax({
        url: 'base/updateStatus',
        method: 'POST',
        data: { eventId: eventId, isChecked: 1, table: table },
        success: function(response) {
            // Handle the response if needed
            alert(response);
            callAddedMembers();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});


$('#uploadExcelForm').on('submit', function(event) {
    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'members/uploadExcelMembers',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function() {
            // Optional: Show a loading spinner or disable the submit button
            $("#submitExcel").prop("disabled", true).html("Processing....");
        },
        success: function(response) {
            alert(response);
            $('#uploadExcelModal').modal('hide');
            // Optional: Refresh the table to show the new data
             $("#submitExcel").prop("disabled", false).html("Upload");
            callAddedMembers();
        },
        error: function(xhr, status, error) {
            alert('An error occurred while uploading the file.');
        }
    });
});


 