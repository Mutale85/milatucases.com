<?php 
	include "includes/db.php";

	if (isset($_GET['clientId'])) {
		$clientId = decrypt($_GET['clientId']);
		$names = decrypt($_GET['names']);
		$email = decrypt($_GET['email']);
		$lawFirmId = decrypt($_GET['firm']);
		$tpin = fetchClientsTpinById($clientId, $lawFirmId);
	}
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter"><head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title><?php echo $names ?> KYC Form</title>

    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="sampleLogo.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
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
        	<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	          <div class="app-brand demo">
	            <a href="index.html" class="app-brand-link">
	              <span class="app-brand-logo demo">
	                <img src="sampleLogo.png" class="img-fluid" alt="logo" style="width: 50px; height: 50px; border-radius:50%">
	              </span>
	              <span class="app-brand-text demo menu-text fw-bolder ms-2" title="Miyanda Williams Legal Practitioners"></span>
	            </a>

	            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
	              <i class="bx bx-chevron-left bx-sm align-middle"></i>
	            </a>
	          </div>

          		<div class="menu-inner-shadow"></div>

	          <ul class="menu-inner py-1">
	            <!-- Dashboard -->
	            <li class="menu-item">
	              <a href="#" class="menu-link">
	                <i class="menu-icon tf-icons bx bx-home-circle"></i>
	                <div data-i18n="Analytics">KYC Form for <?php echo $names?></div>
	              </a>
	            </li>
	          </ul>
        	</aside>
	    	<div class="layout-page">
	      		<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
		            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
		              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
		                <i class="bx bx-menu bx-sm"></i>
		              </a>
		            </div>

		            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
		              <ul class="navbar-nav flex-row align-items-center ms-auto">
		                <!-- Place this tag where you want the button to render. -->
		                <li class="nav-item lh-1 me-3">
		                 	<?php echo $names?>
		                </li>
		                <li class="nav-item navbar-dropdown dropdown-user dropdown">
		                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
		                    <div class="avatar avatar-online">
		                      <img src="<?php echo get_gravatar($email)?>" alt class="w-px-40 h-auto rounded-circle" />
		                    </div>
		                  </a>
		                  <ul class="dropdown-menu dropdown-menu-end">
		                    <li>
		                      <a class="dropdown-item" href="#">
		                        <div class="d-flex">
		                          <div class="flex-shrink-0 me-3">
		                            <div class="avatar avatar-online">
		                              	<img src="<?php echo get_gravatar($email)?>" alt class="w-px-40 h-auto rounded-circle" />
		                            </div>
		                          </div>
		                          <div class="flex-grow-1">
		                            <span class="fw-semibold d-block"><?php echo $tpin?></span>
		                          </div>
		                        </div>
		                      </a>
		                    </li>
		                  </ul>
		                </li>
		              </ul>
		            </div>
	          	</nav>
	      		<div class="content-wrapper">
	      			<div class="container-xxl flex-grow-1 container-p-y">
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
												In order for <strong><?php echo fetchyLawFirmNameByID($lawFirmId)?></strong> to comply with obligations relating to the standards of doing business, some of which relate to requirements prescribed in the Financial Intelligence Centre Act, fill in this KYC Form and sign on the signatured pad. Once completed, kindly Submit the Form and we will get your information instantly.
											</p>
										</div>
									</div>
								</div>

								<!-- <form id="individualFormKYC" method="POST" enctype="multipart/form-data"> 
							      	<div class="card mb-4 mt-4">
							      		<div class="card-header border-bottom">
								        	<h5 class="card-title">ACCOUNT OWNER(S) HOLDER(S) - INDIVIDUAL</h5>
								        </div>
								        <div class="card-body p-4">
									        <div class="form-group">
									          	<label for="client-name" class="form-label"> Full Name:</label>
									          	<input type="text" class="form-control" id="client-name" name="client-name" required value="<?php echo $names?>">
									          	<input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
									          	<input type="hidden" name="client_tpin" id="client_tpin" value="<?php echo $tpin?>">
									          	<input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
									        </div>
									        
										    <div class="form-group mb-3">
										      	<label for="marital-status" class="form-label">Marital status:</label>
										      	<select type="text" class="form-control" id="marital-status" name="marital-status" required>
										      		<option value="">Choose Status</option>
										      		<option value="Single">Single</option>
										      		<option value="Married">Married</option>
										      		<option value="Divorced">Divorced</option>
										      		<option value="Widowed">Widowed</option>
										      		<option value="Seperated">Seperated</option>
										      	</select>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="date-of-birth" class="form-label">Date of birth:</label>
										      	<input type="date" class="form-control" id="date-of-birth" name="date-of-birth" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="sex" class="form-label">Sex:</label>
										      	<select type="text" class="form-control" id="sex" name="sex" required>
										      		<option value="">Choose Gender</option>
										      		<option value="Male">Male</option>
										      		<option value="Female">Female</option>
										      	</select>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="profession" class="form-label">Profession:</label>
										      	<input type="text" class="form-control" id="profession" name="profession" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="occupation" class="form-label">Occupation:</label>
										      	<input type="text" class="form-control" id="occupation" name="occupation" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="nationality" class="form-label">Nationality:</label>
										      	<input type="text" class="form-control" id="nationality" name="nationality" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="identity-type" class="form-label">Identity type:</label>
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
										    <div class="form-group mb-3">
										      	<label for="identification-number" class="form-label">Identification Number:</label>
										      	<input type="text" class="form-control" id="identification-number" name="identification-number" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="date-of-issue" class="form-label">Date of issue:</label>
										      	<input type="date" class="form-control" id="date-of-issue" name="date-of-issue" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="place-of-issue" class="form-label">Place of issue:</label>
										      	<input type="text" class="form-control" id="place-of-issue" name="place-of-issue" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="identification-issued-by" class="form-label">Identification issued by:</label>
										      	<input type="text" class="form-control" id="identification-issued-by" name="identification-issued-by" required>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="TPN-no" class="form-label">TPN No.:</label>
										      	<input type="text" class="form-control" id="TPN-no" name="TPN-no" required value="<?php echo $tpin?>">
										    </div>
										    <div class="form-group mb-3">
										      	<label for="residential-address" class="form-label">Residential Address (Property Nmber, Street Name, Chief):</label>
											    <textarea class="form-control" id="residential-address" name="residential-address" rows="3" required></textarea>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="postal-address" class="form-label">Postal Address :</label>
											    <textarea class="form-control" id="postal-address" name="postal-address" rows="3" required></textarea>
										    </div>
										    <div class="form-group mb-3">
										      	<label for="contact-details" class="form-label">Contact Details:</label>
											    <textarea class="form-control" id="contact-details" name="contact-details" rows="3" required></textarea>
										    </div>
										</div>
								   	</div>

									<div class="card mb-4">
										<div class="card-header border-bottom">
											<h5 class="card-title">ANTI-MONEY LAUNDERING/TERRORISM/CRIMINAL ACTIVITY QUESTIONNAIRE</h5>
										</div>
										<div class="card-body p-4">
										    <div class="form-group mb-3">
										      <label for="politically-exposed-foreign-person" class="form-label">Are you, the Applicant, a politically exposed foreign person? Activity?</label>
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
										      <label for="potentially-exposed-to-money-laundering" class="form-label">Are you, the Applicant, potentially exposed to any Money Laundering Activity?</label>
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
										      <label for="potentially-exposed-to-any-terrorist-act" class="form-label">Are you, the Applicant, potentially exposed to any terrorist act?</label>
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
											    <label for="criminalActivity" class="form-label">Have you, the Applicant ever been accused, charged, or convicted of any criminal activity?</label>
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
											    <label for="terroristAssociation" class="form-label">Has your country of origin or residence ever been associated with any terrorist activity?</label>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristAssociationYes" name="terroristAssociation" value="yes">
											        <label class="form-check-label" for="terroristAssociationYes">Yes</label>
											    </div>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristAssociationNo" name="terroristAssociation" value="no">
											        <label class="form-check-label" for="terroristAssociationNo">No</label>
											    </div>
											</div>

											<div class="form-group">
											    <label for="terroristDealings" class="form-label">Have you the Applicant ever had any dealings with persons from countries that have been linked to terrorist activity?</label>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristDealingsYes" name="terroristDealings" value="yes">
											        <label class="form-check-label" for="terroristDealingsYes">Yes</label>
											    </div>
											    <div class="form-check form-check-inline">
											        <input class="form-check-input" type="radio" required id="terroristDealingsNo" name="terroristDealings" value="no">
											        <label class="form-check-label" for="terroristDealingsNo">No</label>
											    </div>
											</div>
											<h4 class="text-center">Signature</h4>
											<div class="row">
												<div class="mb-3 col-md-4">
											    	<label for="signature_names" class="mb-2">Client Names</label>
											    	<input type="text" class="form-control" id="signature_names" name="signature_names" required>
											  	</div>
											  	<div class="mb-3 col-md-4">
											    	<label for="signature" class="mb-2 text-center">Signature</label>
												    <div class="signature-container">
													    <canvas id="signature-pad" width="400" height="200">Sign Here</canvas>
													    <input type="hidden" id="signature-data" name="signature-data">
													    <button type="button" id="clear-signature">Clear</button>
													    <button type="button" id="save-signature">Save Signature</button>
													</div>
											  	</div>
											  	<div class="mb-3 col-md-4">
											    	<label for="date" class="mb-2">Date</label>
											    	<input type="date" class="form-control" id="signature_date" name="signature_date" required>
											  	</div>
											</div>
										</div>
									</div>
									<div class="d-flex justify-content-end">
										<button type="button" class="btn btn-success me-2" id="save-btn"><i class="bi bi-floppy"></i> Save</button>
										<button type="submit" class="btn btn-primary me-2" id="submitBtn">Finish and Submit Form</button>
										<button type="button" class="btn btn-danger btn-sm me-2" id="close-page"><i class="bi bi-x"></i> Close </button>
									</div>
							    </form> -->
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

											<div class="form-group">
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
											<h4 class="text-center">Signature</h4>
											<div class="row">
												<div class="mb-3 col-md-4">
											    	<label for="signature_names" class="mb-2">Client Names</label>
											    	<input type="text" class="form-control" id="signature_names" name="signature_names" required>
											  	</div>
											  	<div class="mb-3 col-md-4">
											    	<label for="signature" class="mb-2 text-center">Signature</label>
												    <div class="signature-container">
													    <canvas id="signature-pad" width="400" height="200">Sign Here</canvas>
													    <input type="hidden" id="signature-data" name="signature-data">
													    <button type="button" id="clear-signature">Clear</button>
													    <button type="button" id="save-signature">Save Signature</button>
													</div>
											  	</div>
											  	<div class="mb-3 col-md-4">
											    	<label for="date" class="mb-2">Date</label>
											    	<input type="date" class="form-control" id="signature_date" name="signature_date" required>
											  	</div>
											</div>
										</div>
									</div>
								    <div class="d-flex justify-content-end">
								        <button type="button" class="btn btn-success me-2" id="save-btn"><i class="bi bi-floppy"></i> Save</button>
								        <button type="submit" class="btn btn-primary me-2" id="submitBtn">Finish and Submit Form</button>
								        <button type="button" class="btn btn-danger btn-sm me-2" id="close-page"><i class="bi bi-x"></i> Close </button>
								    </div>
								</form>
							</div>
	          			</div>
	          					
										<!-- Processing Modal -->
						<div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-body">
						        Processing...
						      </div>
						    </div>
						  </div>
						</div>
	          		</div>
	          	</div>
	      	</div>
	      	<div class="content-backdrop fade"></div>
	      	<div class="layout-overlay layout-menu-toggle"></div>
	      	<div id="loadingIndicator" style="display:none;">
			    <div class="spinner">
			        <div class="spinner-border text-primary" role="status">
			            <span class="sr-only">Loading...</span>
			        </div>
			        <p>Processing...</p>
			    </div>
			</div>
      	</div>
   	</div>
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- <script type="text/javascript" src="../dist/controls/corporate.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
		    alert("Data saved");
		}

		
		// Event listeners
		document.getElementById('save-btn').addEventListener('click', saveFormData);


		document.addEventListener('DOMContentLoaded', function() {
		    const canvas = document.getElementById('signature-pad');
		    resizeCanvas(canvas);
		    const signaturePad = new SignaturePad(canvas);

		    document.getElementById('clear-signature').addEventListener('click', () => {
		        signaturePad.clear();
		        document.getElementById('signature-data').value = '';
		        alert("Signature has been cleared.");
		    });

		    document.getElementById('save-signature').addEventListener('click', () => {
		        if (!signaturePad.isEmpty()) {
		            const dataUrl = signaturePad.toDataURL();
		            document.getElementById('signature-data').value = dataUrl;
		            // Optionally, display the signature image somewhere
		            const img = document.createElement('img');
		            img.src = dataUrl;
		            // document.body.appendChild(img); // Replace this with the desired location
		            alert("Signature has been saved.");
		        } else {
		            alert("Please provide a signature first.");
		        }
		    });

		    window.addEventListener('resize', () => resizeCanvas(canvas));
		});

		function resizeCanvas(canvas) {
		    const ratio = Math.max(window.devicePixelRatio || 1, 1);
		    canvas.width = canvas.offsetWidth * ratio;
		    canvas.height = canvas.offsetHeight * ratio;
		    canvas.getContext('2d').scale(ratio, ratio);
		}

		function sweetError(message){
			Swal.fire({
				position: "top-end",
			  	icon: "error",
			  	title: "Oops...",
			  	text: message
			});
		}

		function sweetBeforeSend(message){
			Swal.fire({
			  position: "top-end",
			  icon: "success",
			  title: "Your work has been saved",
			  showConfirmButton: false,
			  timer: 1500
			});
		}

		$(document).ready(function() {
		    $('#individualFormKYC').on('submit', function(e) {
		        e.preventDefault();
		        var formData = new FormData(this);
		        $.ajax({
		            type: 'POST',
		            url: 'parsers/processIndividualKYC',
		            data: formData,
		            processData: false,
		            contentType: false,
		            beforeSend: function() {
		                // Show loading indicator
		                $('#loadingIndicator').show();
		            },
		            success: function(response) {
		                // Hide loading indicator
		                if(response.includes("Form submitted successfully and email has been sent")){
		                	sweetBeforeSend(response);
		                }else{
		                	sweetError(response);
		                }
		                $('#loadingIndicator').hide();
		                alert(response);
		            },
		            error: function(xhr, status, error) {
		                // Hide loading indicator
		                $('#loadingIndicator').hide();
		                sweetError(error);
		                alert('An error occurred while processing the request.');
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