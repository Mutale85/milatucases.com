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
	<title>Matter Status</title>
	<?php include '../addon_header.php';
		// $lawFirmId = $_SESSION['parent_id']; 
		if(isset($_GET['caseId'])){
			$caseId = $_GET['caseId'];
			$caseNo = $_GET['caseNo'];
			$clientId  = $_GET['clientId'];
		}
	?>
	<link rel="stylesheet" type="text/css" href="../assets/custom/caseDetails.css">
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
				      	<div class="col-md-12" id="Consolidation">
				      		<h5 class="text-primary">Matter Consolidation</h5>
				      		
				      			<?php 
				      				$query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND clientId = ? ");
				      				$query->execute([$caseId, $clientId]);
				      				$row = $query->fetch(PDO::FETCH_ASSOC);
				      				$causeId = $row['causeId'];
				      				$description = html_entity_decode(decrypt($row['caseDescription']));
				      				$title = decrypt($row['caseTitle']);
				      				if($causeId !== NULL){
				      					$cause = "Cause ID: $causeId |";
				      				}else{
				      					$cause = null;
				      				}
				      				$createdAt = date("D d, M, Y H:i A", strtotime($row['created_at']));
				      			?>
				      		<div class="card">
				      			<div class="card-header">
				      				<h5 class="card-title"><?php echo $cause ?> <?php echo "Matter / File No: $caseNo |"; ?> </h5>
				      			</div>
				      			<div class="card-body">
				      				<?php echo " <strong>Matter Title</strong><br> $title <br>Dated: $createdAt"?>
				      				<div class="border border-top mt-3 mb-3"></div>
				      				<?php echo " <h4 class='text-dark'>Matter Description</h4><br> $description "?>
				      				<div class="border border-top mt-3 mb-3"></div>
				      				<h5 class="text-primary">Status</h5>
				      				<?php 
				      					$queryMilestones = $connect->prepare("SELECT * FROM case_milestones WHERE caseId = ?  ORDER BY created_at DESC ");
				      					$queryMilestones->execute([$caseId]);
				      					if($queryMilestones->rowCount() > 0){
				      						foreach($queryMilestones->fetchAll() as $row){?>

				      							<div class="card-body">
				      								<h5>Title: <?php echo html_entity_decode(decrypt($row['milestoneTitle']))?></h5>
				      								<p><?php echo html_entity_decode(decrypt($row['milestoneDescription']))?></p>
				      								<p><em>Added By: <?php echo fetchLawFirmUserName($userId, $lawFirmId)?></em></p>
				      								<em><i class="bi bi-calendar"></i> <?php echo date("D d M, Y", strtotime($row['created_at'])) ?> - <i class="bi bi-clock-history"></i> <?php echo time_ago_check($row['created_at']) ?></em>
				      							</div>
				      				<?php	}
				      					}else{	
				      						echo "";
				      					}
				      				?>
				      				<div class="border border-top mt-3 mb-3"></div>
				      				<h5>Attached Documents</h5>
				      				<?php 
				      					$queryFiles = $connect->prepare("SELECT * FROM caseDocuments WHERE caseId = ?");
				      					$queryFiles->execute([$caseId]);
				      					if($queryFiles->rowCount() > 0){
				      						?>
				      						<table class="table table-hover">
			      								<tr>
			      									<th>Document</th>
			      									<th>Added By</th>
			      									<th>Date</th>
			      								</tr>
				      							
				      						<?php
				      						foreach($queryFiles->fetchAll() as $row){
				      							$userId = $row['userId'];
				      						?>
				      							<tr>
			      									<td>
				      									<a href="caseDocuments/<?php echo $row['documentName']?>" target="_blank"><?php echo html_entity_decode($row['documentName'])?></a>
				      								</td>
				      								<td><em><?php echo fetchLawFirmUserName($userId, $lawFirmId)?></em></td>
				      								<td>
				      									<em><i class="bi bi-calendar"></i> <?php echo date("D d M, Y", strtotime($row['date_added'])) ?> - <i class="bi bi-clock-history"></i> <?php echo time_ago_check($row['date_added']) ?></em>
				      								</td>
				      							</tr>
				      				<?php	} ?>
				      						</table>
				      				<?php

				      					}else{	
				      						echo "";
				      					}
				      				?>

				      				<!-- <div class="border border-top mt-3 mb-3"></div> -->
				      				<div class="mt-3">
					      				<h5 class="text-dark">Status</h5>
					      				<?php 
			      							$queryStatus = $connect->prepare("SELECT DISTINCT(case_status), userId, date_added FROM case_status WHERE caseId = ? ORDER BY date_added ASC");
											$queryStatus->execute([$caseId]);
											if($queryStatus->rowCount() > 0){
											    $prevStatus = null;
											    $rows = $queryStatus->fetchAll();
											    foreach($rows as $row){
											      	$userId = $row['userId'];
											      	$caseStatus = $row['case_status'];
											     	$dateAdded =  date("D d, M, Y H:i A", strtotime($row['date_added']));
											?>
											       <?php echo "<strong>$caseStatus</strong> <i>($dateAdded)</i> <i class='bi bi-arrow-right'></i>" ?>
											            
											<?php
											       
											    }
											} else {
											    echo "";
											}
										?>
									</div>
				      			</div>
				      			<div class="card-footer">
				      				<button class="btn btn-primary btn-sm" data-case-id="<?php echo $caseId?>" data-client-id="<?php echo $clientId?>"  id="generatePdf"><i class="bi bi-file-earmark-pdf"></i> Generate PDF</button>

				      				<button class="btn btn-dark btn-sm" data-caseid="<?php echo $caseId?>" id="sendEmail"><i class="bi bi-envelope"></i> Send Email</button>
				      			</div>
				      		</div>
				      	</div>
				    </div>
					</div>
					<?php include '../addon_footer.php';?>
					<!-- Email Modal -->
					<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
						    <div class="modal-content">
						      	<div class="modal-header">
						        	<h5 class="modal-title" id="emailModalLabel">Send Matter Status</h5>
						        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
						        	</button>
						      	</div>
						      	<form id="sendEmailForm">
						      		<div class="modal-body">
						        		<div class="mb-3">
							            	<label for="recipient-name" class="col-form-label">Select Milestone Period:</label>
							            	<div class="input-group">
							            		<input type="date" name="from" id="from" class="form-control" onchange="setEndDate()" required>
							            		<input type="date" name="end" id="end" class="form-control" required>
							            	</div>
							          	</div>
						          		<div class="form-group">
						            		<label for="emailInput">Email address</label>
						            		<input type="email" class="form-control" name="email" id="emailInput" required>
						          		</div>
						          		<input type="hidden" id="caseId" name="caseId" value="<?php echo $caseId?>">
						          		<input type="hidden" id="clientId" name="clientId" value="<?php echo $clientId?>">
							      	</div>
							      	<div class="modal-footer">
							        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							        	<button type="submit" class="btn btn-primary" id="sendEmailBtn">Send</button>
							      	</div>
							    </form>
						    </div>
						</div>
					</div>
					<!-- End of email modal -->
					<div class="content-backdrop fade"></div>
				</div>
				<!-- Generate PDF Modal -->
				<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
					<div class="modal-dialog">
					    <div class="modal-content">
					      	<div class="modal-header">
					        	<h5 class="modal-title" id="pdfModalLabel">Which Period</h5>
					        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      	</div>
					      	<form method="POST" id="createPdfForm">
					      		<div class="modal-body">
						          	<div class="mb-3">
						            	<label for="recipient-name" class="col-form-label">Select Milestone Period:</label>
						            	<div class="input-group">
						            		<input type="date" name="from" id="fromdate" class="form-control" onchange="setEndDate2()" required>
						            		<input type="date" name="end" id="todate" class="form-control" required>
						            	</div>
						          	</div>
						          	<div class="mb-3">
						          		<input type="hidden" name="caseId" value="<?php echo $caseId?>">
							        	<input type="hidden" name="clientId" value="<?php echo $clientId?>">
						          	</div>
							     </div>
					      		<div class="modal-footer">
					        		<button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
					        		<button type="submit" class="btn btn-primary" id="submitPdfBtn">Create PDF</button>
					      		</div>
						    </form>
					    </div>
					</div>
				</div>
				<!-- End of generate pdf modal -->
	    	</div>
	  	</div>
	  	<div class="layout-overlay layout-menu-toggle"></div>
	</div>
  <?php include '../addon_footer_links.php';?>
	<!-- <script type="text/javascript" src="../dist/controls/clientCases.js"></script> -->
	<script>
		$("#sendEmail").click(function(){
			$("#emailModal").modal("show");
		})

		$('#sendEmailForm').submit('click', function(e) {
			e.preventDefault();
	        var sendEmailForm = $(this).serialize();
	        $.ajax({
	            url: 'cases/sendMatterStatus',
	            method: 'POST',
	            data:sendEmailForm,
	            beforeSend:function(){
	                $("#sendEmailBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
	            },
	            success: function(response) {
	                sweetSuccess(response);
	                $('#emailModal').modal('hide');
	                $("#sendEmailBtn").prop("disabled", false).html('Send as Email');
	            },
	            error: function() {
	                sweetError('An error occurred while sending the email.');
	                $("#sendEmailBtn").prop("disabled", false).html('Send as Email');

	            }
	        });
	    })

	    $('#generatePdf').on('click', function() {
	        var clientId = $(this).data('client-id');
	        var caseId = $(this).data('case-id');
	        $("#pdfModal").modal("show");
	    })

	    $("#createPdfForm").submit(function(e){
	    	e.preventDefault();
	    	var createPdfForm = $(this).serialize();
	        $.ajax({
	            url: 'cases/generateMatterStatusPDF',
	            method: 'POST',
	            data: createPdfForm,
	            beforeSend:function(){
	                $("#submitPdfBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
	            },
	            success: function(response) {
	            	if (response.success) {
	                sweetSuccess("PDF REPORT CREATED");
	                $("#pdfModal").modal("hide");
	              	setTimeout(function(){
	              		window.open("cases/" + response.pdfUrl, '_blank');
	              	}, 2000)
	              }else{
	                sweetError(response);
	              }
	              $("#submitPdfBtn").prop("disabled", false).html('Create PDF');
	            },
	            error: function() {
	                sweetError('An error occurred while creating pdf.');
	                $("#submitPdfBtn").prop("disabled", false).html('Create PDF');

	            },
	            dataType:"json"
	        });
	    })
    	function setEndDate() {
			const fromDate = document.getElementById('from').value;
			const endDateInput = document.getElementById('end');
			const fromDateObj = new Date(fromDate);
			const endDateObj = new Date(fromDateObj.getTime() + (7 * 24 * 60 * 60 * 1000));
			const endDateStr = endDateObj.toISOString().slice(0, 10);
			endDateInput.value = endDateStr;
			endDateInput.min = fromDate;
		}
		function setEndDate2() {
			const fromDate = document.getElementById('fromdate').value;
			const endDateInput = document.getElementById('todate');
			const fromDateObj = new Date(fromDate);
			const endDateObj = new Date(fromDateObj.getTime() + (7 * 24 * 60 * 60 * 1000));
			const endDateStr = endDateObj.toISOString().slice(0, 10);
			endDateInput.value = endDateStr;
			endDateInput.min = fromDate;
		}
	</script>
</body>
</html>
