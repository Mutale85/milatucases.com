function loadReminderContacts() {
    $.ajax({
        url: 'process/fetch_reminder_contacts', // Path to your PHP script for fetching contacts
        type: 'GET',
        success: function(response) {
            $('#loadReminderContacts').html(response);
        },
        error: function() {
            alert('Error loading contacts');
        }
    });
}

$(document).ready(function() {

    $('#reminderContacts').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        if (navigator.onLine) {
            $.ajax({
                url: 'process/submit_reminder_contacts',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert("Contacts saved successfully");
                        // Additional actions (e.g., clear form, close modal)
                        $("#reminderContacts")[0].reset();
                    } else {
                        alert("Error: " + response.error);
                    }
                    loadReminderContacts();
                },
                error: function() {
                    alert("An error occurred while submitting the data.");
                }
            });
        }else{
            alert('No internet connection. Please check your network and try again.');

        }
    });
});

$(document).on('click', '.editReminder-btn', function() {
    var reminderContactId = $(this).data('id');
    $.ajax({
        url: 'process/get_reminder_contact',
        type: 'POST',
        data: { 'id': reminderContactId },
        dataType:'Json',
        success: function(response) {
            $('#contact_id').val(response.id);
            $('#contact_name').val(response.contact_name);
            $('#contact_email').val(response.contact_email);
            $('#contact_phone').val(response.contact_phone);
            $('#remindercontactModalbtn').click();
        }
    });
});

$(document).on('click', '.deleteReminder-btn', function() {
    var reminderContactId = $(this).data('id');
    if(confirm("Are you sure you want to delete this contact?")) {
        $.ajax({
            url: 'process/delete_reminder_contact', // Path to your PHP script for deletion
            type: 'POST',
            data: { 'id': reminderContactId },
            success: function(response) {
                alert(response);
                loadReminderContacts();
            },
            error: function() {
                alert('Error deleting contact');
            }
        });
    }
});
loadReminderContacts();
