<?php include('../../includes/db.php')?>
<?php require('../base/base.php')?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include '../addon_header.php'?>
        <style>
            .modal-lg {
                max-width: 80%;
            }
        </style>

    </head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include '../addon_side_nav.php';?>        
            <div class="layout-page">
                <?php include '../addon_top_nav.php';?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <button type="button" id="adminBtn" data-id="<?php echo $_SESSION['parent_id']?>" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#userModal">
                                    <i class="bi bi-file-pdf"></i> Upload Files
                                </button>

                                <div class="card card-outline-warning">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title">File Upload</h5>
                                            </div>
                                            <div class="col-auto">
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createFolderModal"><i class="bi bi-folder"></i> Create Folder</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Name</th>
                                                    <th>Owner</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="listedFiles">
                                                <!-- Files will be listed here -->
                                            </tbody>
                                        </table>
                                        <button id="moveToFolderButton" class="btn btn-primary mt-5" style="display: none;">Move to Folder</button>
                                          
                                    </div>

                                    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
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
                                                        <h5 class="modal-title" id="userModalLabel">Files System</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" id="churchForm" enctype="multipart/form-data">
                                                            <div class="form-group">
                                                                <label for="files">Choose files:</label>
                                                                <input type="file" name="files[]" id="files" class="form-control" multiple required>
                                                            </div>
                                                            <input type="hidden" name="church_id" value="<?php echo $_SESSION['parent_id']; ?>">
                                                            <input type="hidden" name="uploaded_by" value="<?php echo $_SESSION['user_id']; ?>">
                                                            <div class="form-group mt-3">
                                                                <h4>Selected Files</h4>
                                                                <ul id="fileList" class="list-group">
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

                                        <!-- Create Folder Modal -->
                                        <div class="modal fade " id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
                                            <div class="modal-dialog center">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="createFolderModalLabel">Create Folder</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="createFolderForm">
                                                            <div class="mb-3">
                                                                <label for="folderName" class="form-label">Folder Name:</label>
                                                                <input type="text" class="form-control" id="folderName" name="folderName" placeholder="Enter folder name" required>
                                                            </div>
                                                            <button type="submit" id="folderBtn" class="btn btn-primary">Create</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal for folders preview -->
                                    
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
                    </div>
                    <?php include '../addon_footer_link.php';?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer.php';?>
    <script type="text/javascript" src="../assets/custom/files.js"></script>
</body>
</html>