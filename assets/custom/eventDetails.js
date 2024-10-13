function callAddedEvents(){
    var eventData = "getEvents";
    $.ajax({
        url: 'base/fetchEventsDetails', // Your PHP script to process the event
        type: 'POST',
        data: {eventData: eventData},
        success: function(response) {
            $("#calendarEvents").html(response);
            
        }
    });
}

callAddedEvents();

$(document).ready(function() {
    $(document).on('click','.viewAttendees', function(e) {
        e.preventDefault(); // Prevent default link behavior

        var eventId = $(this).data('eventid'); // Get the event ID from the data attribute

        // Fetch event attendees using AJAX
        $.ajax({
            url: 'base/fetchAttendeesDetail', // Update with the correct URL
            type: 'POST',
            data: { eventId: eventId },
            success: function(response) {
                $('#attendeesDiv').html(response);
                $('#attendeesModal').modal('show'); // Show the modal
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


    $('#messageForm').submit(function(e) {
        e.preventDefault();
        var message = $('#message').val();
        var selectedAttendees = [];

        $('.attendeeCheckbox:checked').each(function() {
            selectedAttendees.push($(this).val());
        });


        $.ajax({
            url: 'base/send_message',
            type: 'POST',
            data: { message: message, attendees: selectedAttendees },
            dataType: 'json',
            beforeSend: function() {
                $("#messageBtn").html('Processing...').prop('disabled', true);
            },
            success: function(response) {
                alert('Message sent successfully');
                $('#messageForm')[0].reset();
                $("#messageBtn").prop('disabled', false).html('Send Message');
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
            },
            complete: function() {
                $("#messageBtn").prop('disabled', false).html('Send Message');
            }
        });
    });
});

$(document).on('change', '.statusCheckbox', function() {
    var eventId = $(this).data('eventid');
    var isChecked = $(this).prop('checked');
    if (!confirm('Are you sure you want to change the status?')) {
        $(this).prop('checked', false);
        return;
    }

    $.ajax({
        url: 'base/updateEventStatus',
        method: 'POST',
        data: { eventId: eventId, isChecked: 1 },
        success: function(response) {
            // Handle the response if needed
            alert(response);
            callAddedEvents();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});


// =========== adding a checklist ========

$(document).on('click', '.checkListModal', function (e) {
    $('#new-item').focus();
    e.preventDefault();
    $("#addItemModal").modal('show');
    $('#event-id').val($(this).data('id'));
    updateChecklistDisplay($(this).data('id'));
});

$('#add-item-btn').click(function() {
    var item = $('#new-item').val();
    var event_id = $('#event-id').val();

    $('#add-item-btn').html('Processing...');

    $.post('base/createEventChecklist', {item: item, event_id: event_id}, function(data) {
        if (data.success) {
            $('#add-item-btn').html('Add Item');
            updateChecklistDisplay(event_id);
        } else {
            $('#add-item-btn').html('Add Item');

            updateChecklistDisplay(event_id);
        }
    });
});

function updateChecklistDisplay(event_id) {
    $.post('base/getEventChecklist', {event_id: event_id}, function(data) {
        $('#checkListDiv').html(data);
        $("#checklist-form")[0].reset();
    });
}


$(document).on('change', '.checklist-item', function() {
    var id = $(this).attr('id').replace('check_', '');
    var event_id = $(this).data("event_id");
    var checked = $(this).is(':checked') ? 1 : 0;

    if (confirm("Are you sure you want to update the status?")) {
        $.post('base/eventCheckListUpdate', {id: id, checked: checked}, function(data) {
            // Handle response checkListModal
            if (!data) {
                alert('Failed to update status. Please try again.');
            } else {
                alert(data);
                updateChecklistDisplay(event_id);
            }
        });
    } else {
        // If the user cancels the confirmation, prevent the checkbox from being checked
        $(this).prop('checked', !checked);
    }
});


$(document).on('click', '.delete-item', function() {
    var id = $(this).data('id');
    var event_id = $(this).data("event_id");


    if (confirm("Are you sure you want to delete this item?")) {
        $.post('base/deleteEventCheckListItem', {id: id}, function(data) {
            // Handle response
            if (data) {
                
                updateChecklistDisplay(event_id);
            } else {
                alert('Failed to delete item. Please try again.');
            }
        });
        updateChecklistDisplay(event_id);
    }
});

function printContent(el) {
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();

    // Remove the last th and td (Delete column)
    printcontent.find('th:last-child, td:last-child').remove();

    $('body').empty().html(printcontent);

    // Store current scroll position
    var scrollPos = $(window).scrollTop();

    setTimeout(function() {
        window.print();
        setTimeout(function() {
            if ($(window).scrollTop() === scrollPos) {
                location.reload();
            } else {
                $('body').html(restorepage);
            }
        }, 500); 
    }, 100);

    window.onafterprint = function() {
        $('body').html(restorepage);
    };
}
