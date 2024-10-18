<?php 
	include "../../includes/db.php";
	include '../base/base.php';
	if(isset($_GET['folderName'])){
		$folderName = $_GET['folderName'];
		$caseId = base64_decode($_GET['caseId']);
		$folderId = base64_decode($_GET['folderId']);
		$caseNo = fetchCaseNoById($caseId);
	}
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $folderName?></title>
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
          								<div class="row">
	          								<div class="col">
	          									<i class="bi bi-folder"></i> <?php echo getCaseFolderNameById($folderId)?> (Case ID: <?php echo $caseNo?>)
	          								</div>
  														<div class="col-auto">
                                  <button type="button" id="adminBtn" data-id="<?php echo $_SESSION['parent_id']?>" class="btn btn-primary btn-sm mb-4" data-bs-toggle="modal" data-bs-target="#userModal">
                                      <i class="bi bi-file-pdf"></i> Add Files
                                  </button>
                             	</div>
                          </div>
          							</div>
          							<div class="card-body">
          								<table class="table" id="allTables">
                              <thead>
                                  <tr>
                                      <th>File Name</th>
                                      <th>Uploaded By</th>
                                      <th>Uploaded At</th>
                                      <th>Actions</th>
                                  </tr>
                              </thead>
                              <tbody id="folderListedFiles">
                              	<?php echo fetchCaseFolderFiles($folderId);?>
                              </tbody>
                          </table>  
          							</div>
          						</div>
          					</div>
          				</div>
          				<!-- Modals for files -->
          				<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-xl">
                        	<div class="modal-content">
                            	<div class="modal-header">
                                	<h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                                	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            	</div>
                            	<div class="modal-body" id="filePreviewContent">
                            	</div>
                        	</div>
                      </div>
                  </div>
									<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-xl">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="userModalLabel">Add files to <em><?php echo getCaseFolderNameById($folderId)?></em> folder</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                  <form method="POST" id="folderForm" enctype="multipart/form-data">
                                      <div class="form-group">
                                          <label for="files">Choose files:</label>
                                          <input type="file" name="files[]" id="files" class="form-control" multiple required>
                                      </div>
                                      <input type="hidden" name="lawFirmId" value="<?php echo $_SESSION['parent_id']; ?>">
                                      <input type="hidden" name="userId" value="<?php echo $_SESSION['user_id']; ?>">
                                      <input type="hidden" name="folder_id" id="folderId" value="<?php echo $folderId ; ?>">
                                      <input type="hidden" name="caseId" id="caseId" value="<?php echo $caseId?>">
                                      <input type="hidden" name="caseNo" id="caseNo" value="<?php echo $caseNo?>">
                                      <div class="form-group mt-3">
                                          <p id="">Selected Files</p>
                                          <ul id="fileList" class="list-group">
                                              <!-- Selected files will be displayed here -->
                                          </ul>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                          <button type="submit" id="fileBtn" class="btn btn-primary">Save Files</button>
                                      </div>
                                  </form>
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
   	<script type="text/javascript" src="../assets/custom/caseFolders.js"></script>
</body>
</html>