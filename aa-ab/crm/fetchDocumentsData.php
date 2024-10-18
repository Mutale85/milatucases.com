<?php 
	include "../../includes/db.php";
	if(isset($_POST['clientId'])){
		$clientId = base64_decode($_POST['clientId']);
		$lawFirmId = $_SESSION['parent_id'];
		
		$sql = $connect->prepare("SELECT * FROM `cases` WHERE `lawFirmId` = ? AND `clientId` = ? ");
		$sql->execute([$lawFirmId, $clientId]);
		
		    
	    if ($sql->rowCount() > 0) {
	        echo '<select id="selectCaseId" class="form-control">'; 
	        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $row) {
	            extract($row);
	            $folderName = htmlspecialchars($caseNo, ENT_QUOTES, 'UTF-8'); // Prevent XSS attacks
	            $docs = countDocumentsByCaseAndLawFirm($id);
	            if (userHasAccessToCase($userId, $id, $lawFirmId)) {
	            	echo '<option value="'.$id.'">'.$caseNo.'</option>';
	            }
	        }
	        echo '</select>';
	    
		

		?>

		<div class="tab-pane fade show " id="list-documents" role="tabpanel" aria-labelledby="list-documents-list" style="display:none">
			<div class="d-flex justify-content-between mb-5">
			    <button type='button' class='mb-3 btn btn-dark btn-sm displayDocument' data-case-id='<?php echo $caseId?>' data-case-no='<?php echo $caseNo?>'><i class='bi bi-file-earmark-pdf'></i>Add Files</button>
			    <button type='button' class='mb-3 btn btn-primary btn-sm' data-bs-toggle="modal" data-bs-target="#createFolderModal"><i class='bi bi-folder'></i> Create Folder</button>
			</div>
			<ul class="my-nav-tabs">
	      		<li><a href="#files" class="active" data-tab="files">Matter Files</a></li>
	      		<li><a href="#folders" data-tab="folders">Matter Folders</a></li>
	    	</ul>
		    <div class="my-tab-content">
		      <div class="my-tab-pane active" id="files">
		        <h4 class="mb-3">Matter Files</h4>
		        <div class="table table-responsive">
		        	<table class='table mb-4' id="allTables">
	        		<thead class='border-bottom pb-3'>
	        			<tr>
							<th>Select</th>
	        				<th>Document Name</th>
	        				<th>Uploaded By</th>
						    <th>Created At</th>
	        				<th>Actions</th>
	        			</tr>
	        		</thead>
	            	<tbody id="filesDisplay">
	            		<?php echo fetchCasePostedDocuments($caseId, $lawFirmId)?>
	            	</tbody>
	            </table>
				<button id="moveToFolderButton" class="btn btn-primary btn-sm mt-5" style="display: none;">Move to Folder</button>
	          </div>
		      </div>
		      	<div class="my-tab-pane" id="folders">
		      		<h4 class="mb-3">Matter Folders</h4>
		        	<div class='table table-responsive'>
				        <table class='table table-striped' id='allTables'>
					        <thead>
						        <tr>
							        <th>Folder Name</th>
							        <th>Uploaded By</th>
							        <th>Created At</th>
							        <th>Actions</th>
					        	</tr>
					        </thead>
					        <tbody id="fetchFolders">
					        	<?php echo displayFolders($caseId, $lawFirmId)?>
					        </tbody>
					    </table>
					</div>
		      	</div>
		    </div>
		</div>
<?php
		} else {
	        echo "No case files found";
	    }		
	}

?>