
function callAddedVisitors() {
    var eventData = "getVisitors";
    $.ajax({
        url: 'visitors/fetchVisitorsDetails', // Your PHP script to viewDetails process the event
        type: 'POST',
        data: {eventData: eventData},
        success: function(response) {
            $("#visitorTable").html(response);
        }
    });
}

callAddedVisitors();
$(function() {
    $("#visitorForm").submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        var visitorForm = $(this).serialize();
        var submitBtn = $("#submitBtn");
        
        if (navigator.onLine) {
            $.ajax({
                url: 'visitors/createVisitor',
                method: 'post',
                data: visitorForm,
                beforeSend: function() {
                    submitBtn.prop("disabled", true); // Disable the submit button
                    submitBtn.html("Processing..."); // Change button text to indicate processing
                },
                success: function(response) {
                    alert(response);
                    submitBtn.prop("disabled", false); // Re-enable the submit button
                    submitBtn.html("Save changes"); // Restore original button text
                    $("#visitorForm")[0].reset();
                    callAddedVisitors();
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


$(document).on("click", ".editVisitor-btn", function(e) {
    var visitorId = $(this).data('id');
    $.ajax({
        url: 'visitors/fetchSelectedVisitor',
        method: 'POST',
        data: { visitor_id: visitorId },
        dataType: 'json',
        success: function(response) {
            // Prefill form fields with visitor data
            $('#title').val(response.visitor.title);
            $('#firstname').val(response.visitor.firstname);
            $('#lastname').val(response.visitor.lastname);
            $('#gender').val(response.visitor.gender);
            $('#email').val(response.visitor.email);
            $('#phone').val(response.visitor.phone);
            $('#address').val(response.visitor.address);
            $('#visitDate').val(response.visitor.visitDate);
            $('#visitor_id').val(visitorId);
            $('#visitorModal').modal('show');
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log('Error:', errorThrown);
        }
    });
})


$(document).on("click", ".deleteVisitor-btn", function () {
    var visitorId = $(this).data("id");
    if (confirm("Are you sure you want to delete this visitor?")) {
        $.ajax({
            url: 'visitors/deleteVisitor',
            method: 'POST',
            data: { visitor_id: visitorId },
            success: function(response) {
                alert(response);
                callAddedVisitors();
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(xhr.responseText); // Log error message to console
                alert("Error deleting visitor. Please try again."); // Display error message to user
            }
        });
    }
})

