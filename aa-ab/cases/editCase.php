<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Client Cases</title>
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
	          							<h5 class="card-title">Add New Case</h5>
	          						</div>
	          						<div class="card-body">
	          							<form id="addNewCaseForm" method="POST" enctype="multipart/form-data">
							                <div class="row">
							                	<!-- Select Client -->
							                	<div class="col-md-6">
							                		<label class="mb-1" for="caseNo">Select Client</label>
							                		<div class="input-group">
									                	<select class="form-select" id="client_tpin" name="client_tpin" required></select>
									                    <button type="button" class="btn btn-sm btn-primary" id="addCaseBtn" data-bs-toggle="modal" data-bs-target="#clientModal">+ New Client</button>
									                </div>
								                </div>
							                    <div class="col-md-6 mb-3">
							                        <label class="mb-1" for="caseNo">Case ID</label>
							                        <div class="input-group">
							                            <input type="text" class="form-control" id="caseNo" name="caseNo" placeholder="Enter or generate case ID" required>
							                            <button type="button" class="btn btn-outline-secondary" id="generateCaseId">Generate</button>
							                        </div>
							                    </div>
							                    <div class="col-md-6 mb-3">
							                        <label class="mb-1" for="caseTitle">Case Title</label>
							                        <input type="text" class="form-control" id="caseTitle" name="caseTitle" placeholder="Enter case title" required>
							                        <input type="hidden" name="clientId" id="clientId">
							                        <input type="hidden" name="caseId" id="caseId">
							                        <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
							                    </div>
							                    <div class="col-md-6 mb-3">
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
													</select>
							                    </div>
							                    <div class="col-md-12 mb-3">
							                        <label class="mb-1" for="caseDescription">Case Description</label>
							                        <textarea class="form-control" id="caseDescription" name="caseDescription" rows="3" placeholder="Enter case description" required></textarea>
							                    </div>
							                    <div class="col-md-6 mb-3">
							                        <label class="mb-1" for="caseDate">Case Start Date</label>
							                        <input type="date" class="form-control" id="caseDate" name="caseDate" required>
							                    </div>
							                    <div class="col-md-6 mb-3">
							                        <label class="mb-1" for="caseDocuments">Case Files</label>
							                        <input type="file" class="form-control" id="caseDocuments" name="caseDocuments[]" multiple>
							                        <div id="uploadedFilesList" class="mt-2"></div>
							                    </div>
							                    <div class="col-md-6 mb-3">
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
							                </div>
							                <div class="div-footer">
							                    <button type="submit" class="btn btn-primary" id="submitCase">Save Case</button>
							                </div>
							            </form>
	          						</div>
	          						<div class="card-footer">
	          							
										<!-- Modal -->
										<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
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
														<select class="form-select" id="clientType" name="client_type" required>
															<option value="">Select client type</option>
															<option value="Corporate">Corporate</option>
															<option value="Individual">Individual</option>
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
										</div>
										
										<!-- Processing Modal -->
										<!-- Modal -->
										<div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true">
										  <div class="modal-dialog modal-fullscreen">
										    <div class="modal-content bg-light bg-opacity-75">
										      <div class="modal-body d-flex align-items-center justify-content-center" style="height: 100vh;">
										        <div class="text-center">
										          <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
										            <span class="visually-hidden">Loading...</span>
										          </div>
										          <p class="mt-3">Processing...</p>
										        </div>
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
    
    <script>
    	$(document).ready(function() {
		  	$('#clientType').on('change', function() {
			    if ($(this).val() === 'Corporate') {
					$('#corporateFields').show();
					$('#individualFields').hide();
					$('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', true);
					$('#client_names, #client_phone, #client_email, #clientTpin').prop('required', false);
			    } else if ($(this).val() === 'Individual') {
					$('#corporateFields').hide();
					$('#individualFields').show();
					$('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', false);
					$('#client_names, #client_phone, #client_email, #clientTpin').prop('required', true);
			    } else {
			      	$('#corporateFields, #individualFields').hide();
			    }
		  	});
		});

		$(document).ready(function() {
		    $('#clientsForm').submit(function(e) {
		        e.preventDefault();
		        var formData = $(this).serialize();
		        $.ajax({
		            type: 'POST',
		            url: 'cc/createInitialClient',
		            data: formData,
		            beforeSend: function() {
		                $('#saveClient').prop("disabled", true).html(`Processing ... <div class="spinner-border text-primary" role="status">
		            	<span class="visually-hidden">Loading...</span>
		          		</div>`);
		            },
		            success: function(response) {
		                $('#saveClient').prop("disabled", false).html("Save Client");
		                $('#clientModal').modal('hide');
		                sweetSuccess(response);
		                $('#clientsForm')[0].reset();
		                fetchLawFirmClients();
		            },
		            error: function(xhr, status, error) {
		                $('#saveClient').prop("disabled", false).html("Save Client");
		                sweetError(error);
		            }
		        });
		    });
		});

		
		function fetchLawFirmClients() {
			var lawFirmId = "<?php echo $_SESSION['parent_id']?>"; // Assign the actual lawFirmId here
			$.ajax({
				type: 'POST',
				url: 'cc/fetchLawFirmClients',
				data: { lawFirmId: lawFirmId },
				dataType: 'json', // Specify the response type
				success: function(response) {
					if (response.success) {
						var clients = response.clients;
						var select = $("#client_tpin");
						select.empty(); // Clear any existing options
						select.append('<option value="">Select Client</option>'); // Add default option
						
						// Populate the select element with new options
						clients.forEach(function(client) {
							var option = $('<option></option>')
								.attr('value', client.client_tpin)
								.attr('data-email', client.client_email)
								.attr('data-tpin', client.client_tpin)
								.attr('data-id', client.id)
								.text(client.client_names + ' (' + client.client_type + ')');
							select.append(option);
						});
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error: ' + error);
				}
			});
		}

    	fetchLawFirmClients();

    	document.getElementById('generateCaseId').addEventListener('click', function() {
	        var randomId = 'CASE-' + Math.floor(Math.random() * 1000000);
	        document.getElementById('caseNo').value = randomId;
	    });
    	

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

	    $('#addNewCaseForm').submit(function (e) {
		    e.preventDefault();

		    let formData = new FormData(this);
		    let selectedLawyers = [];

		    $('#accessControl input:checked').each(function () {
		        selectedLawyers.push($(this).val());
		    });

		    if (selectedLawyers.length === 0) {
		        sweetError('Please select at least one lawyer.');
		        return;
		    }

		    formData.append('selectedLawyers', JSON.stringify(selectedLawyers));

		    $.ajax({
		        type: 'POST',
		        url: 'cases/createNewCase',
		        data: formData,
		        beforeSend:function(){
		        	$("#submitCase").prop("disabled", true).html("Processing...");
		        },
		        processData: false,
		        contentType: false,
		        success: function (response) {
		            sweetSuccess(response);
		            $("#submitCase").prop("disabled", false).html("Save Case");
		            // location.reload();
		        },
		        error: function (error) {
		            sweetError('Error: ' + error.responseText);
		            $("#submitCase").prop("disabled", false).html("Save Case");
		        }
		    });
		});

	    $('#caseDocuments').on('change', function() {
            var files = this.files;
            var filesList = $('#uploadedFilesList');
            filesList.empty();

            for (var i = 0; i < files.length; i++) {
                filesList.append('<p>' + files[i].name + '</p>');
            }
        });

        $(document).ready(function() {
		  	$('#caseDescription').trumbowyg({
			    btns: [
			      ['strong', 'em', 'del'],
			      ['link'],
			      ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
			      ['unorderedList', 'orderedList'],
			      ['horizontalRule'],
			      ['removeformat']
			    ],
			    autogrow: true,
			    removeformatPasted: true,
			    disabled: false,
			    imageWidthModalEdit: true,
			    imageUpload: false,
			    urlProtocol: false,
			    plugins: {
			      // Disable image and video features
			      upload: {
			        serverPath: null
			      },
			      // Disable emoji plugin
			      emoji: {
			        svgPath: null
			      }
			    }
		  	});
		

        
			$('#client_tpin').change(function() {
		        var selectedClient = $(this).find('option:selected');
		        var clientId = selectedClient.data('id');
		       
		        if ($(this).val() !== "") {
		            
		            $('#clientId').val(clientId);
		        } else {
		            
		            $('#clientId').val('');
		        }
		    });
		    $('#client_tpin').trigger('change');
	    });
    </script>
</body>
</html>