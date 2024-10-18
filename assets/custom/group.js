function fetchGroups() {
    var eventData = "getGroups";
    $.ajax({
        url: 'groups/fetchGroups', // Your PHP script to viewDetails process the event
        type: 'POST',
        data: {eventData: eventData},
        success: function(response) {
            $("#groupTable").html(response);
        }
    });
}

fetchGroups();
$(function() {
    $("#groupForm").submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        var groupForm = $(this).serialize();
        var submitBtn = $("#submitBtn");
        
        if (navigator.onLine) {
            $.ajax({
                url: 'groups/createGroup',
                method: 'post',
                data: groupForm,
                beforeSend: function() {
                    submitBtn.prop("disabled", true); // Disable the submit button
                    submitBtn.html("Processing..."); // Change button text to indicate processing
                },
                success: function(response) {
                    alert(response);
                    submitBtn.prop("disabled", false); // Re-enable the submit button
                    submitBtn.html("Save changes"); // Restore original button text
                    $("#groupForm")[0].reset();
                    fetchGroups();
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
