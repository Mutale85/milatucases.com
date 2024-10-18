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
          		
          	</div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer_links.php';?>
    <script type="text/javascript" src="../assets/custom/clients.js"></script>
    
</body>
</html>