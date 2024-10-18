<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-semi-dark"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter"><head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Fee Notes For Clients</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		if (isset($_GET['caseNo'])) {
			$caseId = $_GET['caseId'];
			$caseNo = base64_decode($_GET['caseNo']);
			$client_tpin = decrypt($_GET['clientId']);
			$clientId = fetchCLientIdCaseId($caseId);
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
				                <div class="card">
				                	<div class="card-header">
					                   	<div class="row align-items-center">
								                <div class="col-md-6">
								                    <?php echo fetchLogoByLawFirmId($lawFirmId)?>
								                </div>
								                <div class="col-md-6 text-end">
								                    <h4>FEE NOTE</h4>
								                </div>
								            	</div>
									            <div class="row">
									            	<?php echo fetchAndDisplayCompanyInfo() ?>
									            </div>
								        	</div>
				                  <div class="card-body">
				                      <?php echo fetchClientInfoById($clientId)?>
					                    <strong>Date: <?php echo date('d F Y')?></strong>&nbsp;&nbsp;&nbsp;&nbsp;
					                    <strong>Cause No: <?php echo $caseNo?></strong></p>
				                        <div class="table table-responsive">
					                        <table class="table table-striped">
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
																            $query = $connect->prepare("SELECT * FROM `timer_logs` WHERE case_id = ? AND lawFirmId = ?");
																            $query->execute([$caseId, $lawFirmId]);
																            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
																                $date = date('d.m.y', strtotime($row['created_at']));
																                $taskDescription = htmlspecialchars($row['task_description']);
																                $timeSpent = $row['time_spent'] . ' min';
																                $hourlyRate = number_format($row['hourly_rate'], 2);
																                $totalCharge = number_format($row['total_amount'], 2);                                     
																                $totalAmount += $row['total_amount'];
																        ?>
																        <tr data-id="<?php echo $row['id']; ?>">
																            <td><?php echo $date; ?></td>
																            <td><?php echo $taskDescription; ?></td>
																            <td><?php echo $timeSpent; ?></td>
																            <td><?php echo $hourlyRate; ?></td>
																            <td><?php echo $totalCharge; ?></td>
																            <td>
																            	<div class="btn-group">
																	                <button class="btn btn-sm btn-primary editLog" data-id="<?php echo $row['id']; ?>">Edit</button>
																	                <button class="btn btn-sm btn-danger deleteLog" data-id="<?php echo $row['id']; ?>">Delete</button>
																	            </div>
																            </td>
																        </tr>
																        <?php } ?>
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
															    class="btn btn-primary btn-sm send-email-btn">
															    <i class="bi bi-receipt-cutoff"></i>
															    Send as Email
															</button>
									            <button id="generatePdf" class="btn btn-dark btn-sm" data-client-id="<?php echo $clientId?>" data-case-id=<?php echo $caseId?>><i class="bi bi-file-pdf"></i> Generate PDF</button>
				                    </div>
				                </div>
				                <!-- Edit Modal -->
				                <div class="modal fade" id="timerModal" tabindex="-1" aria-labelledby="timerModalLabel" aria-hidden="true">
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
										</div>
								<!-- End of timer edits -->
								<!-- Modal for sending email  -->
												<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
												  <div class="modal-dialog" role="document">
												    <div class="modal-content">
												      <div class="modal-header">
												        <h5 class="modal-title" id="emailModalLabel">Send Fee Note</h5>
												        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
												        </button>
												      </div>
												      <div class="modal-body">
												        <form id="sendEmailForm">
												          <div class="form-group">
												            <label for="emailInput">Email address</label>
												            <input type="email" class="form-control" id="emailInput" required>
												          </div>
												          <input type="hidden" id="clientIdInput">
												          <input type="hidden" id="caseIdInput">
												        </form>
												      </div>
												      <div class="modal-footer">
												        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
												        <button type="button" class="btn btn-primary" id="sendEmailBtn">Send</button>
												      </div>
												    </div>
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
</body>
</html>