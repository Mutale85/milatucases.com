<form id="addCaseForm" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<div class="row">
			<div class="col-md-6 mb-3">
				<label class="mb-1" for="causeId">Cause ID</label>
				<div class="input-group">
					<input type="text" class="form-control" id="causeId" name="causeId" placeholder="Enter or Cause Id from court" >
				</div>
			</div>
			<div class="col-md-6 mb-3">
				<label class="mb-1" for="caseNo">Case ID</label>
				<div class="input-group">
					<input type="text" class="form-control" id="caseNo" name="caseNo" placeholder="Enter or generate case ID" required>
					<button type="button" disabled class="btn btn-outline-secondary" id="generateCaseId">Generate</button>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<label class="mb-1" for="caseTitle">Case Title</label>
				<input type="text" class="form-control" id="caseTitle" name="caseTitle" placeholder="Enter case title" required>
				<input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
				<input type="hidden" name="client_tpin" id="client_tpin">
				<input type="hidden" name="caseId" id="caseId" value="<?php echo $caseId?>">
				<input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
			</div>
			<div class="col-md-4 mb-3">
			    <label class="mb-1" for="caseCategory">Matter Category</label>
			    <select name="caseCategory" id="caseCategory" class="form-control" required>
				    <option value="">Select Matter Category</option>
				    <option value="business">Business</option>
				    <option value="civil">Civil</option>
				    <option value="conveyancing">Conveyancing</option>
				    <option value="corporate-advisory">Corporate Advisory</option>
				    <option value="criminal">Criminal</option>
				    <option value="employment">Employment</option>
				    <option value="estate">Estate/Probate</option>
				    <option value="family">Family</option>
				    <option value="intellectual-property">Intellectual Property</option>
				    <option value="other">Other</option>
				    <option value="personal-injury">Personal Injury</option>
				</select>
			    <div id="custom-status-input" class="mt-2" style="display: none;">
			        <input type="text" class="form-control" id="custom-status" name="custom-status" placeholder="Enter custom status">
			    </div>
			</div>
			<div class="col-md-4 mb-3">
				<label class="mb-1" for="caseStatus">Case Status</label>
				<select class="form-control" id="caseStatus" name="caseStatus" required>
					<option value="">Choose</option>
					<option value="Initial Review">Initial Review</option>
					<option value="Active">Active Case</option>
					<option value="Trial">Trial</option>
					<option value="Judgement">Judgement</option>
					<option value="Post-Trial">Post-Trial</option>
					<option value="Closed">Closed Case</option>
					<option disabled>──────────</option>
					<option value="Review Document">Review Document</option>
					<option value="Negotiations">Negotiations</option>
					<option value="Drafting">Drafting</option>
					<option value="Review of Draft">Review of Draft</option>
					<option value="Signature-Execution">Signature/Execution</option>
					<option disabled>──────────</option>
					<option value="Contract of Sale">Contract of Sale</option>
					<option value="Consent to Assign">Consent to Assign</option>
					<option value="Property Transfer">Property Transfer</option>
					<option value="Tax Clearance">Tax Clearance</option>
					<option value="Register Assignment">Register Assignment</option>
					<option value="custom">Other Status</option>
				</select>
				<div id="custom-status-input" class="mt-2" style="display: none;">
					<input type="text" class="form-control" id="custom-status" name="custom-status" placeholder="Enter custom status">
				</div>
			</div>
			<div class="col-md-12 mb-3">
				<label class="mb-1" for="caseDescription">Case Description</label>
				<textarea class="form-control" id="caseDescription2" name="caseDescription" rows="3" placeholder="Enter case description" required></textarea>
			</div>
			<div class="col-md-6 mb-3">
				<label class="mb-1" for="caseDate">Case Start Date</label>
				<input type="date" class="form-control" id="caseDate" name="caseDate" required>
			</div>
			
			<div class="col-md-6 ">
				<label class="mb-1" for="caseDocuments">Case Files</label>
				<div class="input-group mb-3">
					<input type="text" class="form-control" readonly aria-describedby="button-addon2">
					<button id="docBtn" class="btn btn-outline-secondary displayDocument" type="button" id="button-addon2"><i class='bi bi-file-earmark-pdf'></i> View / Add Documents</button>
				</div>
				
			</div>
			<div class="col-md-6 mb-3">
				<label class="mb-1" for="feeMethod">Fee Method</label>
				<select class="form-control" id="feeMethod" name="feeMethod" required>
					<option value="">Choose</option>
					<option value="Hourly Rate">Hourly Rate</option>
					<option value="Fixed Fee">Fixed Fee</option>
					<option value="Contingency Fee">Contingency Fee</option>
					<option value="Commissioned Fee">Commissioned Fee</option>
					<option value="Retainer Fee">Retainer Fee</option>
					<option value="Success Fee">Success Fee</option>

				</select>
			</div>
			<div id="hourlyRateInput" class="col-md-6 mb-3" style="display: none;">
				<label class="mb-1" for="hourlyRate">Hourly Rate</label>
				<div class="input-group">
					<select class="form-control" id="currency" name="currency">
						<option value="ZMW">Zambia Kwacha (ZMW)</option>
						<option value="USD">US Dollar (USD)</option>
						<option value="GBP">British Pound (GBP)</option>
						<option value="ZAR">South African Rand (ZAR)</option>
						<option value="EUR">Euro (EUR)</option>
					</select>
					<input type="number" class="form-control" id="hourlyRate" name="hourlyRate" placeholder="Enter hourly rate" min="0">
				</div>
			</div>
			<div id="fixedFeeInput" class="col-md-6 mb-3" style="display: none;">
				<label class="mb-1" for="fixedFee">Amount</label>
				<div class="input-group">
					<select class="form-control" id="currency" name="currency">
						<option value="ZMW">Zambia Kwacha (ZMW)</option>
						<option value="USD">US Dollar (USD)</option>
						<option value="GBP">British Pound (GBP)</option>
						<option value="ZAR">South African Rand (ZAR)</option>
						<option value="EUR">Euro (EUR)</option>
					</select>
					<input type="number" class="form-control" id="fixedFee" name="fixedFee" placeholder="Enter amount" min="0">
				</div>
			</div>
			<div class="col-md-6 mb-3">
				<label class="mb-1" for="accessControl">Lawyers assigned to Case</label>
				<div id="accessControl">
					<?php 
						$query = $connect->prepare("SELECT * FROM `lawFirms` WHERE `parentId` = ? ");
						$query->execute([$_SESSION['parent_id']]);
						foreach ($query->fetchAll() as $row) {
							echo '<div class="form-check">';
							echo '<input class="form-check-input" type="checkbox" name="accessControl[]" value="'.$row['id'].'" id="accessControl'.$row['id'].'">';
							echo '<label class="form-check-label" for="accessControl'.$row['id'].'">'.$row['names'].'</label>';
							echo '</div>';
						}
					?>
				</div>
			</div>
			<div class="col-md-6 mb-3">
				<div class="div-footer">
					<button type="submit" class="btn btn-primary" id="submitCase">Save Case</button>
				</div>
			</div>
		</div>
	</div>
</form>