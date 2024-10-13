
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0];            
    document.getElementById('travel_date').setAttribute('min', today);
});



$(document).ready(function() {
  // Add Personnel Accompanying button click event
  $('#addPersonnelBtn').click(function() {
    var newPersonnelField = `
        <div class="input-group mb-3">
            <input type="text" name="service_number[]" class="form-control" placeholder="Svc No & Rank">
            <input type="text" name="name[]" class="form-control" placeholder="Enter Names">
            <input type="text" name="phone[]" class="form-control" placeholder="Enter Phone" value="260">
            <button type="button" class="btn btn-danger removePersonnelBtn"><i class="bi bi-person-x"></i></button>
        </div>
    `;

    $('#personnelFieldsContainer').append(newPersonnelField);
  });

  // Remove Personnel Accompanying button click event
  $(document).on('click', '.removePersonnelBtn', function() {
    // Target the closest 'personnel-group' to remove only the input group and its label
    $(this).closest('.personnel-group').remove();
  });
});



function callAddedOutbounds(){
    var eventData = "getOutBounds";
    $.ajax({
        url: 'courtesy/fetchOutBoundsDetails', // Your PHP script to process the event
        type: 'POST',
        data: {eventData: eventData},
        success: function(response) {
            $("#OutBoundTable").html(response);
            
        }
    });
}

callAddedOutbounds();

$(function() {
    $("#outBoundForm").submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        var outBoundForm = $(this).serialize();
        var submitBtn = $("#submitBtn");
        
        
        if (navigator.onLine) {
            $.ajax({
                url: 'courtesy/createOutboundCourtesyCall',
                method: 'post',
                data: outBoundForm,
                beforeSend: function() {
                    submitBtn.prop("disabled", true); // Disable the submit button
                    submitBtn.html("Processing..."); // Change button text to indicate processing
                },
                success: function(response) {
                    alert(response);
                    submitBtn.prop("disabled", false);
                    submitBtn.html("Save changes");
                    $("#outBoundForm")[0].reset();
                    callAddedOutbounds();
                    $("#outbound_id").val("");
                
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
    $(document).on('click','.viewDelegates', function(e) {
        e.preventDefault(); // Prevent default link behavior

        var visitId = $(this).data('id'); // Get the event ID from the data attribute

        // Fetch event attendees using AJAX
        $.ajax({
            url: 'courtesy/fetchOutboundsDelegates', // Update with the correct URL
            type: 'POST',
            data: { visitId: visitId },
            success: function(response) {
                $('#accompanyDiv').html(response);
                $('#accompanyModal').modal('show'); // Show the modal
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
        var selectedAttendeesName = [];

        $('.attendeeCheckbox:checked').each(function() {
            selectedAttendees.push($(this).val());
            selectedAttendeesName.push($(this).attr('id'));
        });

        $.ajax({
            url: 'courtesy/send_message', // Update with your PHP file handling the message sending
            type: 'POST',
            data: { message: message, attendees: selectedAttendees, names:selectedAttendeesName },
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


$(document).ready(function() {
    
    function fetchOutboundData(visitId) {
        $.ajax({
            url: 'courtesy/fetchSelectedVisit2',
            method: 'POST',
            data: { OutBound_id: visitId },
            dataType: 'json',
            success: function(response) {
                // Prefill form fields with OutBound data
                $('#host-designation').val(response.visit.host_designation);
                $('#host-institution').val(response.visit.host_institution);
                $('#host-name').val(response.visit.host_name);
                $('#host-email').val(response.visit.host_email);
                $('#host-phone').val(response.visit.host_phone);
                $('#travel_date').val(response.visit.travel_date);
                $('#travel_time').val(response.visit.travel_time);
                $('#purpose').val(response.visit.purpose);
                $('#reminder').val(response.visit.reminder);
                $('#outbound_id').val(visitId);

                // Create and prefill accompanying personnel input fields
                $('#personnelFieldsContainer').empty();
                if (Array.isArray(response.delegates) && response.delegates.length > 0) {
                    response.delegates.forEach(function(person) {
                        var personnelField = `
                            
                            <div class="input-group mb-3">
                                <input type="text" name="service_number[]" class="form-control" value="${person.service_number}" placeholder="Svc No & Rank">
                                <input type="text" name="name[]" class="form-control" value="${person.name}" placeholder="Enter Name">
                                <input type="text" name="phone[]" class="form-control" value="${person.phone}" placeholder="Enter Phone">
                                <button type="button" class="btn btn-danger removePersonnelBtn"><i class="bi bi-person-x"></i></button>
                            </div>
                            
                                `;
                            $('#personnelFieldsContainer').append(personnelField);

                    });
                } else {
                    // No delegates found, show a message or handle accordingly
                    $('#personnelFieldsContainer').append('<p>No delegates found</p>');
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log('Error:', errorThrown);
            }
        });
    }


   
    function deleteOutBound(visitId) {
        if (confirm("Are you sure you want to delete this Outbound courtesy call?")) {
            $.ajax({
                url: 'courtesy/deleteOutboundVisit',
                method: 'POST',
                data: { visitId: visitId },
                success: function(response) {
                    // Handle success response
                    alert(response); // Display success message or handle as needed
                    callAddedOutbounds();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(xhr.responseText); // Log error message to console
                    alert("Error deleting OutBound. Please try again."); // Display error message to user
                }
            });
        }
    }

    // Handle click on Edit button
    $(document).on('click', '.editOutboundVisit', function() {
        var visitId = $(this).data('id');
        fetchOutboundData(visitId);
        $('#OutBoundModal').modal('show');
    });


    $(document).on('click', '.deleteOutboundVisit', function(e) {
        var visitId = $(this).data('id');
        deleteOutBound(visitId);
    });

    // Handle click on Remove Personnel button
    $('#personnelFieldsContainer').on('click', '.removePersonnelBtn', function() {
        $(this).parent().remove();
    });

    // Handle form submission
    $('#outBoundForm').submit(function(event) {
        event.preventDefault();
        // Implement form submission logic here
    });
});


$(document).on('change', '.statusCheckbox', function() {
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
        data: { eventId: eventId, isChecked: 1, table:table },
        success: function(response) {
            // Handle the response if needed
            alert(response);
            callAddedOutbounds();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});
