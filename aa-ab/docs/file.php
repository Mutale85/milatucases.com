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
  <title>Template</title>
  <?php include '../addon_header.php'; ?>
  <?php

      if(isset($_GET['doc'])){
          $doc = $_GET['doc'];
          $folder_id = $doc;
          $folderName = base64_decode($folder_id);
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
                          
                          <div class="card card-outline-warning">
                              <div class="card-header">
                                  <div class="row">
                                      <div class="col">
                                          <h5 class="card-title"><i class="bi bi-folder-check"></i> <?php echo getFolderName($folderName)?></h5>
                                      </div>
                                      <div class="col-auto">
                                          <button type="button" id="adminBtn" data-id="<?php echo $_SESSION['parent_id']?>" class="btn btn-primary btn-sm mb-4" data-bs-toggle="modal" data-bs-target="#userModal">
                                              <i class="bi bi-file-pdf"></i> Add Files
                                          </button>

                                      </div>
                                  </div>
                              </div>
                              <div class="card-body">
                                  <table class="table table-bordered">
                                      <thead>
                                          <tr>
                                              <th>File Name</th>
                                              <th>Uploaded By</th>
                                              <th>Uploaded At</th>
                                              <th>Actions</th>
                                          </tr>
                                      </thead>
                                      <tbody id="folderListedFiles">
                                          <!-- Files will be listed here -->
                                      </tbody>
                                  </table>                                          
                              </div>

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

                              <div class="card-footer">
                                  <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
                                      <div class="modal-dialog modal-xl">
                                          <div class="modal-content">
                                              <div class="modal-header">
                                                  <h5 class="modal-title" id="userModalLabel">Add files to <em><?php echo getFolderName($folderName)?></em> folder</h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>
                                              <div class="modal-body">
                                                  <form method="POST" id="folderForm" enctype="multipart/form-data">
                                                      <div class="form-group">
                                                          <label for="files">Choose files:</label>
                                                          <input type="file" name="files[]" id="files" class="form-control" multiple required>
                                                      </div>
                                                      <input type="hidden" name="lawFirmId" value="<?php echo $_SESSION['parent_id']; ?>">
                                                      <input type="hidden" name="uploaded_by" value="<?php echo $_SESSION['user_id']; ?>">
                                                      <input type="hidden" name="folder_id" id="folderId" value="<?php echo $folder_id; ?>">
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
                              <!-- Move Files Modal -->

                              <div class="modal fade" id="selectFolderModal" tabindex="-1" aria-labelledby="selectFolderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="selectFolderModalLabel">Select Folder</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <form id="moveFilesForm">
                                            <div class="mb-3">
                                              <label for="folderSelect" class="form-label">Choose Folder</label>
                                              <select class="form-select" id="folderSelect" name="folderSelect" required>
                                                
                                              </select>
                                            </div>
                                            <input type="hidden" id="selectedFiles" name="selectedFiles">
                                            <button type="submit" class="btn btn-primary">Move Files</button>
                                          </form>
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
    <script type="text/javascript" src="../assets/custom/folderFiles.js"></script>
</body>
</html>