<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Worked Timer - Clients</title>
	<?php include '../addon_header.php'; ?>
    <?php 
        if (isset($_GET['clientId'])) {
            $clientId = $_GET['clientId'];
            $caseId = $_GET['caseId'];
        }
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
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item"><a href="./">Home</a> / Cases / <?php echo getClientNameById($clientId, $lawFirmId)?></li>
                                </ul>
                            </div>
          				 	<div class="col mb-4">
                               <div class="card mt-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title"><?php echo fetchCaseTitle($caseId, $clientId)?> </h5>
                                        <button class="btn btn-primary btn-sm" id="newTimerBtn">New Time Entry</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php 
                                                // echo fetchClientInfoById($clientId)
                                                $clientInfo = fetchClientInfoById($clientId);
                                                $displayHtml = displayClientInfo($clientInfo);
                                                echo $displayHtml;
                                                ?>
                                            </div>
                                            <div class="col-md-6">
                                                <p>Date: <?php echo date('d F Y')?></p>
                                                <p>Matter Id: <?php echo fetchCaseNumber($caseId, $lawFirmId)?></p>
                                            </div>
                                        </div>
                                        <div class="table table-responsive">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Task Description</th>
                                                        <th>Time</th>
                                                        <th>Hourly Charge</th>
                                                        <th>Total Charge</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        $totalAmount = 0;
                                                        $query = $connect->prepare("SELECT * FROM `time_entries` WHERE caseId = ? AND clientId = ?");
                                                        $query->execute([$caseId, $clientId]);
                                                        if($query->rowCount() > 0){
                                                            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                                                $date = date('d.m.y', strtotime($row['dateCreated']));
                                                                $taskDescription = htmlspecialchars($row['description']);
                                                                $timeSpent = sprintf("%02d:%02d", $row['hours'], $row['minutes']);
                                                                $hourlyRate = number_format($row['hourlyRate'], 2);
                                                                $totalCharge = number_format($row['cost'], 2);
                                                                $totalAmount += $row['cost'];
                                                    ?>
                                                                <tr data-id="<?php echo $row['id']; ?>">
                                                                    <td><?php echo $date; ?></td>
                                                                    <td><?php echo decrypt($taskDescription); ?></td>
                                                                    <td><?php echo $timeSpent; ?></td>
                                                                    <td><?php echo $hourlyRate; ?></td>
                                                                    <td><?php echo $totalCharge; ?></td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                            <button class="btn btn-sm btn-primary editTimerLog" data-id="<?php echo $row['id']; ?>">Edit</button>
                                                                            <button class="btn btn-sm btn-danger deleteTimerLog" data-id="<?php echo $row['id']; ?>">Delete</button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                    <?php } 
                                                        }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                                                        <td><strong><?php echo number_format($totalAmount, 2); ?></strong></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button data-client-id="<?php echo $clientId?>" 
                                            data-case-id="<?php echo $caseId?>"
                                            data-client-email="<?php echo fetchClientEmailByTPIN($clientId, $lawFirmId)?>"
                                            class="btn btn-primary btn-sm send-email-btn" data-action="email">
                                            <i class="bi bi-receipt-cutoff"></i>
                                            Send as Email
                                        </button>
                                        <button class="btn btn-dark btn-sm open-date-range-modal" id="createPdf" data-action="pdf"
                                            data-client-id="<?php echo $clientId?>" 
                                            data-case-id="<?php echo $caseId?>">
                                            <i class="bi bi-file-pdf"></i> Generate PDF
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Range Modal -->
                            <div class="modal fade" id="dateRangeModal" tabindex="-1" role="dialog" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="dateRangeModalLabel">Select Date Range for PDF</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="dateRangeForm">
                                                <div class="mb-3">
                                                    <label for="startDate" class="form-label">Start Date</label>
                                                    <input type="date" class="form-control" id="startDate" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="endDate" class="form-label">End Date</label>
                                                    <input type="date" class="form-control" id="endDate" required>
                                                </div>
                                                <input type="hidden" id="dateRangeClientId">
                                                <input type="hidden" id="dateRangeCaseId">
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="createPdfBtn">Generate PDF</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- <div class="modal fade" id="timerModal" tabindex="-1" aria-labelledby="timerModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="timerForm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="timerModalLabel">Edit Timer Log</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group mb-3">
                                                    <label for="taskDescription" class="mb-1">Task Description</label>
                                                    <textarea class="form-control" id="taskDescription" name="taskDescription" rows="3" required></textarea>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="hourlyRate" class="mb-1">Hourly Rate</label>
                                                    <input type="text" class="form-control" id="hourlyRate" name="hourlyRate" required>
                                                </div>
                                                <input type="hidden" id="logId" name="logId">
                                                <input type="hidden" id="caseId" name="caseId">
                                                <input type="hidden" id="clientId" name="clientId">
                                                <input type="hidden" id="lawFirmId" name="lawFirmId" value="<?php echo $lawFirmId?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" id="submitTimer">Update Timer Log</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div> -->

                            <!-- Updated Email Modal HTML -->
                            <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="emailModalLabel">Send Fee Note</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form id="sendEmailForm">
                                            <div class="modal-body">
                                                <div class="form-group mb-3">
                                                    <label for="emailInput">Email address</label>
                                                    <input type="email" class="form-control" id="emailInput" name="emailInput" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="startDateInput">Start Date</label>
                                                    <input type="date" class="form-control" id="startDateInput" name="startDateInput" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="endDateInput">End Date</label>
                                                    <input type="date" class="form-control" id="endDateInput" name="endDateInput" required>
                                                </div>
                                                <input type="hidden" id="clientIdInput" name="clientId">
                                                <input type="hidden" id="caseIdInput" name="caseId">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" id="sendMail">Send</button>
                                            </div>
                                        </form>
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
    <script type="text/javascript" src="../assets/custom/feenote.js"></script>
    <script>
        $("#newTimerBtn").click(function(){
            $("#timeEntryModal").modal("show");
        })

        $(document).ready(function() {
            // Open date range modal when Generate PDF button is clicked
            $('#createPdf').on('click', function() {
                var clientId = $(this).data('client-id');
                var caseId = $(this).data('case-id');
                
                // Set the client and case IDs in the modal
                $('#dateRangeClientId').val(clientId);
                $('#dateRangeCaseId').val(caseId);
                
                // Reset date inputs
                $('#startDate').val('');
                $('#endDate').val('').prop('disabled', true);
                
                // Open the date range modal
                $('#dateRangeModal').modal('show');
            });

            // Add event listener for start date input
            $('#startDate').on('change', function() {
                var startDate = $(this).val();
                if (startDate) {
                    $('#endDate').prop('disabled', false).attr('min', startDate);
                } else {
                    $('#endDate').prop('disabled', true).val('');
                }
            });

            // Handle Generate PDF button click in the modal
            $('#createPdfBtn').on('click', function() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var clientId = $('#dateRangeClientId').val();
                var caseId = $('#dateRangeCaseId').val();

                if (!startDate || !endDate) {
                    sweetError('Please select both start and end dates.');
                    return;
                }

                // Close the modal
                $('#dateRangeModal').modal('hide');

                // Generate PDF with selected date range
                generatePdf(clientId, caseId, startDate, endDate);
            });

            // Function to generate PDF
            function generatePdf(clientId, caseId, startDate, endDate) {
                $.ajax({
                    url: 'cases/generateFeeNotePdf',
                    method: 'POST',
                    data: {
                        clientId: clientId,
                        caseId: caseId,
                        startDate: startDate,
                        endDate: endDate
                    },
                    beforeSend: function() {
                        $('#generatePdf').prop("disabled", true).html("Processing...");
                    },
                    success: function(response) {
                        if (response.success) {
                            sweetSuccess("PDF Created and Saved in Your Library");
                            window.open("cases/" + response.pdfUrl, '_blank');
                        } else {
                            sweetError('Failed to generate PDF');
                        }
                        $('#generatePdf').prop("disabled", false).html('<i class="bi bi-file-pdf"></i> Generate PDF');
                    },
                    error: function() {
                        sweetError('An error occurred while generating the PDF.');
                        $('#generatePdf').prop("disabled", false).html('<i class="bi bi-file-pdf"></i> Generate PDF');
                    },
                    dataType: 'json'
                });
            }

            
        });

        $(document).ready(function() {
            $(document).on('click', '.send-email-btn', function() {
                var clientId = $(this).data('client-id');
                var caseId = $(this).data('case-id');
                var clientEmail = $(this).data('client-email');
                $('#emailInput').val(clientEmail);
                $('#clientIdInput').val(clientId);
                $('#caseIdInput').val(caseId);
                $('#startDateInput').val('');
                $('#endDateInput').val('');
                $('#emailModal').modal('show');
            });

            $('#startDateInput').on('change', function() {
                $('#endDateInput').attr('min', $(this).val());
            });

            $('#sendEmailForm').submit( function(e) {
                e.preventDefault();
                var email = $('#emailInput').val();
                var clientId = $('#clientIdInput').val();
                var caseId = $('#caseIdInput').val();
                var startDate = $('#startDateInput').val();
                var endDate = $('#endDateInput').val();

                if (!email) {
                    sweetError('Please enter an email address.');
                    $('#emailModal').modal('hide');
                    return false;
                }

                if (!startDate || !endDate) {
                    sweetError('Please select both start and end dates.');
                    $('#emailModal').modal('hide');
                    return false;
                }

                if (new Date(startDate) > new Date(endDate)) {
                    sweetError('End date cannot be earlier than start date.');
                    $('#emailModal').modal('hide');
                    return false;
                }
                var sendEmailForm = $(this).serialize();
                $.ajax({
                    url: 'cases/sendFeeNoteEmail',
                    method: 'POST',
                    data: sendEmailForm,
                    beforeSend: function() {
                        $("#sendMail").prop("disabled", true).html("Processing...");
                    },
                    success: function(response) {
                        sweetSuccess(response);
                        $('#emailModal').modal('hide');
                        $("#sendMail").prop("disabled", false).html('Send as Email');
                    },
                    error: function() {
                        sweetError('An error occurred while sending the email.');
                        $("#sendMail").prop("disabled", false).html('Send as Email');
                    }
                });
            });
        });

        $(document).ready(function() {
            $(document).on('click', '.editTimerLog', function() {
                var timeEntryId = $(this).data('id');
                
                // Fetch time entry details using AJAX
                $.ajax({
                    url: 'cases/fetch_selected_time_entry',
                    type: 'POST',
                    data: { id: timeEntryId },
                    dataType: 'json',
                    success: function(data) {
                        // Populate the modal form with fetched data
                        $('#selectCase').val(data.caseId);
                        $('#date').val(data.dateCreated);
                        $('#time').val(data.timeCreated);
                        $('#hours').val(data.hours);
                        $('#minutes').val(data.minutes);
                        $('#selectCurrency').val(data.currency);
                        $('#hourlyRate').val(data.hourlyRate);
                        $('#cost').val(data.cost);
                        $('#description').val(data.description);
                        $('input[name="billableStatus"][value="' + data.billableStatus + '"]').prop('checked', true);
                        
                        // Set the timerId hidden input
                        $('#timerId').val(timeEntryId);
                        
                        // Open the modal
                        $('#timeEntryModal').modal('show');
                        
                        // Change the modal title and button text
                        $('#timeEntryModalLabel').text('Edit Time Entry');
                        $('#saveTimeEntry').text('Update Time Entry');
                    },
                    error: function() {
                        alert('Error fetching time entry data');
                    }
                });
            });
        });

        $(document).ready(function() {
            // Function to calculate cost based on time and rate
            function calculateCost2() {
                var hours = parseFloat($('#hours').val()) || 0;
                var minutes = parseFloat($('#minutes').val()) || 0;
                var hourlyRate = parseFloat($('#hourlyRate').val()) || 0;

                var totalHours = hours + (minutes / 60);
                var cost = totalHours * hourlyRate;

                // Round to two decimal places
                cost = Math.round(cost * 100) / 100;

                // Update the cost field
                $('#cost').val(cost.toFixed(2));
            }

            // Function to calculate hourly rate based on cost and time
            function calculateHourlyRate2() {
                var hours = parseFloat($('#hours').val()) || 0;
                var minutes = parseFloat($('#minutes').val()) || 0;
                var cost = parseFloat($('#cost').val()) || 0;

                var totalHours = hours + (minutes / 60);
                
                if (totalHours > 0) {
                    var hourlyRate = cost / totalHours;
                    
                    // Round to two decimal places
                    hourlyRate = Math.round(hourlyRate * 100) / 100;

                    // Update the hourly rate field
                    $('#hourlyRate').val(hourlyRate.toFixed(2));
                }
            }

            // Attach the calculation functions to relevant form fields
            $('#hours, #minutes, #hourlyRate').on('input', calculateCost2);
            $('#cost').on('input', calculateHourlyRate2);

            // Initial calculation when the modal is opened
            $('#timeEntryModal').on('shown.bs.modal', calculateCost);
        });
    </script>
</body>
</html>