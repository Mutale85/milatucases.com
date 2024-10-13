<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName']?> Disbursements</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		if (isset($_GET['details'])) {
			$disbursement_id = $_GET['details'];
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
          								<button class="btn btn-primary btn-sm" id="showDisModal"><i class="bi bi-receipt-cutoff"></i> Add Disbursement</button>
          							</div>
          							<div class="card-body">
          								<div id=""></div>

          								<table class="table" id="allTables">
                                            <thead>
                                                <tr>
                                                    <th>Client</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Financial Officer'){?>
                                                    <th>Action</th>
                                                    <?php }?>
                                                </tr>
                                            </thead>
                                            <tbody id="showDisbursements">
                                                <?php echo fetchLawFirmDisbursements($_SESSION['parent_id']) ?>
                                            </tbody>
                                            <tfoot id="showTotalDisbursements">
                                                
                                            </tfoot>
                                        </table>
          							</div>
          							<div class="card-footer">
          								<div class="modal fade" id="disbursementModal" tabindex="-1" role="dialog" aria-labelledby="disbursementModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-xl" role="document">
											    <div class="modal-content">
											      	<div class="modal-header">
											        	<h5 class="modal-title" id="disbursementModalLabel">Send Matter Status</h5>
											        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
											        	</button>
											      	</div>
											      	<div class="modal-body">
											        	<form id="disbursementForm" method="POST">
													    	<div id="printableDiv">
														        
														        <div class="disbursement-body">
														            <div class="row">
														                <?php echo fetchAndDisplayCompanyInfo() ?>
														                <div class="col-md-6">
														                    <h5>Disbursement For</h5>
														                    <select class="form-select" id="client" name="clientId" required>
														                    	<option value="">Select Client</option>
														                        <?php
															                        $clients = fetchClientContacts($_SESSION['parent_id']);
															                        foreach ($clients as $client) {
															                        	if($client['type'] == 'Individual'){
															                        		$clientName = decrypt($client['name']);
															                        	}else{
															                        		$clientName = $client['name'];
															                        	}
															                        	$clientTpin = decrypt($client['tpin']);
															                        	$clientEmail = decrypt($client['email']);
															                        	$id = $client['id'];
															                            echo "<option value='{$id}' data-email='{$clientEmail}' data-tpin='{$clientTpin}'>{$clientName} ({$client['type']})</option>";
															                        }
														                        ?>
														                    </select>

														                    <div class="mt-3">
														                    	<input type="hidden" name="clientEmail" id="clientEmail">
														                    	<input type="hidden" name="client_tpin" id="client_tpin">
														                    </div>
														                </div>
														                <div class="col-md-6">
														                	<h5>Disbursement Date</h5>
														                	<input type="date" name="disbursement_date" id="disbursement_date" class="form-control" required>
														                </div>
														            </div>
														            
														            <!-- Add your items and total calculation here -->
														            <div class="row mt-4">
																        <div class="col-12">
																            <h4>Disbursement Items:</h4>
																            <div class="table table-responsive">
																	            <table class="table table-bordered" id="disbursementItemsTable">
																	                <thead>
																	                    <tr>
																	                        <th>Description</th>
																	                        <th>Quantity</th>
																	                        <th>Unit Price</th>
																	                        <th>Total</th>
																	                        <th id="action">Action</th>
																	                    </tr>
																	                </thead>
																	                <tbody>
																	                    <tr>
																	                        <td><input type="text" class="form-control item-description" name="items[0][description]" required></td>
																	                        <td><input type="number" class="form-control item-quantity" name="items[0][quantity]" required></td>
																	                        <td><input type="number" class="form-control item-price" name="items[0][price]" step="0.01" required></td>
																	                        <td><input type="text" class="form-control item-total" name="items[0][total]" readonly></td>
																	                        <td id="removeBtn"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="bi bi-x-circle"></i></button></td>
																	                    </tr>
																	                </tbody>
																	            </table>
																	        </div>
																            <button type="button" class="btn btn-dark btn-sm" id="addItem"><i class="bi bi-node-plus"></i> Add Item</button>
																        </div>
																    </div>
																    <!-- Total Calculation Section -->
																     <div class="row mt-4">
																        <div class="col-md-6 offset-md-6">
																            <div class="mb-3">
																                
																                <input type="hidden" name="disbursementTotal" id="disbursementTotalHidden">
																                <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
																            </div>
																            <h5>Total: <span id="disbursementTotal">0.00</span></h5>
																        </div>
																    </div>
																    
														        </div>
														    </div>
													        <div class="row mt-3 mb-4" style="display:none;">
															    <div class="col-12">
															        <div class="form-check">
															            <input class="form-check-input" type="checkbox" id="emaildisbursement" name="email_disbursement">
															            <label class="form-check-label" for="emaildisbursement">
															                Email Disbursment to Client
															            </label>
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
										<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
										    <div class="modal-dialog" role="document">
										        <div class="modal-content">
										            <div class="modal-header">
										                <h5 class="modal-title" id="shareModalLabel">Share PDF</h5>
										                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
										                    <span aria-hidden="true"></span>
										                </button>
										            </div>
										            <div class="modal-body">
										                <form id="shareForm">
										                    <div class="form-group">
										                        <label for="emailInput">Email Address</label>
										                        <input type="email" class="form-control" id="emailInput" placeholder="Enter email address">
										                    </div>
										                </form>
										            </div>
										            <div class="modal-footer">
										                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										                <button type="button" class="btn btn-primary" id="sendEmailBtn">Send Email</button>
										            </div>
										        </div>
										    </div>
										</div>

										<!-- disbursement details modal -->
										<div class="modal fade" id="disbursementDetails" tabindex="-1" role="dialog" aria-labelledby="disbursementModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-xl" role="document">
											    <div class="modal-content">
											      	<div class="modal-header">
											        	<h5 class="modal-title" id="disbursementModalLabel">Disbursement Details</h5>
											        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
											        	</button>
											      	</div>
											      	<div class="modal-body">
											      		<div id="details" class="table table-responsive">
														    <table class="table table-striped">
														        <thead>
														            <tr>
														                <th>Description</th>
														                <th>Quantity</th>
														                <th>Price</th>
														                <th>Total</th>
														                <th>Date</th>
														            </tr>
														        </thead>
														        <tbody>
														            <?php
														            $query = $connect->prepare("SELECT `id`, `disbursement_id`, `description`, `quantity`, `price`, `total`, `created_at` FROM `disbursement_items` WHERE `disbursement_id` = ?");
														            $query->execute([$disbursement_id]);
														            $items = $query->fetchAll(PDO::FETCH_ASSOC);

														            $totalSpent = 0;
														            foreach ($items as $item) {
														                $totalSpent += $item['total'];
														            ?>
														            <tr>
														                <td><?php echo htmlspecialchars($item['description']); ?></td>
														                <td><?php echo $item['quantity']; ?></td>
														                <td><?php echo $item['price']; ?></td>
														                <td><?php echo $item['total']; ?></td>
														                <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
														                
														            </tr>
														            <?php
														            }
														            ?>
														        </tbody>
														        <tfoot>
														            <tr>
														                <td colspan="4">Total Spent:</td>
														                <td><?php echo number_format($totalSpent, 2); ?></td>
														                <td></td>
														            </tr>
														        </tfoot>
														    </table>
														</div>
											      	</div>
											      	<div class="modal-footer">
											        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
											      	</div>
											    </div>
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
    <script type="text/javascript" src="../assets/custom/disbursements.js"></script>
    <?php
		if (isset($_GET['details']) && !empty($_GET['details'])) {
		?>
		<script>
		    $(document).ready(function() {
		        $("#disbursementDetails").modal("show");
		    });
		</script>
		<?php
		}
	?>
    <script>
    	$(function(){
    		$("#showDisModal").click(function(){
    			$("#disbursementModal").modal("show");
    		});
    		var lawFirmId = '<?php echo $lawFirmId?>';
    		$.ajax({
    			url:"finance/fetchDisbursements",
    			method:"POST",
    			data: {lawFirmId:lawFirmId},
    			success:function(data){
    				$("#showDisbursement").html(data);
    			}
    		})

    	})
    </script>

    <script>
	    $(document).ready(function() {
	        // Add click event listener to the share button
	        $('.share-btn').click(function() {
	            var disbursementId = $(this).data('id');
	            var clientId = $(this).data('client-id');
	            $('#shareModal').modal('show');

	            // Set up the email sending logic
	            $('#sendEmailBtn').click(function() {
	                var emailAddress = $('#emailInput').val();
	                // Call a server-side script to send the email with the PDF attachment
	                $.ajax({
	                    url: 'finance/sendDisbursementAsEmail',
	                    type: 'POST',
	                    data: {
	                        emailAddress: emailAddress,
	                        disbursementId: disbursementId,
	                        clientId:clientId
	                    },
	                    beforeSend:function(){
	                    	$("#sendEmailBtn").prop("disabled", true).html("<i class='bi bi-hourglass'></i> Processing... ");
	                    },
	                    success: function(response) {
	                        sweetSuccess(response);
	                        $('#shareModal').modal('hide');
	                        $("#sendEmailBtn").prop("disabled", false).html("Send Email");
	                        setTimeout(function(){
	                        	location.reload();
	                        }, 2000);
	                    },
	                    error: function(xhr, status, error) {
	                        sweetError('Error sending email: ' + error);
	                        $("#sendEmailBtn").prop("disabled", false).html("Send Email");
	                    }
	                });
	            });
	        });
	    });
	</script>
</body>
</html>