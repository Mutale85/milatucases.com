<?php  
	$clientType = basename($_SERVER['REQUEST_URI']);
	$clientType = ucwords($clientType);
?>
<div class="col-md-12">
	<div class="card">
		<div class="card-header">
			<h5 class="card-title">Add New Client</h5>
			<i class="bi bi-braces-asterisk text-danger"></i> Required
		</div>
		<div class="card-body">
			<form id="clientsForm">
				<input type="hidden" name="type" id="typeClient" value="<?php echo $clientType?>">
	          	<div class="mb-3">
	            	<label for="clientType" class="form-label">Client Type</label>
					<select class="form-select" id="clientType" name="client_type" required>
						<option value="">Select client type</option>
						<option value="Corporate">Corporate</option>
						<option value="Individual">Individual</option>
					</select>
	          	</div>
		        <div id="corporateFields" class="row" style="display: none;">
		            <div class="mb-3 col-md-6">
		              	<label for="business_entity_name" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Business/Entity Name </label>
		              	<input type="text" class="form-control" id="business_entity_name" name="business_entity_name">
		            </div>
		            <div class="mb-3 col-md-6">
		              	<label for="incorporation_number" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Business Incorporation Number</label>
		              	<input type="text" class="form-control" id="incorporation_number" name="incorporation_number">
		            </div>
		            <div class="mb-3 col-md-6">
		              	<label for="business_tpin" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Business TPIN Number</label>
		              	<input type="text" class="form-control" id="business_tpin" name="business_tpin">
		            </div>
		            <div class="mb-3 col-md-6">
				        <label for="representative_name" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Representative Names</label>
				        <input type="text" class="form-control" id="representativeName" name="representative_name" required>
				    </div>
		            <div class="mb-3 col-md-6">
				        <label for="representative_email" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Representative Email</label>
				        <input type="email" class="form-control" id="representative_email" name="representative_email" required>
				    </div>
				    <div class="mb-3 col-md-6">
				        <label for="representative_phone" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Representative Phone</label>
				        <input type="phone" class="form-control" id="representative_phone" name="representative_phone" required value="260">
				    </div>
				    <div class="mb-3 col-md-12">
				        <label for="business_address" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Business Address</label>
				        <textarea class="form-control" id="business_address" name="business_address" required placeholder="Plot No."></textarea>
				    </div>
		            <div class="mb-3 form-check" style="display:none;">
		              	<input type="checkbox" class="form-check-input" id="allow_login" name="allow_login">
		              	<label class="form-check-label" for="allow_login">Allow client to log in</label>
		            </div>
		        </div>
	          	<div id="individualFields" class="row" style="display: none;">
		          	<div class="mb-3 col-md-6">
		            	<label for="client_name" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Client Name</label>
		            	<input type="text" class="form-control" id="client_names" name="client_names" required>
		          	</div>
		          	<div class="mb-3 col-md-6">
		            	<label for="nrc_passport_number" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> NRC or Passport Number</label>
		            	<input type="text" class="form-control" id="nrc_passport_number" name="nrc_passport_number" required>
		          	</div>
		          	<div class="mb-3 col-md-6">
	            		<label for="client_phone" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Client Phone</label>
	            		<input type="text" class="form-control" id="client_phone" name="client_phone" required value="260">
	          		</div>
	          		<div class="mb-3 col-md-6">
		            	<label for="client_email" class="form-label">Client Email</label>
		            	<input type="email" class="form-control" id="client_email" name="client_email" placeholder="email@example.com">
		          	</div>
		          	<div class="mb-3 col-md-6">
				        <label for="client_address" class="form-label"><i class="bi bi-braces-asterisk text-danger"></i> Address</label>
				        <textarea class="form-control" id="client_address" name="client_address" required placeholder="House Number:"></textarea>
				    </div>
			        <div class="mb-3 col-md-6">
			            <label for="client_tpin" class="form-label">Individual TPIN Number</label>
			            <input type="text" class="form-control" id="clientTpin" name="client_tpin" placeholder="Individual Tpin">
			        </div>
			    </div>
	          	<input type="hidden" class="form-control" id="lawFirmId" name="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
	          	<input type="hidden" id="client_id" name="client_id" value="">
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