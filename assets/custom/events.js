document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var eventForm = document.getElementById('eventForm');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        themeSystem: 'bootstrap5',
        initialView: 'dayGridMonth',
            headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: 'base/fetchEventsCalendar',
        editable: true,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },

        dateClick: function(info) {
            
            $("#eventCalendarModal").modal('show');
            var clickedDate = formatDate(info.date);
            document.getElementById('start_date').value = clickedDate;
            document.getElementById('end_date').value = clickedDate;
            document.getElementById('start_time').value = '09:00'; // Prefill start_time with 09:00 AM
            document.getElementById('end_time').value = '11:00'; //
        },

        eventDidMount: function(info) {
            var start = new Date(info.event.startStr + 'T00:00:00');
            var end = new Date((info.event.endStr || info.event.startStr) + 'T23:59:59');

            if (info.event.allDay && info.event.end) {
                end.setDate(end.getDate() - 1);
            }

            var current = new Date(start);
            while (current <= end) {
                var dateString = current.toISOString().slice(0, 10);
                var el = document.querySelector('.fc-daygrid-day[data-date="' + dateString + '"]');
                if (el) {
                    var bootstrapClass = info.event.extendedProps.className;
                    el.classList.add(bootstrapClass);
                }
                current.setDate(current.getDate() + 1);
            }

            info.el.removeAttribute('style');
        },

        eventClick: function(info) {
            showEditModal(info.event);
        },
        eventDrop: function(info) {
            var eventId = info.event.id;
            var newStart = info.event.start.toISOString();
            var newEnd = info.event.end ? info.event.end.toISOString() : null;

            $.ajax({
                url: 'base/updateEventTime',
                type: 'POST',
                data: {
                    eventId: eventId,
                    start: newStart,
                    end: newEnd
                },
                success: function(response) {
                    callAddedEvents();
                },
                error: function(xhr, status, error) {
                }
            });
        },

        eventResize: function(info) {
            var eventId = info.event.id;
            var newStart = info.event.start.toISOString();
            var newEnd = info.event.end ? info.event.end.toISOString() : null;

            $.ajax({
                url: 'base/updateEventTime',
                type: 'POST',
                data: {
                    eventId: eventId,
                    start: newStart,
                    end: newEnd
                },
                success: function(response) {
                    callAddedEvents();
                },
                error: function(xhr, status, error) {

                }
            });
        }
    });

    calendar.render();
    //editDeleteModal
    
    function showEditModal(event) {
        var editDeleteModal = new bootstrap.Modal(document.getElementById('editDeleteModal'));
        document.getElementById('eventTitle').textContent = event.title;
        document.getElementById('eventDescription').textContent = event.description;

        var startDate = formatDate(event.start);
        var startTime = formatTime(event.start);
        var endDate = event.end ? formatDate(event.end) : startDate;
        var endTime = event.end ? formatTime(event.end) : startTime;

        document.getElementById('eventStartDate').textContent = startDate;
        document.getElementById('eventStartTime').textContent = startTime;
        document.getElementById('eventEndDate').textContent = endDate;
        document.getElementById('eventEndTime').textContent = endTime;

        document.getElementById('editEventBtn').addEventListener('click', function() {
            editDeleteModal.hide();
            showEditEventModal(event);
        });

        document.getElementById('deleteEventBtn').addEventListener('click', function() {
            editDeleteModal.hide();
            deleteEvent(event);
           
        });

        editDeleteModal.show();
    }

    $(document).on("click", ".editEvent", function(e){
        var event = $(this).data('id');
        $("#eventCalendarModal").modal("show");
        $.ajax({
            url: 'base/fetchSelectedEventDetails',
            type: 'POST',
            data: { eventId: event },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $("#title").val(data.title);
                    $("#description").val(data.description);
                    $("#start_date").val(data.start_date);
                    $("#start_time").val(data.start_time);
                    $("#end_date").val(data.end_date);
                    $("#end_time").val(data.end_time);
                    // $("#reminder_time").val(data.reminder_time);
                    $("#event_id").val(data.id);

                    // Check the appropriate color checkbox
                    $("input[name=color]").prop('checked', false); // Uncheck all checkboxes
                    $("#color_" + data.color).prop('checked', true); // Check the color checkbox
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching event details:', error);
            }
        }); 

        // Fetch event attendees and populate form inputs
        $.ajax({
            url: 'base/fetchEventAttendees',
            type: 'POST',
            data: { eventId: event },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $('#peopleInputs').empty();
                    data.forEach(function(attendee, index) {
                        var attendeeHtml = `
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="person_name_${index}" name="person_name[]" placeholder="Enter name" value="${attendee.name}">
                                <input type="tel" class="form-control" id="person_phone_${index}" name="person_phone[]" placeholder="Enter phone number" value="${attendee.phone}">
                                <input type="email" class="form-control" id="person_email_${index}" name="person_email[]" placeholder="Enter email address" value="${attendee.email}">
                                <button type="button" class="btn btn-outline-danger remove-person-btn"><i class="bi bi-person-fill-x"></i></button>
                            </div>
                        `;
                        $('#peopleInputs').append(attendeeHtml);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching event attendees:', error);
            }
        }); 
    });

    $(document).on("click", ".removeEvent", function(event) {
        var eventId = $(this).data('id');

        // Show confirmation dialog
        if (confirm("Are you sure you want to delete this event?")) {
            $.ajax({
                url: 'base/deleteSelectedEvent',
                type: 'POST',
                data: { eventId: eventId },
                success: function(data) {
                    // Handle success response
                    alert('Event deleted successfully');
                    callAddedEvents();
                    // Perform any further actions after deletion if needed
                },
                error: function(xhr, status, error) {
                    alert('Error deleting event:', error);
                }
            });
        }
    });

    // Helper function to format date as YYYY-MM-DD
    function formatDate(date) {
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        return `${year}-${month}-${day}`;
    }

    // Helper function to format time as HH:MM
    function formatTime(date) {
        var hours = ('0' + date.getHours()).slice(-2);
        var minutes = ('0' + date.getMinutes()).slice(-2);
        return `${hours}:${minutes}`;
    }

    function showEditEventModal(event) {
        var eventModal = new bootstrap.Modal(document.getElementById('eventCalendarModal'));
        document.getElementById('title').value = event.title;
        document.getElementById('description').value = event.description;
        document.getElementById('start_date').value = formatDate(event.start);
        document.getElementById('end_date').value = formatDate(event.end ? event.end : event.start);
        // document.getElementById('reminder_time').value = event.extendedProps.reminder_time ? event.extendedProps.reminder_time.replace(' ', 'T') : '';
        document.getElementById('event_id').value = event.id;

        // Separate date and time for start and end times
        var startTime = event.start ? event.start.toISOString().split('T') : ['', ''];
        var endTime = event.end ? event.end.toISOString().split('T') : startTime;
        document.getElementById('start_time').value = startTime[1].substring(0, 5); // Extract HH:MM format
        document.getElementById('end_time').value = endTime[1].substring(0, 5); // Extract HH:MM format

        // Fetch event attendees and populate form inputs
        $.ajax({
            url: 'base/fetchEventAttendees',
            type: 'POST',
            data: { eventId: event.id },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $('#peopleInputs').empty();
                    data.forEach(function(attendee, index) {
                        var attendeeHtml = `
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="person_name_${index}" name="person_name[]" placeholder="Enter name" value="${attendee.name}">
                                <input type="tel" class="form-control" id="person_phone_${index}" name="person_phone[]" placeholder="Enter phone number" value="${attendee.phone}">
                                <input type="email" class="form-control" id="person_email_${index}" name="person_email[]" placeholder="Enter email address" value="${attendee.email}">
                                <button type="button" class="btn btn-outline-danger remove-person-btn"><i class="bi bi-person-fill-x"></i></button>
                            </div>
                        `;
                        $('#peopleInputs').append(attendeeHtml);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching event attendees:', error);
            }
        });

        var eventColor = event.backgroundColor;
        $('input[name=color]').filter(function() {
            return $(this).val() === eventColor;
        }).prop('checked', true);

        eventModal.show();
    }

    function formatDate(date) {
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        return `${year}-${month}-${day}`;
    }

    // Function to handle event deletion
    function deleteEvent(event) {
        var eventId = event.id; 
         if (confirm("Are you sure you want to delete this event?")) {
            $.ajax({
                url: 'base/deleteEvent',
                type: 'POST',
                data:{eventId: eventId },
                success: function(response) {
                    calendar.refetchEvents();
                    callAddedEvents();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete event:', error);
                }
            });
        }else{
            return false;
        }

    }


    $(document).ready(function(){

        function initializeDatepickers() {
            $('#start_date').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0, // Set minimum date to today
                onSelect: function(selectedDate) {
                    $('#end_date').datepicker('option', 'minDate', selectedDate);
                    var reminderDate = new Date(selectedDate);
                    reminderDate.setDate(reminderDate.getDate() - 1); // Set reminder date to one day before start date
                    reminderDate.setHours(9, 0, 0, 0); // Set time to 09:00 hours
                    var reminderDateString = reminderDate.toISOString().slice(0, 16); // Format reminder date as YYYY-MM-DDTHH:mm
                    // $('#reminder_time').val(reminderDateString);
                }
            });

            $('#end_date').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0, // Set minimum date to today
            });

            $('#end_date').val($('#start_date').val());
        }

        // Call the function to initialize datepickers
        initializeDatepickers();
        
        $('#eventForm').submit(function(e) {
            e.preventDefault();
            var title = $('#title').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            // var reminder_time = $('#reminder_time').val();
            var color = $('#color').val();
            var event_id = $('#event_id').val();
            var formData = $(this).serialize();

            if (navigator.onLine) {
                $.ajax({
                    url: "base/createEvent",
                    method: "post",
                    data: formData,
                    beforeSend: function() {
                        $('#saveEvent').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    },
                    success: function(response) {
                        $('#saveEvent').prop('disabled', false).html('Save Event');
                        $('#eventForm')[0].reset();
                        alert("Event saved successfully!");
                        $('#eventCalendarModal').modal('hide');
                        calendar.refetchEvents();
                        callAddedEvents();
                    },
                    error: function() {
                        $('#saveEvent').prop('disabled', false).html('Save Event');
                        alert("Error saving event.");
                    }
                });
            } else {
                alert('No internet connection. Please check your network and try again.');
            }
        });

    });

    function callAddedEvents(){
        var eventData = "getEvents";
        $.ajax({
            url: 'base/fetchAddedEvent', // Your PHP script to process the event
            type: 'POST',
            data: {eventData: eventData},
            success: function(response) {
                $("#calendarEvents").html(response);
            }
        });
    }

    callAddedEvents();

});

$(document).ready(function() {
    var personCounter = 1;
    function addPersonInput() {
        var html = `
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="person_name_${personCounter}" name="person_name[]" placeholder="Enter name">
                <input type="tel" class="form-control" id="person_phone_${personCounter}" name="person_phone[]" value="260">
                <input type="email" class="form-control" id="person_email_${personCounter}" name="person_email[]" placeholder="Enter email address">
                <button type="button" class="btn btn-outline-danger" id="removePersonBtn_${personCounter}"><i class="bi bi-person-fill-x"></i></button>
            </div>
        `;
        $('#peopleInputs').append(html);

        $(`#removePersonBtn_${personCounter}`).click(function() {
            $(this).closest('.input-group').remove();
        });

        personCounter++;
    }

    $('#addPersonBtn').click(function() {
        addPersonInput();
    });

    $(document).on('click', '.remove-person-btn', function() {
        $(this).closest('.input-group').remove();
    });
});