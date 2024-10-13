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
        events: 'eventsPersonal/fetchEventsCalendar',
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
            document.getElementById('end_time').value = '11:00'; // Prefill end_time with 11:00 AM
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
                url: 'eventsPersonal/updateEventTime',
                type: 'POST',
                data: {
                    eventId: eventId,
                    start: newStart,
                    end: newEnd
                },
                success: function(response) {
                    calendar.refetchEvents();
                    callAddedEvents();
                },
                error: function(xhr, status, error) {
                    console.error('Error updating event time:', error);
                }
            });
        },

        eventResize: function(info) {
            var eventId = info.event.id;
            var newStart = info.event.start.toISOString();
            var newEnd = info.event.end ? info.event.end.toISOString() : null;

            $.ajax({
                url: 'eventsPersonal/updateEventTime',
                type: 'POST',
                data: {
                    eventId: eventId,
                    start: newStart,
                    end: newEnd
                },
                success: function(response) {
                    calendar.refetchEvents()
                    callAddedEvents();
                },
                error: function(xhr, status, error) {
                    console.error('Error updating event time:', error);
                }
            });
        }
    });

    calendar.render();
    
    function showEditModal(event) {
        var editDeleteModal = new bootstrap.Modal(document.getElementById('editDeleteModal'));
        document.getElementById('eventTitle').textContent = event.title;
        document.getElementById('eventDescription').textContent = event.extendedProps.description || '';

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

    

    $(document).on("click", ".removeEvent", function(event) {
        var eventId = $(this).data('id');

        // Show confirmation dialog
        if (confirm("Are you sure you want to delete this event?")) {
            $.ajax({
                url: 'eventsPersonal/deleteSelectedEvent',
                type: 'POST',
                data: { eventId: eventId },
                success: function(data) {
                    // Handle success response
                    sweetSuccess(data);
                    calendar.refetchEvents();
                    callAddedEvents();
                    // Perform any further actions after deletion if needed
                },
                error: function(xhr, status, error) {
                    sweetError('Error deleting event:', error);
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
        // var eventModal = new bootstrap.Modal(document.getElementById('eventCalendarModal'));
        document.getElementById('title').value = event.title;
        document.getElementById('description').value = event.extendedProps.description || '';
        document.getElementById('start_date').value = formatDate(event.start);
        document.getElementById('start_time').value = formatTime(event.start);
        document.getElementById('end_date').value = event.end ? formatDate(event.end) : formatDate(event.start);
        document.getElementById('end_time').value = event.end ? formatTime(event.end) : formatTime(event.start);
        $("#eventCalendarModal").modal("show");
        $.ajax({
            url: 'eventsPersonal/fetchSelectedEventDetails',
            type: 'POST',
            data: { eventId: event.id },
            dataType: 'json',
            success: function(data) {
                if (data.event) {
                    // Check if the event is case-related
                    if (data.event.is_case_related === 'yes') {
                        // Hide the title input field
                        $("#titleContainer").hide();
                        // Show the case dropdown and set the selected option to the event title
                        $("#caseDropdownContainer").show();
                        // $("#case_id").empty(); // Clear existing options
                        $("#case_id").append(new Option(data.event.title, data.event.title));
                        // $("#case_id").val(data.id);
                        $("#case_related_yes").prop('checked', true);
                    } else {
                        // Show the title input field
                        $("#titleContainer").show();
                        // Hide the case dropdown
                        $("#caseDropdownContainer").hide();
                        // Set the title input value
                        $("#title").val(data.event.title);
                        $("#case_related_no").prop('checked', true);
                    }

                    // Populate the other fields
                    $("#description").val(data.event.description);
                    $("#start_date").val(data.event.start_date);
                    $("#start_time").val(data.event.start_time);
                    $("#end_date").val(data.event.end_date);
                    $("#end_time").val(data.event.end_time);
                    $("#eventId").val(data.event.event_id);
                    $("input[name=color]").prop('checked', false); // Uncheck all checkboxes
                    $("#color_" + data.event.color).prop('checked', true); // Check the color checkbox
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching event details:', error);
            }
        });
    }
    /*
    function deleteEvent(event) {
        var eventId = event.id;
        if(confirm("You wish to remove the event")){
           
            $.ajax({
                url: 'eventsPersonal/deleteEvent',
                type: 'POST',
                data: { eventId: eventId },
                success: function(response) {
                    calendar.refetchEvents()
                    callAddedEvents();
                    sweetSuccess("Event Deleted and alerts sent to attendees");
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting event:', error);
                }
            });
        }else{
            return false;
        }
    }
    */

    function deleteEvent(event) {
      var eventId = event.id;

      Swal.fire({
        title: "Do you want to delete this event?",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "Delete",
        denyButtonText: `Don't delete`
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'eventsPersonal/deleteEvent',
            type: 'POST',
            data: { eventId: eventId },
            success: function(response) {
              calendar.refetchEvents();
              callAddedEvents();
              sweetSuccess("Event Deleted and alerts sent to attendees");
              // setTimeout(function() {
              //   location.reload();
              // }, 1500);
            },
            error: function(xhr, status, error) {
              console.error('Error deleting event:', error);
            }
          });
        } else if (result.isDenied) {
          Swal.fire("Changes are not saved", "", "info");
        }
      });
    }

    $(document).ready(function(){
        function initializeDatepickers() {
            $('#start_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: 0,
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
                changeMonth: true,
                changeYear: true,
                minDate: 0,
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
            var color = $('#color').val();
            var event_id = $('#event_id').val();
            var formData = $(this).serialize();

            if (navigator.onLine) {
                $.ajax({
                    url: "eventsPersonal/createEvent",
                    method: "post",
                    data: formData,
                    beforeSend: function() {
                        $('#saveEvent').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    },
                    success: function(response) {
                        $('#saveEvent').prop('disabled', false).html('Save Event');
                        $('#eventForm')[0].reset();
                        sweetSuccess(response);
                        $('#eventCalendarModal').modal('hide');
                        calendar.refetchEvents();
                        callAddedEvents();
                    },
                    error: function(xhr, status, error) {
                        $('#saveEvent').prop('disabled', false).html('Save Event');
                        sweetError(error);
                    }
                });
            } else {
                sweetError('No internet connection. Please check your network and try again.');
            }
        });
    });

    function callAddedEvents(){
        var eventData = "getEvents";
        $.ajax({
            url: 'eventsPersonal/fetchAddedEvent', // Your PHP script to process the event
            type: 'POST',
            data: {eventData: eventData},
            success: function(response) {
                $("#calendarEvents").html(response);
            }
        });

        $.ajax({
            url: 'eventsPersonal/fetchWeeklyEvents', // Your PHP script to process the event
            type: 'POST',
            data: {eventData: eventData},
            success: function(response) {
                $("#WeeklyEvents").html(response);
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