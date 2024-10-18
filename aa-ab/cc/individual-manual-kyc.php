<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter"><head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Individual Clients Manual KYC</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		if (isset($_GET['clientId'])) {
			$clientId = base64_decode($_GET['clientId']);
			$names = base64_decode($_GET['names']);
			$email = base64_decode($_GET['email']);
			$lawFirmId = base64_decode($_GET['firm']);
			$tpin = base64_decode($_GET['tpin']);
		}
	?>
	<style>
	    
		.signature-container {
		    border: 1px solid #000;
		    padding: 10px;
		    display: inline-block;
		}

		#signature-pad {
		    border: 1px solid #000;
		    width: 100%;
		    height: 100%;
		    cursor: crosshair;
		}

		#loadingIndicator {
		    position: fixed;
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    background: rgba(0, 0, 0, 0.5);
		    z-index: 9999;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		}

		#loadingIndicator .spinner {
		    text-align: center;
		    color: white;
		}

		.spinner-border {
		    width: 3rem;
		    height: 3rem;
		    margin-bottom: 10px;
		}
	</style>
</head>
<body>
	<div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<?php include '../addon_side_nav.php'; ?>
        	<div class="layout-page">
          		<?php include '../addon_top_nav.php'; ?>
          		<div class="content-wrapper">
          			<div class="container-xxl flex-grow-1 container-p-y">
          				<p class="text-primary mb-3">KYC Form for <?php echo $names?></p>
          				<div class="row">
	      					<div class="col-md-12">
	          					<div class="card">
	          						<div class="card-header">
	          							<h5 class="card-title">Client Details</h5>
	          						</div>
	          						<div class="card-body border-top">
	          							
										<div class="partOne my-5 hidden">
											<p>
												KYC FORM<br>
												In order for <strong><?php echo fetchyLawFirmNameByID($lawFirmId)?></strong>  to comply with obligations relating to the standards of doing business, some of which relate to requirements prescribed in the Financial Intelligence Centre Act, fill in this KYC Form and sign on the signatured pad. Once completed, kindly Submit the Form and we will get your information instantly.
											</p>
										</div>
									</div>
								</div>

								<form id="individualFormKYC" method="POST" enctype="multipart/form-data"> 
							      	<div class="card mb-4 mt-4">
								        <div class="card-header border-bottom">
								            <h5 class="card-title">ACCOUNT OWNER(S) HOLDER(S) - INDIVIDUAL</h5>
								        </div>
								        <div class="card-body p-4">
								            <div class="row mb-3">
								                <label for="client-name" class="col-sm-3 col-form-label">Full Name:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="client-name" name="client-name" required value="<?php echo $names?>">
								                    <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
								                    <input type="hidden" name="client_tpin" id="client_tpin" value="<?php echo $tpin?>">
								                    <input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
								                </div>
								            </div>
								            
								            <div class="row mb-3">
								                <label for="marital-status" class="col-sm-3 col-form-label">Marital status:</label>
								                <div class="col-sm-9">
								                    <select class="form-control" id="marital-status" name="marital-status" required>
								                        <option value="">Choose Status</option>
								                        <option value="Single">Single</option>
								                        <option value="Married">Married</option>
								                        <option value="Divorced">Divorced</option>
								                        <option value="Widowed">Widowed</option>
								                        <option value="Seperated">Seperated</option>
								                    </select>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="date-of-birth" class="col-sm-3 col-form-label">Date of birth:</label>
								                <div class="col-sm-9">
								                    <input type="date" class="form-control" id="date-of-birth" name="date-of-birth" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="sex" class="col-sm-3 col-form-label">Sex:</label>
								                <div class="col-sm-9">
								                    <select class="form-control" id="sex" name="sex" required>
								                        <option value="">Choose Gender</option>
								                        <option value="Male">Male</option>
								                        <option value="Female">Female</option>
								                    </select>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="profession" class="col-sm-3 col-form-label">Profession:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="profession" name="profession" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="occupation" class="col-sm-3 col-form-label">Occupation:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="occupation" name="occupation" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="nationality" class="col-sm-3 col-form-label">Nationality:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="nationality" name="nationality" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label class="col-sm-3 col-form-label">Identity type:</label>
								                <div class="col-sm-9">
								                    <div class="form-check form-check-inline">
								                        <input class="form-check-input" type="radio" name="identity-type" id="national-registration-card" value="National Registration card">
								                        <label class="form-check-label" for="national-registration-card">National Registration card</label>
								                    </div>
								                    <div class="form-check form-check-inline">
								                        <input class="form-check-input" type="radio" name="identity-type" id="passport" value="Passport">
								                        <label class="form-check-label" for="passport">Passport</label>
								                    </div>
								                    <div class="form-check form-check-inline">
								                        <input class="form-check-input" type="radio" name="identity-type" id="drivers-licence" value="Driver's licence">
								                        <label class="form-check-label" for="drivers-licence">Driver's licence</label>
								                    </div>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="identification-number" class="col-sm-3 col-form-label">Identification Number:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="identification-number" name="identification-number" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="date-of-issue" class="col-sm-3 col-form-label">Date of issue:</label>
								                <div class="col-sm-9">
								                    <input type="date" class="form-control" id="date-of-issue" name="date-of-issue" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="place-of-issue" class="col-sm-3 col-form-label">Place of issue:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="place-of-issue" name="place-of-issue" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="identification-issued-by" class="col-sm-3 col-form-label">Identification issued by:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="identification-issued-by" name="identification-issued-by" required>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="TPN-no" class="col-sm-3 col-form-label">TPN No.:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="TPN-no" name="TPN-no" readonly value="<?php echo $tpin?>">
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="residential-address" class="col-sm-3 col-form-label">Residential Address:</label>
								                <div class="col-sm-9">
								                    <textarea class="form-control" id="residential-address" name="residential-address" rows="3" required></textarea>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="postal-address" class="col-sm-3 col-form-label">Postal Address:</label>
								                <div class="col-sm-9">
								                    <textarea class="form-control" id="postal-address" name="postal-address" rows="3" required></textarea>
								                </div>
								            </div>

								            <div class="row mb-3">
								                <label for="contact-details" class="col-sm-3 col-form-label">Contact Details:</label>
								                <div class="col-sm-9">
								                    <textarea class="form-control" id="contact-details" name="contact-details" rows="3" required></textarea>
								                </div>
								            </div>
								        </div>
								    </div>

									<div class="card mb-4">
										<div class="card-header border-bottom">
											<h5 class="card-title">ANTI-MONEY LAUNDERING/TERRORISM/CRIMINAL ACTIVITY QUESTIONNAIRE</h5>
										</div>
										<div class="card-body p-4">
										    <div class="form-group mb-3">
										      <label for="politically-exposed-foreign-person" class="form-label">Are you, the Applicant, a politically exposed foreign person? Activity?</label><br>
										      <div class="form-check form-check-inline">
										        <input class="form-check-input" type="radio" required name="politically-exposed-foreign-person" id="politically-exposed-foreign-person-yes" value="Yes">
										        <label class="form-check-label" for="politically-exposed-foreign-person-yes">Yes</label>
										      </div>
										      <div class="form-check form-check-inline">
										        <input class="form-check-input" type="radio" required name="politically-exposed-foreign-person" id="politically-exposed-foreign-person-no" value="No">
										        <label class="form-check-label" for="politically-exposed-foreign-person-no">No</label>
										      </div>
										    </div>
										    <div class="form-group mb-3">
										      <label for="potentially-exposed-to-money-laundering" class="form-label">Are you, the Applicant, potentially exposed to any Money Laundering Activity?</label><br>
										      <div class="form-check form-check-inline">
										        <input class="form-check-input" type="radio" required name="potentially-exposed-to-money-laundering" id="potentially-exposed-to-money-laundering-yes" value="Yes">
										        <label class="form-check-label" for="potentially-exposed-to-money-laundering-yes">Yes</label>
										      </div>
										      <div class="form-check form-check-inline">
										        <input class="form-check-input" type="radio" required name="potentially-exposed-to-money-laundering" id="potentially-exposed-to-money-laundering-no" value="No">
										        <label class="form-check-label" for="potentially-exposed-to-money-laundering-no">No</label>
										      </div>
										    </div>
										    <div class="form-group mb-3">
										      <label for="potentially-exposed-to-any-terrorist-act" class="form-label">Are you, the Applicant, potentially exposed to any terrorist act?</label><br>
										      <div class="form-check form-check-inline">
										        <input class="form-check-input" type="radio" required name="potentially-exposed-to-any-terrorist-act" id="potentially-exposed-to-any-terrorist-act-yes" value="Yes">
										        <label class="form-check-label" for="potentially-exposed-to-any-terrorist-act-yes">Yes</label>
										      </div>
										      <div class="form-check form-check-inline">
										        <input class="form-check-input" type="radio" required name="potentially-exposed-to-any-terrorist-act" id="potentially-exposed-to-any-terrorist-act-no" value="No">
										        <label class="form-check-label" for="potentially-exposed-to-any-terrorist-act-no">No</label>
										      </div>
										  	</div>
										  	<div class="form-group">
											    <label for="criminalActivity" class="form-label">Have you, the Applicant ever been accused, charged, or convicted of any criminal activity?</label><br>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="criminalActivityYes" name="criminalActivity" value="yes">
											        <label class="form-check-label" for="criminalActivityYes">Yes</label>
											    </div>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="criminalActivityNo" name="criminalActivity" value="no">
											        <label class="form-check-label" for="criminalActivityNo">No</label>
											    </div>
											</div>
											<div class="form-group">
											    <label for="terroristAssociation" class="form-label">Has your country of origin or residence ever been associated with any terrorist activity?</label><br>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristAssociationYes" name="terroristAssociation" value="yes">
											        <label class="form-check-label" for="terroristAssociationYes">Yes</label>
											    </div>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristAssociationNo" name="terroristAssociation" value="no">
											        <label class="form-check-label" for="terroristAssociationNo">No</label>
											    </div>
											</div>

											<div class="form-group mb-3">
											    <label for="terroristDealings" class="form-label">Have you the Applicant ever had any dealings with persons from countries that have been linked to terrorist activity?</label><br>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristDealingsYes" name="terroristDealings" value="yes">
											        <label class="form-check-label" for="terroristDealingsYes">Yes</label>
											    </div>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristDealingsNo" name="terroristDealings" value="no">
											        <label class="form-check-label" for="terroristDealingsNo">No</label>
											    </div>
											</div>

											<div class="form-group mb-3">
										    <label class=" form-label">Source of Funds:</label><br>
										    <div class="">
										        <div class="form-check form-check-inline">
										            <input class="form-check-input" type="radio" name="source_of_funds" id="business" value="Business" required>
										            <label class="form-check-label" for="business">Business</label>
										        </div>
										        <div class="form-check form-check-inline">
										            <input class="form-check-input" type="radio" name="source_of_funds" id="employment" value="Employment" required>
										            <label class="form-check-label" for="employment">Employment</label>
										        </div>
										        <div class="form-check form-check-inline">
										            <input class="form-check-input" type="radio" name="source_of_funds" id="others" value="Others">
										            <label class="form-check-label" for="others" required>Others</label>
										        </div>
										        <!-- Hidden input field -->
										        <div id="others-input" class="mt-3" style="display: none;">
										            <label for="other_details" class="form-label">Please specify:</label>
										            <input type="text" id="other_details" name="other_details" class="form-control" placeholder="Specify here">
										        </div>
										    </div>
										</div>


											<h4 class="text-center">Signature</h4>
											<div class="row">
												<div class="mb-3 col-md-6">
											    	<label for="signature_names" class="mb-2">Client Names</label>
											    	<input type="text" class="form-control" id="signature_names" name="signature_names" required value="<?php echo $names?>">
											  	</div>
											  	
											  	<div class="mb-3 col-md-6">
											    	<label for="date" class="mb-2">Date</label>
											    	<input type="date" class="form-control" id="signature_date" name="signature_date" required>
											  	</div>
											</div>
										</div>
									</div>
									<div class="d-flex justify-content-end">
										<button type="button" class="btn btn-success me-2" id="save-btn"><i class="bi bi-floppy"></i> Save</button>
										<button type="submit" class="btn btn-primary me-2" id="submitBtn">Submit Form</button>
									</div>
							    </form>
							</div>
	          			</div>
	          					
										<!-- Processing Modal -->
						<div id="loadingIndicator" style="display:none;">
						    <div class="spinner">
						        <div class="spinner-border text-primary" role="status">
						            <span class="sr-only">Loading...</span>
						        </div>
						        <p>Processing...</p>
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
		// Ensure only one checkbox is checked
		document.querySelectorAll('input[name="typeOfBusiness"]').forEach(checkbox => {
		  	checkbox.addEventListener('change', function() {
			    if (this.checked) {
			      	document.querySelectorAll('input[name="typeOfBusiness"]').forEach(box => {
			        	if (box !== this) box.checked = false;
			      	});
			    }
		  	});
		});
		

		const form = document.getElementById('individualFormKYC');
		const formFields = Array.from(form.querySelectorAll('input, textarea, select, table'));
		const submitBtn = document.querySelector('button[type="submit"]');

		// Function to save form data to localStorage
		function saveFormData() {
		    $('#loadingIndicator').show();
		    const formData = {};
		    formFields.forEach(field => {
		        formData[field.name] = field.value;
		    });

		    localStorage.setItem('formData', JSON.stringify(formData));
		    $('#loadingIndicator').hide();
		    sweetSuccess("Data saved");
		}


		// Event listeners
		document.getElementById('save-btn').addEventListener('click', saveFormData);
		// window.addEventListener('load', loadFormData);

		/*
		$(document).ready(function() {
		    $('#individualFormKYC').on('submit', function(e) {
		        e.preventDefault();
		        var formData = new FormData(this);
		        $.ajax({
		            type: 'POST',
		            url: 'base/createIndividualKYC',
		            data: formData,
		            processData: false,
		            contentType: false,
		            beforeSend: function() {
		                // Show loading indicator
		                $('#loadingIndicator').show();
		            },
		            success: function(response) {
		                // Hide loading indicator
		                $('#loadingIndicator').hide();
		                sweetSuccess(response);
		                localStorage.clear();
		                // location.reload();
		            },
		            error: function(xhr, status, error) {
		                // Hide loading indicator
		                $('#loadingIndicator').hide();
		                console.error(xhr);
		                sweetError('An error occurred while processing the request.');
		            }
		        });
		    });
		});
		*/
		$(document).ready(function() {
		    // Handle changes in the radio buttons
		    $('input[name="source_of_funds"]').on('change', function() {
		        const $othersInput = $('#others-input');
		        const $otherDetails = $('#other_details');
		        
		        if ($(this).val() === 'Others' && $(this).is(':checked')) {
		            $othersInput.show();
		            $otherDetails.attr('required', true);
		        } else {
		            $othersInput.hide();
		            $otherDetails.removeAttr('required');
		        }
		    });

		    $('#individualFormKYC').on('submit', function(e) {
		        e.preventDefault();
		        
		        // Check if a radio button is selected and, if "Others", if the other details are filled
		        const $selectedOption = $('input[name="source_of_funds"]:checked');
		        const otherDetailsValue = $('#other_details').val().trim();
		        
		        if (!$selectedOption.length) {
		            alert('Please select a source of funds.');
		            return; // Prevent form submission
		        } 
		        
		        if ($selectedOption.val() === 'Others' && !otherDetailsValue) {
		            alert('Please specify the source of funds.');
		            return; // Prevent form submission
		        }
		        
		        // Prepare form data
		        var formData = new FormData(this);
		        
		        $.ajax({
		            type: 'POST',
		            url: 'base/createIndividualKYC',
		            data: formData,
		            processData: false,
		            contentType: false,
		            beforeSend: function() {
		                // Show loading indicator
		                $('#loadingIndicator').show();
		            },
		            success: function(response) {
		                // Hide loading indicator
		                $('#loadingIndicator').hide();
		                sweetSuccess(response);
		                localStorage.clear();
		                // location.reload();
		            },
		            error: function(xhr, status, error) {
		                // Hide loading indicator
		                $('#loadingIndicator').hide();
		                console.error(xhr);
		                sweetError('An error occurred while processing the request.');
		            }
		        });
		    });
		});

		function closePage() {
		  	window.close();
		}

		document.getElementById('close-page').addEventListener('click', closePage);
	</script>
</body>
</html>