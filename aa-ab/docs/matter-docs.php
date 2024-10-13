<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-semi-dark" data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
  <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName'];?> Documents</title>
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
          								<h5 class="card-title">Folders</h5>
          							</div>
          							<div class="card-body">
          								<?php
          									$caseIds = fetchcaseIdsByLawFirmId($lawFirmId);
          								 	echo '<div class="list-group">'; 
										        foreach ($caseIds as $case) {
										            $caseId = $case['caseId'];
										            $clientId = fetchCLientIdCaseId($caseId);
										            $folderName = getClientNameById($clientId, $lawFirmId);
										            $docs = countDocumentsByCaseAndLawFirm($caseId);
										            $caseNo = fetchCaseNoById($caseId);
										            if(userHasAccessToCase($userId, $caseId, $lawFirmId)){
       
											            echo '<a href="#" data-case-id="'.$caseId.'" data-case-no="'.$caseNo.'" class="list-group-item list-group-item-action displayDocument"><i class="bi bi-folder"></i> ' . $caseNo .' : '. $folderName . ' - files ('.$docs.') - View Files</a>';
											        }
										        }
										    		echo '</div>';
          								?>
          							</div>
          							<div class="card-footer">
          								<p class="text-primary">Files are created when you add a case for the client</p>
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
    <script type="text/javascript" src="../assets/custom/matterDocs.js"></script>
</body>
</html>