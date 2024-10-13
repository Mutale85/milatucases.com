 <?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['names']?>  Events</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		$parentId = $_SESSION['parent_id'];
		$query = $connect->prepare("SELECT * FROM lawFirms WHERE parentId = ?");
		$query->execute([$parentId]);
		$users = $query->fetchAll(PDO::FETCH_ASSOC);
	?>
</head>
<body>
	<div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<?php include '../addon_side_nav.php'; ?>
        	<div class="layout-page">
          		<?php include '../addon_top_nav.php'; ?>
          		<div class="content-wrapper">
          			<div class="container-xxl flex-grow-1 container-p-y">
          				<div class="row">
          					<div class="col-md-12">
          						<div class="card h-100">
                                    <div class="card-header">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#eventCalendarModal">Add New Event</button>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title mb-5 border-bottom pb-4"><?php echo $_SESSION['names']?>'s Calendar</h5>
                                        <div class="row">
                                        	<div class="col-md-4">
                                        		<div id="WeeklyEvents"></div>
	                                        </div>
                                        	<div class="col-md-8">                                   
                                        		<div id='calendar'></div>
                                        	</div>
                                        	
                                        </div>
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
													                <label class="mb-2" for="is_case_related">Is the event case related?</label><br>
													                <input type="radio" id="case_related_yes" name="is_case_related" value="yes">
													                <label for="case_related_yes">Yes</label>
													                <input type="radio" id="case_related_no" name="is_case_related" value="no" checked>
													                <label for="case_related_no">No</label>
													            </div>

													            <div class="form-group col-md-12 mb-3" id="caseDropdownContainer" style="display:none;">
													                <label class="mb-2" for="case_id">Select Case</label>
													                <select class="form-control" id="case_id" name="case_id">
													                	<option value="">Select Case</option>
													                </select>
													            </div>
													            <div class="form-group col-md-12 mb-3" id="titleContainer">
													                <label class="mb-2" for="title">Title</label>
													                <input type="text" class="form-control" id="title" name="title">
													                <input type="hidden" class="form-control" id="eventId" name="eventId">
													                <input type="hidden" class="form-control" id="lawFirmId" name="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
													            </div>
													            <div class="form-group col-md-12 mb-3">
													                <label class="mb-2" for="description">Description</label>
													                <textarea class="form-control" id="description" name="description"></textarea>
													            </div>
													            <div class="form-group col-md-6 mb-3">
													                <label class="mb-2" for="start_date">Start Date</label>
													                <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Click here to add start date" required>
													            </div>
													            <div class="form-group col-md-6 mb-3">
													                <label class="mb-2" for="start_time">Start Time</label>
													                <input type="time" class="form-control" id="start_time" name="start_time" required value="07:00">
													            </div>
													            <div class="form-group col-md-6 mb-3">
													                <label class="mb-2" for="end_date">End Date</label>
													                <input type="text" class="form-control" id="end_date" name="end_date" placeholder="Click here to add end date" required>
													            </div>
													            <div class="form-group col-md-6 mb-3">
													                <label class="mb-2" for="end_time">End Time</label>
													                <input type="time" class="form-control" id="end_time" name="end_time" required value="09:00">
													            </div>
													            <div class="form-group mb-3">
													                <label class="mb-2">COLOUR CODE</label><br>
													                <div class="form-check form-check-inline">
													                    <input class="form-check-input color-checkbox" type="checkbox" id="color_red" name="color" value="red">
													                    <label class="form-check-label text-danger" for="color_red">Court</label>
													                </div><br>
													                <div class="form-check form-check-inline">
													                    <input class="form-check-input color-checkbox" type="checkbox" id="color_blue" name="color" value="blue">
													                    <label class="form-check-label text-primary" for="color_blue">Consult</label>
													                </div><br>
													                <div class="form-check form-check-inline">
													                    <input class="form-check-input color-checkbox" type="checkbox" id="color_green" name="color" value="green">
													                    <label class="form-check-label text-success" for="color_green">Client Meeting</label>
													                </div><br>
													                <div class="form-check form-check-inline">
													                    <input class="form-check-input color-checkbox" type="checkbox" id="color_yellow" name="color" value="yellow">
													                    <label class="form-check-label text-warning" for="color_yellow">Staff Meeting</label>
													                </div>
													                <br>
													                <div class="form-check form-check-inline">
													                    <input class="form-check-input color-checkbox" type="checkbox" id="color_gray" name="color" value="gray">
													                    <label class="form-check-label text-secondary" for="color_gray">General</label>
													                </div>
													            </div>
													        </div>
													    </div>
													    <div class="modal-footer">
													        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
													        <button type="submit" class="btn btn-primary" id="saveEvent">Save Event</button>
													    </div>
													</form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Email Modal -->
                                        <div class="modal right fade" id="emailCalendarModal" tabindex="-1" aria-labelledby="calendarModal" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="calendarModal">Send Weekly Calendar</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form id="emailCalendaForm">
													    <div class="modal-body">
													        <div class="row">
													            <div class="form-group col-md-12 mb-3" id="titleContainer">
													                <label class="mb-2" for="email">Enter Email</label>
													                <input type="email" class="form-control" id="email" name="email">
													                <input type="hidden" class="form-control" id="userId" name="userId" value="<?php echo $_SESSION['user_id']?>">
													                <input type="hidden" class="form-control" id="lawFirmId" name="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
													            </div>
													        </div>
													    </div>
													    <div class="modal-footer">
													        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
													        <button type="submit" class="btn btn-primary" id="sendEmailBtn">Send Calendar</button>
													    </div>
													</form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
          					</div>
          				</div>
          				<!-- Modal -->
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
                                        <button type="button" class="btn btn-primary btn-sm" id="editEventBtn">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm" id="deleteEventBtn">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
          			</div>
          			<?php include '../addon_footer.php';?>

          			<div class="content-backdrop fade"></div>
          		</div>
          	</div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer_links.php';?>
    <script src="../assets/js/fullCalendar.min.js"></script> 
    <script src="../dist/controls/personal_events.js"></script>
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

        $(document).ready(function() {
		    $('input[name="is_case_related"]').change(function() {
		        if ($('input[name="is_case_related"]:checked').val() === 'yes') {
		            $('#titleContainer').hide();
		            $('#caseDropdownContainer').show();
		        } else {
		            $('#titleContainer').show();
		            $('#caseDropdownContainer').hide();
		        }
		    });

		    // Initially hide case dropdown
		    $('#caseDropdownContainer').hide();

		    // Fetch and populate cases in the dropdown
		    $.ajax({
		        url: 'events/fetch-cases',
		        method: 'GET',
		        success: function(data) {
		            var cases = JSON.parse(data);
		            var caseDropdown = $('#case_id');
		            caseDropdown.empty();
		            cases.forEach(function(caseItem) {
		                caseDropdown.append($('<option>', {
		                    value: caseItem.id,
		                    text: caseItem.caseTitle
		                }));
		            });
		        }
		    });
		})


		document.getElementById('emailCalendaForm').addEventListener('submit', function(e) {
		    e.preventDefault();
		    
		    var email = document.getElementById('email').value;
		    var userId = document.getElementById('userId').value;
		    var lawFirmId = document.getElementById('lawFirmId').value;
		    
		    // Disable the submit button and show loading state
		    var submitBtn = document.getElementById('sendEmailBtn');
		    submitBtn.disabled = true;
		    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Sending...';
		    
		    // Send AJAX request
		    var xhr = new XMLHttpRequest();
		    xhr.open('POST', 'eventsPersonal/send_calendar_email', true);
		    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		    xhr.onload = function() {
		        if (xhr.status === 200) {
		            var response = JSON.parse(xhr.responseText);
		            if (response.success) {
		                alert('Calendar sent successfully!');
		                // Close the modal
		                var modal = bootstrap.Modal.getInstance(document.getElementById('emailCalendarModal'));
		                modal.hide();
		            } else {
		                alert('Error: ' + response.message);
		            }
		        } else {
		            alert('Error sending calendar. Please try again.');
		        }
		        // Re-enable the submit button
		        submitBtn.disabled = false;
		        submitBtn.innerHTML = 'Send Calendar';
		    };
		    xhr.send('email=' + encodeURIComponent(email) + '&userId=' + encodeURIComponent(userId) + '&lawFirmId=' + encodeURIComponent(lawFirmId));
		});

    </script>
</body>
</html>