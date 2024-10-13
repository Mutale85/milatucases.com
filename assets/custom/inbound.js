function callAddedVisitis(){
    var eventData = "getInboundCalls";
    $.ajax({
        url: 'courtesy/fetchInboundVisits', // Your PHP script to process the event
        type: 'POST',
        data: {eventData: eventData},
        success: function(response) {
            $("#InboundData").html(response);
            
        }
    });
}
callAddedVisitis()
function populateCountries(dropdownElement) {
    $.get('papa/fetchCountries', function(data) {
        var countries = JSON.parse(data);

        // Clear the dropdown first
        dropdownElement.empty();

        countries.forEach(function(country) {
            var option = $('<option>', {
                value: country.id,
                text: country.country_name
            });
            dropdownElement.append(option);
        });
    });
}
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0];            
    document.getElementById('visit-date').setAttribute('min', today);
});

$(document).ready(function() {
    populateCountries($('.country-select'));
});

$(document).ready(function() {
    $('#courtesyCallForm').submit(function(event) {
        event.preventDefault();

        // Check if the browser is online
        if (!navigator.onLine) {
            alert('No internet connection. Please check your network settings.');
            return;
        }

        var formData = $(this).serialize();

        // Show spinner before AJAX request
        $('#spinner').show();

        $.ajax({
            type: 'POST',
            url: 'courtesy/createInboundCourtesyCall',
            data: formData,
            beforeSend:function(){
                $("#saveInbound").prop('disabled', true).html(`Processing... <div class="spinner-border text-primary" role="status" ></div>`);
            },
            success: function(response) {
                alert('Form submitted successfully!');
                callAddedVisitis();
                $('#courtesyCallForm').trigger('reset');
                $('#visit_id').val("");
                $("#saveInbound").prop('disabled', false).html('Save Inbound Call');
                
            },
            error: function(xhr, status, error) {
                alert('An error occurred while submitting the form.');
                console.error(xhr.responseText);
            },
            complete: function() {
                // Hide spinner after AJAX request is complete
                $('#spinner').hide();
            }
        });
    });
});


$(document).ready(function() {
    // Initialize DataTable
    
    
    // Handle edit button click
    $(document).on('click', '.editInboundVisit', function() {
        var visitId = $(this).data('id');
        
        // AJAX request to fetch visit details
        $.ajax({
            url: 'courtesy/fetchSelectedVisit',
            method: 'POST',
            data: { visit_id: visitId },
            dataType: 'json',
            success: function(response) {
                // Populate form fields with retrieved data
                $('#visit_id').val(response.visit_id);
                $('#visitor-name').val(response.visitor_name);
                $('#visitor-institution').val(response.visitor_institution);
                $('#visitor-designation').val(response.visitor_designation);
                $('#visitor-email').val(response.visitor_email);
                $('#visitor-phone').val(response.visitor_phone);
                $('#visitor-country').val(response.visitor_country);
                $('#visit-date').val(response.visit_date);
                $('#visit-time').val(response.visit_time);
                $('#visits-description').val(response.visitor_description);
                $('#reminder_time').val(response.reminder);
                
                // Show the modal
                $('#courtesyModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while fetching visit details.');
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.deleteInboundVisit', function() {
        var visitId = $(this).data('id');
        if (confirm('Are you sure you want to delete this visit?')) {
            // Send AJAX request to delete_visit.php with visitId parameter
            $.ajax({
                url: 'courtesy/deleteInboundVisit',
                method: 'POST',
                data: { visit_id: visitId },
                success: function(response) {
                    // Reload DataTable on success
                    // $('#visitsTable').DataTable().ajax.reload();
                    callAddedVisitis();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while deleting the visit.');
                }
            });
        }
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
            callAddedVisitis();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});