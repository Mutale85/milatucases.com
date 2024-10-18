<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add New Clients</title>
	<?php include '../addon_header.php'; ?>
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
	          							<h5 class="card-title">Add New Client</h5>
	          						</div>
	          						<div class="card-body">
	          							<form id="clientsForm">
								          	<div class="mb-3">
								            	<label for="clientType" class="form-label">Client Type</label>
												<select class="form-select" id="clientType" name="client_type" required>
													<option value="">Select client type</option>
													<option value="Corporate">Corporate</option>
													<option value="Individual">Individual</option>
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
									            	<input type="email" class="form-control" id="client_email" name="client_email" placeholder="email@example.com">
									          	</div>
										        <div class="mb-3">
										            <label for="client_tpin" class="form-label">Individual TPIN Number</label>
										            <input type="text" class="form-control" id="clientTpin" name="client_tpin"required>
										        </div>
										    </div>
								          	<input type="hidden" class="form-control" id="lawFirmId" name="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
								          	<input type="hidden" id="client_id" name="client_id">
								          	<button type="submit" class="btn btn-primary" id="saveClient">Save Client</button>
								        </form>
	          						</div>
	          						<div class="card-footer">
	          							
										<!-- Modal -->
										<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <h5 class="modal-title" id="clientModalLabel">Client Initial Information Form</h5>
										        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										      </div>
										      <div class="modal-body">
										        
										      </div>
										    </div>
										  </div>
										</div>
										
										<!-- Processing Modal -->
										<!-- Modal -->
										<div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true">
										  <div class="modal-dialog modal-fullscreen">
										    <div class="modal-content bg-light bg-opacity-75">
										      <div class="modal-body d-flex align-items-center justify-content-center" style="height: 100vh;">
										        <div class="text-center">
										          <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
										            <span class="visually-hidden">Loading...</span>
										          </div>
										          <p class="mt-3">Processing...</p>
										        </div>
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
    
    <script>
    	$(document).ready(function() {
		  	$('#clientType').on('change', function() {
			    if ($(this).val() === 'Corporate') {
					$('#corporateFields').show();
					$('#individualFields').hide();
					$('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', true);
					$('#client_names, #client_phone, #client_email, #clientTpin').prop('required', false);
			    } else if ($(this).val() === 'Individual') {
					$('#corporateFields').hide();
					$('#individualFields').show();
					$('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', false);
					$('#client_names, #client_phone, #client_email, #clientTpin').prop('required', true);
			    } else {
			      	$('#corporateFields, #individualFields').hide();
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
		                fetchLawFirmClients();
		            },
		            error: function(xhr, status, error) {
		                $('#saveClient').prop("disabled", false).html("Save Client");
		                sweetError(error);
		            }
		        });
		    });
		});

		function fetchLawFirmClients() {
			var lawFirmId = "<?php echo $_SESSION['parent_id']?>"; // Assign the actual lawFirmId here
			$.ajax({
				type: 'POST',
				url: 'cc/fetchLawFirmClients',
				data: { lawFirmId: lawFirmId },
				dataType: 'json', // Specify the response type
				success: function(response) {
					if (response.success) {
						var clients = response.clients;
						var select = $("#client_tpin");
						select.empty(); // Clear any existing options
						select.append('<option value="">Select Client</option>'); // Add default option
						
						// Populate the select element with new options
						clients.forEach(function(client) {
							var option = $('<option></option>')
								.attr('value', client.client_tpin)
								.attr('data-email', client.client_email)
								.attr('data-tpin', client.client_tpin)
								.attr('data-id', client.id)
								.text(client.client_names + ' (' + client.client_type + ')');
							select.append(option);
						});
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error: ' + error);
				}
			});
		}

    	fetchLawFirmClients();

    </script>
</body>
</html>