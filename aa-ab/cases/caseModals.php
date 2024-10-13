<div class="card-footer">
	<div class="modal fade" id="addNewCaseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-fullscreen bg-primary">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Edit Case</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<!-- Include Matter Form -->
				<?php include 'addMatterForm.php';?>
			</div>
		</div>
	</div>

	<!-- Milestone Modal -->
	<div class="modal fade" id="milestoneModal" tabindex="-1" role="dialog" aria-labelledby="milestoneModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content modal-lg">
				<div class="modal-header">
					<h5 class="modal-title" id="milestoneModalLabel">Add Milestone</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					</button>
				</div>
				<div class="modal-body">
					<form id="milestoneForm">
						<input type="hidden" id="milestoneClientId" name="clientId" value="<?php echo $clientId?>">
						<input type="hidden" id="milestoneClient_tpin" name="client_tpin" value="<?php echo $client_tpin?>">
						<input type="hidden" id="milestoneCaseNo" name="caseNo" value="<?php echo $caseNumber?>">
						<input type="hidden" name="caseId" id="milestoneCaseId" value="<?php echo encrypt($caseId)?>">
						<input type="hidden" id="userId" name="userId" value="<?php echo $userId?>">
						<input type="hidden" id="lawFirmId" name="lawFirmId" value="<?php echo $lawFirmId?>">
						<input type="hidden" name="milestoneId" id="milestoneId">
						<div class="form-group mb-3">
							<label for="milestoneTitle" class="mb-2">Milestone Title</label>
							<input type="text" class="form-control" id="milestoneTitle" name="milestoneTitle" required>
						</div>
						<div class="form-group mb-3">
							<label for="milestoneDescription" class="mb-2">Description</label>
							<div class="editor-toolbar">
								<button type="button" onclick="formatText('bold')"><i class="bi bi-type-bold"></i></button>
								<button type="button" onclick="formatText('italic')"><i class="bi bi-type-italic"></i></button>
								<button type="button" onclick="formatText('underline')"><i class="bi bi-type-underline"></i></button>
								<select onchange="formatText('heading', this.value)">
								<option value="">Paragraph</option>
								<option value="h1">Heading 1</option>
								<option value="h2">Heading 2</option>
								<option value="h3">Heading 3</option>
								<option value="h4">Heading 4</option>
								<option value="h5">Heading 5</option>
								<option value="h6">Heading 6</option>
								</select>
								<button type="button" onclick="formatText('insertUnorderedList')"><i class="bi bi-list-ul"></i></button>
								<button type="button" onclick="formatText('insertOrderedList')"><i class="bi bi-list-ol"></i></button>
								<select onchange="formatText('fontName', this.value)">
								<option value="">Font</option>
								<option value="Arial">Arial</option>
								<option value="Times New Roman">Times New Roman</option>
								<option value="Georgia">Georgia</option>
								<option value="Verdana">Verdana</option>
								</select>
								<button type="button" onclick="formatText('justifyLeft')"><i class="bi bi-justify-left"></i></button>
								<button type="button" onclick="formatText('justifyCenter')"><i class="bi bi-justify"></i></button>
								<button type="button" onclick="formatText('justifyRight')"><i class="bi bi-justify-right"></i></button>
								<input type="color" onchange="formatText('foreColor', this.value)" title="Text Color">
								<input type="color" onchange="formatText('hiliteColor', this.value)" title="Background Color">
							</div>
							<div class="editor-content" contenteditable="true" id="editor-content"></div>
						</div>
						<div class="form-group mb-3">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="agree" name="agree" required>
								<label class="form-check-label" for="agree">
									I agree that the information provided is correct.
								</label>
							</div>
						</div>
						<button type="submit" class="btn btn-primary" id="milestoneBtn">Save Milestone</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- Report Modal -->
	<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="reportModalLabel">Add Report to Clients</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					</button>
				</div>
				<div class="modal-body">
					<form id="reportForm">
						<input type="hidden" id="reportClientId" name="clientId" value="<?php echo $clientId?>">
						<input type="hidden" id="reportClient_tpin" name="client_tpin" value="<?php echo $client_tpin?>">
						<input type="hidden" id="reportCaseNo" name="caseNo" value="<?php echo $caseNumber?>">
						<input type="hidden" name="caseId" id="reportCaseId" value="<?php echo encrypt($caseId)?>">
						<input type="hidden" id="reportUserId" name="userId" value="<?php echo $userId?>">
						<input type="hidden" id="lawFirmId" name="lawFirmId" value="<?php echo $lawFirmId?>">
						<input type="hidden" name="reportId" id="reportId">
						<div class="form-group mb-3">
							<label for="reportSubject" class="mb-2">Subject</label>
							<input type="text" class="form-control" id="reportSubject" name="reportSubject" required>
						</div>
						<div class="form-group mb-3">
							<label for="reportMessage" class="mb-2">Message</label>
							<textarea class="form-control report-textarea" id="reportMessage" name="reportMessage" rows="10" required></textarea>
						</div>
						<button type="submit" class="btn btn-primary">Send Report</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- End of modal for reports and milestone -->

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

	<!-- End of document modal -->
	<!-- Change Case Status -->
	<div class="modal fade" id="caseStatusModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="caseModalLabel"> Update Case Status</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="caseStatusForm" method="POST">
					<div class="modal-body">
						<input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
						<input type="hidden" name="caseId" id="caseId" value="<?php echo $caseId ?>">
						<input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
						<div class="form-groups mb-3">
							<label class="mb-2" for="caseStatus">Case Status</label>
							<select class="form-control" id="caseStatus" name="caseStatus" required>
								<option value="">Choose</option>
								<option value="Initial Review">Initial Review</option>
								<option value="Active">Active Case</option>
								<option value="Trial">Trial</option>
								<option value="Judgement">Judgement</option>
								<option value="Post-Trial">Post-Trial</option>
								<option value="Closed">Closed Case</option>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" id="submitCaseStatus">Update Case Status</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Create Folder Modal -->
	<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-labelledby="createFolderModalLabel" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="createFolderModalLabel">Create Folder</h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true"></span>
	                </button>
	            </div>
	            <form id="createFolderForm">
	            	<div class="modal-body">
                <div class="form-group">
                    <label for="folderName" class="mb-1">Folder Name</label>
                    <input type="text" class="form-control" id="folderName" name="folderName" required>
                </div>
                <input type="hidden" name="caseId" value="<?php echo $caseId?>">
                <input type="hidden" name="caseNo" value="<?php echo $caseNo?>">
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
		                <button type="submit" class="btn btn-primary" id="createFolderBtn">Create</button>
		            </div>
	            </form>
	        </div>
	    </div>
	</div>
	<!-- File Preview -->
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

  	<!-- Move Files to Folder Modal -->
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
              <button type="submit" class="btn btn-primary" id="moveBtn">Move Files</button>
            </form>
          </div>
        </div>
      </div>
  	</div>
</div>