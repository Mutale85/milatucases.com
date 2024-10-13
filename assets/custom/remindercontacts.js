$(document).ready(function() {
    $('#appointmentForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        // Check if the internet is active
        if (navigator.onLine) {
            $.ajax({
                type: 'POST',
                url: 'base/createAppointment',
                data: formData,
                beforeSend: function() {
                    $('#addbtn').html('Loading...<div class="spinner-border text-primary" role="status"></div>');
                },
                success: function(response) {
                    alert('Appointment saved successfully!');
                    $('#appointmentForm')[0].reset();
                    $('#addbtn').html('Submit');
                    appointmentsTable();
                },
                error: function() {
                    alert('Error saving appointment.');
                },
                complete: function() {
                    $('#addbtn').html('Submit');
                }
            });
        } else {
            alert('No internet connection. Please check your network and try again.');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var appointmentDateInput = document.getElementById('appointment_date');
    var reminderDateInput = document.getElementById('reminder_date');
    var today = new Date();
    var minDate = today.toISOString().slice(0, 16);
    appointmentDateInput.min = minDate;

    appointmentDateInput.addEventListener('change', function() {
        var appointmentDate = new Date(this.value);
        appointmentDate.setDate(appointmentDate.getDate() - 1);
        appointmentDate.setHours(11, 0, 0);
        reminderDateInput.value = appointmentDate.toISOString().slice(0, 16);
    });
});

function appointmentsTable(){
	var fetch = 'appointments';
	$.ajax({
        type: 'POST',
        url: 'base/fetchAppointments',
        data: {fetch:fetch},
        
        success: function(response) {
            $('#appointmentsTable').html(response);
        },
    });
}

appointmentsTable();


$(document).ready(function() {
    // Listen for click events on the "Edit" buttons
    $(document).on('click', '.editApp', function() {
        var appointmentId = $(this).data('id');

        $.ajax({
            url: 'base/fetchSelectedAppointment',
            type: 'POST',
            data: { id: appointmentId },
            dataType: 'json',
            success: function(appointment) {
                // Fill the form with the appointment details
                $('#appointmentsModal').modal("show");
                $('#title').val(appointment.title);
                $('#names').val(appointment.names);
                $('#from_location').val(appointment.from_location);
                $('#phone_number').val(appointment.phone_number);
                $('#email').val(appointment.email);
                $('#purpose').val(appointment.purpose);
                $('#appointment_date').val(appointment.appointment_date);
                $('#reminder_date').val(appointment.reminder_date);
                $('#appointment_id').val(appointment.id);
            }
        });
    });
});

// Listen for click events on the "Delete" buttons
$(document).on('click', '.deleteApp', function() {
    if (confirm('Are you sure you want to delete this appointment?')) {
        var appointmentId = $(this).data('id');

        // Send an AJAX request to delete the appointment
        $.ajax({
            url: 'base/delete_appointment',
            type: 'POST',
            data: { id: appointmentId },
            success: function(response) {
                alert('Appointment deleted successfully');
                // Reload the page to update the appointments list
                // location.reload();
                 appointmentsTable();
            },
            error: function() {
                alert('Error deleting appointment');
            }
        });
    }
});
