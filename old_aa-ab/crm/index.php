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
							<div class="col-md-4">
								<ul class="list-group">
									<li class="list-group-item">
										<a href="#" class="fetchCases" data-client-id="<?php echo $rowId?>" data-bs-target="#invoiceClientModal">Matter:  <?php echo  fetchTotalCasesById($lawFirmId, $clientId);?></a>
									</li>
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#sendEmailModal">Send Message</a>
									</li>
									<li class="list-group-item">
										<a href="#" class="fetchInvoices" data-client-id="<?php echo $rowId?>" data-bs-target="#invoiceClientModal">Invoices: <?php echo fetchTotalInvoicesById($lawFirmId, $clientId)?></a>
									</li>
									<!-- <li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#businessProspectModal">Business Prospect</a>
									</li>
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#clientIssuesModal">Client Issues</a>
									</li> -->
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#addNoteModal">KYC</a>
									</li>
									<li class="list-group-item">
										<a href="#" class="fetchDocuments" data-client-id="<?php echo $rowId?>" data-bs-target="#attachFileModal">Documents : <?php echo  fetchTotalCasesById($lawFirmId, $clientId);?></</a>
									</li>
									<li class="list-group-item">
										<a href="#" class="editCrmClient" data-id="<?php echo $rowId?>" data-bs-target="#clientModal">Edit Contact</a>
									</li>
									<li class="list-group-item">
										<a href="#" data-bs-toggle="modal" data-client-id="<?php echo $rowId?>" data-bs-target="#deleteContactModal">Delete Contact</a>
									</li>
								</ul>
							</div>
							<div class="col-md-8">
								<div class="card">
								  	<!-- <img src="../banner.jpeg" class="card-img-top" alt="..."> -->
								  	<div class="card-body">
								    	<h5 class="card-title"><?php echo getClientNameById($clientId, $lawFirmId) ?></h5>
								    	<div class="card-text">
								    		<?php echo fetchClientInfoByIdForCRM($clientId)?>
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
		            $('#client_id').val(clientId);

		            // Check client type and display fields accordingly
		            if (data.client_type === 'Corporate') {
		                $('#business_entity_name').val(data.business_name);
		                $('#business_tpin').val(data.client_tpin);
		                $('#representativeName').val(data.client_names);
		                $('#representative_email').val(data.client_email);
		                $('#representative_phone').val(data.client_phone);
		                $('#corporateFields').show();
		                $('#individualFields').hide();
		                $('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', true);
		                $('#client_names, #client_phone, #client_email, #clientTpin').prop('required', false);
		            } else {
		                $('#client_names').val(data.client_names);
		                $('#client_phone').val(data.client_phone);
		                $('#client_email').val(data.client_email);
		                $('#clientTpin').val(data.client_tpin);
		                $('#corporateFields').hide();
		                $('#individualFields').show();
		                $('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', false);
		                $('#client_names, #client_phone, #client_email, #clientTpin').prop('required', true);
		            }

		            $("#clientType").val(data.client_type).prop("readonly", true);

		            $('#clientModal').modal('show');
		        },
		        error: function(xhr, status, error) {
		            console.error('Error fetching client data:', error);
		        }
		    });
		});
    </script>
	
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
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="clientModalLabel">Client Initial Information Form</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        <form id="clientsForm">
	          <div class="mb-3">
	            	<label for="clientType" class="form-label">Client Type</label>
					<select class="form-select" id="clientType" name="client_type" readonly>
						<option value="Corporate">Corporate</option>
					</select>
	          </div>
	          <div id="corporateFields" style="display: none;">
	            <div class="mb-3">
	              	<label for="business_entity_name" class="form-label">Business/Entity Name</label>
	              	<input type="text" class="form-control" id="business_entity_name" name="business_entity_name">
	            </div>
	            <div class="mb-3">
	              	<label for="business_tpin" class="form-label">Business TPIN Number</label>
	              	<input type="text" class="form-control" id="business_tpin" name="business_tpin">
	            </div>
	            <div class="mb-3">
			        <label for="representative_name" class="form-label">Representative Names</label>
			        <input type="text" class="form-control" id="representativeName" name="representative_name" required>
			    </div>
	            <div class="mb-3">
			        <label for="representative_email" class="form-label">Representative Email</label>
			        <input type="email" class="form-control" id="representative_email" name="representative_email" required>
			    </div>
			    <div class="mb-3">
			        <label for="representative_phone" class="form-label">Representative Phone</label>
			        <input type="phone" class="form-control" id="representative_phone" name="representative_phone" required value="260">
			    </div>
	            <div class="mb-3 form-check">
	              <input type="checkbox" class="form-check-input" id="allow_login" name="allow_login">
	              <label class="form-check-label" for="allow_login">Allow client to log in</label>
	            </div>
	          </div>
	          <div id="individualFields" style="display: none;">
		          <div class="mb-3">
		            <label for="client_name" class="form-label">Client Name</label>
		            <input type="text" class="form-control" id="client_names" name="client_names" required>
		          </div>
		          <div class="mb-3">
	            	<label for="client_phone" class="form-label">Client Phone</label>
	            	<input type="text" class="form-control" id="client_phone" name="client_phone" required value="260">
	          		</div>
	          		<div class="mb-3">
		            	<label for="client_email" class="form-label">Client Email</label>
		            	<input type="email" class="form-control" id="client_email" name="client_email" placeholder="email@example.com" required>
		          	</div>
			          <div class="mb-3">
			            <label for="client_tpin" class="form-label">Individual TPIN Number</label>
			            <input type="text" class="form-control" id="clientTpin" name="client_tpin" required>
			          </div>
			    </div>
	          	<input type="hidden" class="form-control" id="lawFirmId" name="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
	          	<input type="hidden" id="client_id" name="client_id">
	          	<button type="submit" class="btn btn-primary" id="saveClient">Save Client</button>
	        </form>
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
	<script type="text/javascript" src="../assets/custom/library.js"></script>
</body>
</html>