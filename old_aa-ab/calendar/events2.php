<?php include('../../includes/db.php')?>
<?php require('../base/base.php')?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include '../addon_header.php'?>

        <style>
            .fc-h-event .fc-event-title {
                display: inline-block;
                left: 0px;
                max-width: 100%;
                height: 25px;
                overflow: hidden;
                right: 0px;
                vertical-align: top;
            }
            
        </style>
    </head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include '../addon_side_nav.php';?>        
            <div class="layout-page">
                <?php include '../addon_top_nav.php';?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 mb-3">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventCalendarModal">Add New Event</button>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Events Calendar</h5>                                    
                                        <div id='calendar'></div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="modal right fade" id="eventCalendarModal" tabindex="-1" aria-labelledby="calendarModal" aria-hidden="true">

                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="calendarModal">Add Event</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form id="eventForm">
                                                        <div class="modal-body">
                                                        
                                                            <div class="row">
                                                                <div class="form-group col-md-12 mb-3">
                                                                    <label class="mb-2" for="title">Title</label>
                                                                    <input type="text" class="form-control" id="title" name="title" required>
                                                                    <input type="hidden" class="form-control" id="event_id" name="event_id">
                                                                </div>
                                                                <div class="form-group col-md-12 mb-3">
                                                                    <label class="mb-2" for="title">Description</label>
                                                                    <textarea type="text" class="form-control" id="description" name="description" ></textarea>
                                                                </div>
                                                                <div class="form-group col-md-6 mb-3">
                                                                    <label class="mb-2" for="start_date">Start Date</label>
                                                                    <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Click here to add to start date" required>
                                                                </div>
                                                                <div class="form-group col-md-6 mb-3">
                                                                    <label class="mb-2" for="start_time">Start Time</label>
                                                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                                                </div>
                                                                <div class="form-group col-md-6 mb-3">
                                                                    <label class="mb-2" for="end_date">End Date</label>
                                                                    <input type="text" class="form-control" id="end_date" name="end_date" placeholder="Click here to add end date" required>  
                                                                </div>
                                                                <div class="form-group col-md-6 mb-3">
                                                                    <label class="mb-2" for="end_time">End Time</label>
                                                                    <input type="time" class="form-control" id="end_time" name="end_time" placeholder="Click here to add end date" required>  
                                                                </div>
                                                                <!-- <div class="form-group col-md-12 mb-3">
                                                                    <label class="mb-2" for="reminder_time">Reminder Time</label>
                                                                    <input type="datetime-local" class="form-control" id="reminder_time" name="reminder_time">
                                                                </div> -->
                                                                
                                                                <div class="form-group mb-3">
                                                                    <label class="mb-2">FILTER</label><br>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input color-checkbox" type="checkbox" id="color_red" name="color" value="red">
                                                                        <label class="form-check-label text-danger" for="color_red">Red</label>
                                                                    </div><br>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input color-checkbox" type="checkbox" id="color_blue" name="color" value="blue">
                                                                        <label class="form-check-label text-primary" for="color_blue">Blue</label>
                                                                    </div><br>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input color-checkbox" type="checkbox" id="color_green" name="color" value="green">
                                                                        <label class="form-check-label text-success" for="color_green">Green</label>
                                                                    </div><br>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input color-checkbox" type="checkbox" id="color_yellow" name="color" value="yellow">
                                                                        <label class="form-check-label text-warning" for="color_yellow">Yellow</label>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="border-top pt-3"></div>
                                                            <button type="button" class="btn btn-secondary btn-sm" id="addPersonBtn"><i class="bi bi-person-add"></i> Add Event Attendees</button>
                                                            <div id="peopleInputs" class="border-bottom pb-3 mt-3"></div>
                                                        
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
                                                            <button type="submit" class="btn btn-primary" id="saveEvent">Save Event</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventCalendarModal">Add New Event</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-12 mb-3">
                                <div class="card h-100">
                                    
                                    <div class="card-header">
                                        <h5 class="card-title">Added Events</h5>
                                    </div>
                                    <div class="card-body">                                        
                                        <div class="table table-responsive">
                                            <table class="table table-striped" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Details</th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id='calendarEvents'>
                                                    <?php //echo fetchandDisplayEVent();?>
                                                </tbody>
                                            </table>
                                        </div>
                                    
                                    </div>
                                
                                    <div class="card-footer">
                                        
                                        <div class="modal fade" id="editDeleteModal" tabindex="-1" aria-labelledby="editDeleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editDeleteModalLabel">Even Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Title: <span id="eventTitle"></span> </p>
                                                        <p>Details: <span id="eventDescription"></span></p>
                                                        <p>From : <span id="eventStartDate"></span> : <span id="eventStartTime"></span></p>
                                                        <p>To: <span id="eventEndDate"></span> : <span id="eventEndTime"></span></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" id="editEventBtn">Edit</button>
                                                        <button type="button" class="btn btn-danger" id="deleteEventBtn">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer.php';?>
    <?php include '../addon_footer_links.php';?>
    <!-- -->
    <script src="../assets/js/fullCalendar.min.js"></script> 
    <script src="../assets/custom/events.js"></script>

    <script>
        // Select all checkboxes with the class 'color-checkbox'
        var checkboxes = document.querySelectorAll('.color-checkbox');

        // Add a change event listener to each checkbox
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // If the checkbox is checked
                if (checkbox.checked) {
                    // Uncheck all other checkboxes
                    checkboxes.forEach(function(otherCheckbox) {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>