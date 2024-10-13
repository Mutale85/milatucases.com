<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>All Cases</title>
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
          								<h5 class="card-title text-primary">New Cases</h5>
          							</div>
          							<div class="card-body">
          								<div class="table table-responsive">
          									<table class="table table-striped" id="allTables">
          										<thead>
          											<tr>
          												<th>Client</th>
          												<th>Tpin</th>
          												<th>Added On</th>
          												<th>Add Case</th>
          												<th>Cases</th>
          											</tr>
          										</thead>
          										<tbody>
											        <?php echo fetchClients($_SESSION['parent_id']);?>
											    </tbody>
          									</table>
          								</div>
          							</div>
          							<div class="modal fade" id="addCaseModal" tabindex="-1" aria-labelledby="caseModalLabel" aria-hidden="true">
									    <div class="modal-dialog modal-lg">
									        <div class="modal-content">
									            <div class="modal-header">
									                <h5 class="modal-title" id="caseModalLabel">Add Case</h5>
									                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									            </div>
									            <form id="addCaseForm" method="POST" enctype="multipart/form-data">
									                <div class="modal-body">
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="caseNo">Case ID</label>
									                        <div class="input-group">
									                            <input type="text" class="form-control" id="caseNo" name="caseNo" placeholder="Enter or generate case ID" required>
									                            <button type="button" class="btn btn-outline-secondary" id="generateCaseId">Generate</button>
									                        </div>
									                    </div>
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="caseTitle">Case Title</label>
									                        <input type="text" class="form-control" id="caseTitle" name="caseTitle" placeholder="Enter case title" required>
									                        <input type="hidden" name="clientId" id="clientId">
									                        <input type="hidden" name="caseId" id="caseId">
									                        <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
									                    </div>
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="caseStatus">Case Status</label>
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
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="caseDescription">Case Description</label>
									                        <textarea class="form-control" id="caseDescription" name="caseDescription" rows="3" placeholder="Enter case description" required></textarea>
									                    </div>
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="caseDate">Case Start Date</label>
									                        <input type="date" class="form-control" id="caseDate" name="caseDate" required>
									                    </div>
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="caseDocuments">Case Files</label>
									                        <input type="file" class="form-control" id="caseDocuments" name="caseDocuments[]" multiple>
									                        <div id="uploadedFilesList" class="mt-2"></div>
									                    </div>
									                    
									                    <div class="form-group mb-3">
									                        <label class="mb-1" for="feeMethod">Fee Method</label>
									                        <select class="form-control" id="feeMethod" name="feeMethod" required>
									                            <option value="">Choose</option>
									                            <option value="Hourly Rate">Hourly Rate</option>
									                            <option value="Fixed Fee">Fixed Fee</option>
									                            <option value="Contingency Fee">Contingency Fee</option>
									                            <option value="Retainer Fee">Retainer Fee</option>
									                            <option value="Success Fee">Success Fee</option>
									                        </select>
									                    </div>
									                    <div id="hourlyRateInput" class="form-group mb-3" style="display: none;">
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
									                    <div id="fixedFeeInput" class="form-group mb-3" style="display: none;">
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
									                    <div class="form-group mb-3">
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
									                </div>
									                <div class="modal-footer">
									                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
									                    <button type="submit" class="btn btn-primary" id="submitCase">Save Case</button>
									                </div>
									            </form>
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
    <!-- <script type="text/javascript" src="../dist/controls/clientDetails.js"></script> -->
    <script>
    	document.getElementById('generateCaseId').addEventListener('click', function() {
	        var randomId = 'CASE-' + Math.floor(Math.random() * 1000000);
	        document.getElementById('caseNo').value = randomId;
	    });
    	$(document).on("click", ".addCase", function(e){
    		var clientId = $(this).data('client-id');
    		$("#clientId").val(clientId);
    	})

    	document.addEventListener('DOMContentLoaded', function () {
	        const feeMethodSelect = document.getElementById('feeMethod');
	        const hourlyRateInput = document.getElementById('hourlyRateInput');
	        const fixedFeeInput = document.getElementById('fixedFeeInput');

	        feeMethodSelect.addEventListener('change', function () {
	            if (this.value === 'Hourly Rate') {
	                hourlyRateInput.style.display = 'block';
	                fixedFeeInput.style.display = 'none';
	            } else {
	                hourlyRateInput.style.display = 'none';
	                fixedFeeInput.style.display = 'block';
	            }
	        });
	    });

	    $('#addCaseForm').submit(function (e) {
	        e.preventDefault();
	        
	        let formData = new FormData(this);
	        let selectedLawyers = [];
	        
	        $('#accessControl input:checked').each(function () {
	            selectedLawyers.push($(this).val());
	        });
	        
	        formData.append('selectedLawyers', JSON.stringify(selectedLawyers));

	        $.ajax({
	            type: 'POST',
	            url: 'base/createCase',
	            data: formData,
	            processData: false,
	            contentType: false,
	            success: function (response) {
	                alert(response);
	                location.reload();
	                // Handle success - close modal, reset form, etc.
	            },
	            error: function (error) {
	                alert('Error: ' + error.responseText);
	                // Handle error
	            }
	        });
	    });
    </script>
</body>
</html>