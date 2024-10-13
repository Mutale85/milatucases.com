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
	<title>All <?php echo $_SESSION['lawFirmName']?>'s Clients</title>
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
	          							<h5 class="card-title text-primary">All Clients</h5>
	          						</div>
	          						<div class="card-body">
	          							<div class="table-responsive text-nowrap">
		          							<table id="allTables" class="table table-light">
														    <thead>
														        <tr>
														            <th>Type</th>
														            <th>Names</th>
														            <th>KYC</th>
														            <th>Manual KYC</th>
														            <th>Action</th>
														            <th>Send KYC</th>
														        </tr>
														    </thead>
														    <tbody id="allClients">
														        <?php fetchLawFirmClients(); ?>
														    </tbody>
														</table>
													</div>
	          						</div>
	          						
	          						<div class="card-footer">
	          							<button type="button" class="btn btn-sm btn-primary" id="addClientModal" data-id="Individual" data-bs-toggle="modal" data-bs-target="#clientModal">+ New Client</button>
	          							<!-- Add Client Modal -->
	          							<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
													  <div class="modal-dialog modal-lg">
													    <div class="modal-content">
													      <div class="modal-header">
													        <h5 class="modal-title" id="clientModalLabel">Client Initial Information Form</h5>
													        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													      </div>
													      <div class="modal-body">
													      	<?php include 'addclientmodal.php';?>
													      </div>
													    </div>
													  </div>
													</div>
										<!-- End of Adding Client Modal -->
										<!-- Processing Modal -->
										<div class="modal fade" id="processingModal" tabindex="-1" aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      	<div class="modal-body">
										        	Processing...
											        <div class="spinner-border text-primary" role="status">
									                	<span class="visually-hidden">Loading...</span>
									                </div>
										      	</div>
										    </div>
										  </div>
										</div>
										<!-- End of Processing Modal -->
	          						</div>
	          					</div>
	          				</div>
          				</div>
          			</div>
          			<?php include '../addon_footer.php';?>
          			<div class="content-backdrop fade"></div>
          		</div>
          		<!-- <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
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
				</div> -->
          	</div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer_links.php';?>
    <script type="text/javascript" src="../assets/custom/clients.js"></script>
    
</body>
</html>