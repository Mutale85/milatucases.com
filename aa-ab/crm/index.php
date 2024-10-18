<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CRM Details</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		if(isset($_GET['clientId'])){
			$clientId = base64_decode($_GET['clientId']);
			$rowId = $_GET['clientId'];
		}
		// $geo_data = getGeoData();

		$stmt = $connect->prepare("SELECT id, caseNo, caseTitle FROM cases WHERE clientId = ? AND lawFirmId = ?");
		$stmt->execute([$clientId, $lawFirmId]);
		$caseResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

		
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
						<div class="row mb-5">
							<div class="col-md-4">
								<ul class="list-group">
									<li class="list-group-item">
										<a href="#" class="fetchCases" data-client-id="<?php echo $rowId?>" data-bs-target="#invoiceClientModal">Matter:  <?php echo fetchTotalCasesById($lawFirmId, $clientId);?></a>
									</li>
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#sendEmailModal">Send Message</a>
									</li>
									<li class="list-group-item">
										<a href="#" class="fetchInvoices" data-client-id="<?php echo $rowId?>" data-bs-target="#invoiceClientModal">Invoices: <?php echo fetchTotalInvoicesById($lawFirmId, $clientId)?></a>
									</li>
									
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#addNoteModal">KYC</a>
									</li>
									<li class="list-group-item">
										<a href="#" class="fetchDocuments" data-client-id="<?php echo $rowId?>" data-bs-target="#attachFileModal">Documents : <?php echo  fetchTotalCasesById($lawFirmId, $clientId);?></</a>
									</li>
									
									<li class="list-group-item">
										<a href="#" class="depositFunds" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>">Fund Deposits</a>
									</li>
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#disbursementModal"> Disbursements</a>
									</li>
									<li class="list-group-item">
										<a href="#" class="editCrmClient" data-id="<?php echo $clientId?>" data-bs-target="#clientModal">Edit Contact</a>
									</li>
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#deleteContactModal">Delete Contact</a>
									</li>
								</ul>
							</div>
							<div class="col-md-8">
								<div class="card">
								  	<div class="card-body">
								    	<h5 class="card-title"><?php echo getClientNameById($clientId, $lawFirmId) ?></h5>
								    	<div class="card-text">
								    		<?php echo fetchClientInfoByIdForCRM($clientId)?>
								    	</div>
								    	
								  	</div>
								</div>
							</div>
							<!-- 0977951727 -->
						</div>
						<div class="row">
							<div class="col-md-6 mb-3">
								<div class="card card-outline-success">
									<div class="card-header border-bottom">
										<h5 class="card-title">Deposited Funds</h5>
									</div>
									<div class="card-body">
								        <div class="table-responsive">
								            <table class="table datatable-class">
								                <thead>
								                    <tr>
								                        <th>Date</th>
								                        <th>Case</th>
								                        <th>Amount</th>
								                        <th>Description</th>
								                    </tr>
								                </thead>
								                <tbody id="displayDepositedAmount">
								                    <?php fetchDeposits($clientId);?>
								                </tbody>
								            </table>
								        </div>
									</div>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<div class="card">
									<div class="card-header border-bottom">
										<h5 class="card-title">Disbursed Funds</h5>
									</div>
									<div class="card-body">
								        <div class="table-responsive">
								            <table class="table datatable-class">
								                <thead>
								                    <tr>
								                        <th>Date</th>
								                        <th>Case</th>
								                        <th>Amount</th>
								                        <th>Description</th>
								                    </tr>
								                </thead>
								                <tbody id="displayDisbursedAmount">
								                    <?php fetchDisbursements($clientId);?>
								                </tbody>
								            </table>
								        </div>
									</div>
								</div>
								
							</div>
							<div class="col-md-12 mb-3">Other Contacts</div>
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
    <script type="text/javascript" src="../assets/custom/disbursements.js"></script>
	
	<!-- Send Email Modal -->
	<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="sendEmailModalLabel">Send Message to <?php echo getClientNameById($clientId, $lawFirmId) ?></h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body">
	                <form>
	                    <div class="mb-3">
	                        <label for="msgTitle" class="form-label">Title</label>
	                        <input type="text" class="form-control" name="msgTitle" id="msgTitle" required>
	                        <input type="hidden" name="from" id="from" value="<?php echo $lawFirmId?>">
	                        <input type="hidden" name="to" id="to" value="<?php echo $clientId?>">
	                    </div>
	                    <div class="mb-3">
	                        <label for="message" class="form-label">Message</label>
	                        <textarea class="form-control" name="message" id="message" rows="5"></textarea>
	                    </div>
	                    <button type="submit" class="btn btn-primary">Send</button>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Invoice Client Modal -->
	<div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="resultsModalLabel"> <?php echo getClientNameById($clientId, $lawFirmId) ?>'s Data </h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body">
	                <p id="resultsDiv">Client Data Will Appear Here.</p>
	            </div>
	        </div>
	    </div>
	</div>
	<!-- Disburments Modal -->
	<div class="modal fade" id="disbursementModal" tabindex="-1" role="dialog" aria-labelledby="disbursementModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		    <div class="modal-content">
		      	<div class="modal-header">
		        	<h5 class="modal-title" id="disbursementModalLabel">Create Disbursement</h5>
		        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
		        	</button>
		      	</div>
		      	<div class="modal-body">
				    <form id="disbursedFundsForm" method="POST">
				    	<div id="printableDiv">
					        <div class="disbursement-body">
					            <div class="row">
					                <div class="form-group mb-3">
					                	<label class="form-label mb-1" for="client">Case Title:</label>
					                    <select class="form-select" id="caseId" name="caseId" required>
					                    	<option value="">Select Matter</option>
					                        <?php
						                        $matters = fetchClientCRMMatters($clientId, $lawFirmId);
						                        foreach ($matters as $row) {
						                        	$matterTitle = decrypt($row['caseTitle']);
						                        	$id = $row['id'];
						                            echo "<option value='{$id}'>{$matterTitle}</option>";
						                        }
					                        ?>
					                    </select>
					                </div>
					                <div class="form-group mb-3">
					                	<label class="form-label mb-1" for="date_disbursed">Date:</label>
					                	<input type="date" name="date_disbursed" id="date_disbursed" class="form-control" required>
					                </div>
					                <div class="form-group mb-3">
					                	<label class="form-label mb-1" for="currency">Amount:</label>
						                <div class="input-group ">
							                <select id="currency" name="currency" class="form-select" required>
					                            <option value="">Choose Currency</option>
					                            <option value="<?php echo $currency ?>"><?php echo $country?> (<?php echo $currency?>)</option>
					                            <option value="USD">US Dollar (USD)</option>
					                            <option value="GBP">British Pound (GBP)</option>
					                            <option value="ZAR">South African Rand (ZAR)</option>
					                            <option value="EUR">Euro (EUR)</option>
					                        </select>
					                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required placeholder="Enter Amount">
							            </div>
						            	<input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
				                        <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
				                        <input type="hidden" name="userId" id="userId" value="<?php echo $userId?>">
						        	</div>
						            <div class="form-group mb-3">
						                <label class="form-label mb-1" for="description">Description:</label>
						            	<textarea name="description" id="description" class="form-control" placeholder="Enter Disbursement Purpose"></textarea>
						            </div>
						            <div class="form-check mb-3">
								        <input class="form-check-input" type="checkbox" id="postToExpense" name="postToExpense" checked>
								        <label class="form-check-label" for="postToExpense">
								            Post to Expense Table
								        </label>
								    </div>
					            </div> 
					        </div>
					    </div>
				        
				        <button type="submit" class="btn btn-primary" id="submitBtn"> <i class="bi bi-receipt-cutoff"></i> Submit Disbursement</button>
				    </form>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		      	</div>
		    </div>
		</div>
	</div>
	
	<!-- Documents Modal -->
	<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
	    <div class="modal-dialog modal-lg" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="documentModalLabel">Documents</h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body">
	                <div id="showDocuments"></div>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Edit Contact Modal -->
	<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="clientModalLabel">Client Initial Information Form</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <?php include '../cc/addclientmodal.php';?>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- Delete Contact Modal -->
	<div class="modal fade" id="deleteContactModal" tabindex="-1" aria-labelledby="deleteContactModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="deleteContactModalLabel">Delete Contact</h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body">
	                <p>Are you sure you want to delete this contact? This action cannot be undone.</p>
	                <button type="button" class="btn btn-danger">Delete</button>
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Deposit Funds Modal -->
	<div class="modal fade" id="depositFundsModal" tabindex="-1" aria-labelledby="depositFundsModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="depositFundsModalLabel">Add Deposited Funds</h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <div class="modal-body">
	                <form id="depositedFundsForm" method="post">
	                	<div class="form-group mb-3">
			                <label class="form-label mb-1" for="case_id">Case Title:</label>
			                <select class="form-control" id="case_id" name="case_id" required>
			                    <option value="">Select a case</option>
			                    <?php foreach ($caseResults as $case): ?>
			                        <option value="<?php echo htmlspecialchars($case['id']); ?>">
			                            <?php echo html_entity_decode(decrypt($case['caseTitle'])); ?>
			                        </option>
			                    <?php endforeach; ?>
			                </select>
			            </div>
			            <div class="form-group mb-3">
			                <label class="form-label mb-1" for="currency">Currency:</label>
			                <select id="currency" name="currency" class="form-select" required>
	                            <option value="">Choose...</option>
	                            <option value="<?php echo $currency ?>"><?php echo $country?> (<?php echo $currency?>)</option>
	                            <option value="USD">US Dollar (USD)</option>
	                            <option value="GBP">British Pound (GBP)</option>
	                            <option value="ZAR">South African Rand (ZAR)</option>
	                            <option value="EUR">Euro (EUR)</option>
	                        </select>
	                        <input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
	                        <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
	                        <input type="hidden" name="userId" id="userId" value="<?php echo $userId?>">
			            </div>
			            <div class="form-group mb-3">
			                <label class="form-label mb-1" for="amount">Amount:</label>
			                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
			            </div>
			            <div class="form-group mb-3">
			                <label class="form-label mb-1" for="date_deposited">Date Deposited:</label>
			                <input type="date" class="form-control" id="date_deposited" name="date_deposited" required>
			            </div>
			            <div class="form-group mb-3">
			                <label class="form-label mb-1" for="description">Description:</label>
			            	<textarea name="description" id="description" class="form-control" placeholder="Enter Amount Purpose"></textarea>
			            </div>
			            <div class="form-check mb-3">
					        <input class="form-check-input" type="checkbox" id="postToIncome" name="postToIncome" checked>
					        <label class="form-check-label" for="postToIncome">
					            Post to Income Table
					        </label>
					    </div>
			            <button type="submit" class="btn btn-primary" id="submitDeposit">Submit Deposit</button>
			        </form>
	            </div>
	        </div>
	    </div>
	</div>
	<script type="text/javascript" src="../assets/custom/library.js"></script>
	<script>
    	$(document).on("click", ".fetchCases", function(e){
    		e.preventDefault();
    		var clientId = $(this).data("client-id");
    		// alert(clientId);
    		$.ajax({
    			url:"crm/fetchCaseData",
    			method:"POST",
    			data:{clientId:clientId},
    			success:function(response){
    				$("#resultsModal").modal("show");
    				$("#resultsDiv").html(response);
    			}
    		})
    	})

    	$(document).on("click", ".fetchInvoices", function(e){
    		e.preventDefault();
    		var clientId = $(this).data("client-id");
    		// alert(clientId);
    		$.ajax({
    			url:"crm/fetchInvoiceData",
    			method:"POST",
    			data:{clientId:clientId},
    			success:function(response){
    				$("#resultsModal").modal("show");
    				$("#resultsDiv").html(response);
    			}
    		})
    	})

    	$(document).on("click", ".fetchDocuments", function(e){
    		e.preventDefault();
    		var clientId = $(this).data("client-id");
    		// alert(clientId);
    		$.ajax({
    			url:"crm/fetchDocumentsData",
    			method:"POST",
    			data:{clientId:clientId},
    			success:function(response){
    				$("#resultsModal").modal("show");
    				$("#resultsDiv").html(response);
    			}
    		})
    	})

    	$(document).on("click", '.editCrmClient', function(e){
		    e.preventDefault();
		    var clientId = $(this).data('id');
		    $.ajax({
		        url: 'crm/fetchClientData',
		        type: 'POST',
		        data: { id: clientId },
		        dataType: 'JSON',
		        success: function(data) {
		            // Populate the form fields with the fetched data
		            

		            // Check client type and display fields accordingly
		            if (data.client_type === 'Corporate') {
						$('#business_entity_name').val(data.business_name);
						$('#incorporation_number').val(data.incorporation_number);
						$('#business_tpin').val(data.client_tpin);
						$('#representativeName').val(data.client_names);
						$('#representative_email').val(data.client_email);
						$('#representative_phone').val(data.client_phone);
						$('#business_address').val(data.address);
						$('#corporateFields').show();
						$('#individualFields').hide();
						$('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', true);
						$('#client_names, #client_phone, #nrc_passport_number, #client_address').prop('required', false);
		                
		            } else {
						$('#client_names').val(data.client_names);
						$('#nrc_passport_number').val(data.nrc_passport_number);
						$('#client_phone').val(data.client_phone);
						$('#client_email').val(data.client_email);
						$('#clientTpin').val(data.client_tpin);
						$('#client_address').val(data.address);
						$('#corporateFields').hide();
						$('#individualFields').show();
						$('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', false);
						$('#client_names, #client_phone, #client_address, #nrc_passport_number').prop('required', true);

		            }
		            $('#client_id').val(data.id);

		            $("#clientType").val(data.client_type).prop("readonly", true);

		            $('#clientModal').modal('show');
		        },
		        error: function(xhr, status, error) {
		            console.error('Error fetching client data:', error);
		        }
		    });
		});

		$(document).ready(function() {
		    $('#clientsForm').submit(function(e) {
		        e.preventDefault();
		        var formData = $(this).serialize();
		        $.ajax({
		            type: 'POST',
		            url: 'cc/createInitialClient',
		            data: formData,
		            beforeSend: function() {
		                $('#saveClient').prop("disabled", true).html(`Processing ... <div class="spinner-border text-primary" role="status">
		            	<span class="visually-hidden">Loading...</span>
		          		</div>`);
		            },
		            success: function(response) {
		                $('#saveClient').prop("disabled", false).html("Save Client");
		                $('#clientModal').modal('hide');
		                sweetSuccess(response);
		                $('#clientsForm')[0].reset();
		                location.reload();
		            },
		            error: function(xhr, status, error) {
		                $('#saveClient').prop("disabled", false).html("Save Client");
		                sweetError(error);
		            }
		        });
		    });
		});

		$(document).on('click', '.depositFunds', function(){
			$("#depositFundsModal").modal("show");
			fetchCurrencyData();
		})

		$(document).ready(function() {
			var clientId = document.getElementById('clientId').value;
		    $('#depositedFundsForm').on('submit', function(e) {
		        e.preventDefault();
		        
		        $.ajax({
		            url: 'crm/processDeposit',
		            type: 'POST',
		            data: $(this).serialize(),
		            dataType: 'json',
		            beforeSend:function(){
		            	$("#submitDeposit").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
		            },
		            success: function(response) {
		                if (response.status === 'success') {
		                    sweetSuccess('Funds deposited successfully!');
		                    $('#depositFundsModal').modal('hide');
		                    displayDepositedAmount(clientId);
		                } else {
		                    sweetError('Error: ' + response.message);
		                }
		                $("#submitDeposit").prop("disabled", false).html("Submit Deposit");
		            },
		            error: function() {
		                sweetError('An error occurred. Please try again.');
		                $("#submitDeposit").prop("disabled", false).html("Submit Deposit");
		            }
		        });
		    });

		    function displayDepositedAmount(clientId){
		    	$.ajax({
		            url: 'crm/fetchDeposit',
		            type: 'POST',
		            data: {clientId:clientId},
		            
		            success: function(response) {
		             	$("#displayDepositedAmount").html(response);  
		            }
		        });
		        $('#depositedFundsForm')[0].reset();
		    }
		    displayDepositedAmount(clientId);
		});


		$(document).ready(function() {
			var clientId = document.getElementById('clientId').value;
		    $('#disbursedFundsForm').on('submit', function(e) {
		        e.preventDefault();
		        
		        $.ajax({
		            url: 'crm/processDebursement',
		            type: 'POST',
		            data: $(this).serialize(),
		            dataType: 'json',
		            beforeSend:function(){
		            	$("#submitBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
		            },
		            success: function(response) {
		                if (response.status === 'success') {
		                    sweetSuccess('Funds disbursed successfully!');
		                    $('#depositFundsModal').modal('hide');
		                    displayDisbursedAmount(clientId);
		                } else {
		                    sweetError('Error: ' + response.message);
		                }
		                $("#submitBtn").prop("disabled", false).html("Submit Disbursement");
		            },
		            error: function() {
		                sweetError('An error occurred. Please try again.');
		                $("#submitBtn").prop("disabled", false).html("Submit Disbursement");
		            }
		        });
		    });

		    function displayDisbursedAmount(clientId){
		    	$.ajax({
		            url: 'crm/fetchDisbursements',
		            type: 'POST',
		            data: {clientId:clientId},
		            
		            success: function(response) {
		             	$("#displayDisbursedAmount").html(response);
		            }
		        });
		        $('#disbursedFundsForm')[0].reset();
		    }
		    displayDisbursedAmount(clientId);
		});

		

    </script>
</body>
</html>