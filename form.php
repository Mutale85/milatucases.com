<?php 
	include "includes/db.php";

	if ($_GET['clientId']) {
		$clientId = base64_decode($_GET['clientId']);
		$busiName = base64_decode($_GET['busiName']);
		$names = base64_decode($_GET['names']);
		$email = base64_decode($_GET['email']);
		$lawFirmId = base64_decode($_GET['firm']);
		// $tpin = base64_decode($_GET['tpin']);
		$tpin = fetchClientsTpinById($clientId, $lawFirmId);

	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title><?php echo $busiName ?> Corporate's Clients Form</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="sampleLogo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Page CSS -->

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
	                <div data-i18n="Analytics">KYC Form for <?php echo $busiName?></div>
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
		                 	<?php echo $busiName?>
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
		                            <!-- <small class="text-muted">Admin</small> -->
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
	          							<h5 class="card-title">Client Business</h5>
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

							    <form id="corporateFormKYC" method="POST" enctype="multipart/form-data">
								    <div class="card mb-4 mt-4">
								        <div class="card-header border-bottom">
								            <h5 class="card-title">ACCOUNT OWNER(S) HOLDER(S) - BUSINESS ENTITY</h5>
								        </div>
								        <div class="card-body p-4">
								            <div class="row mb-3">
								                <label for="client-name" class="col-form-label col-sm-3">Business Name:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="client-name" name="client-name" required value="<?php echo $busiName?>">
								                    <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
								                    <input type="hidden" name="client_tpin" id="client_tpin" value="<?php echo $tpin?>">
								                    <input type="hidden" name="clientId" id="clientId" value="<?php echo $clientId?>">
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label class="col-sm-3 col-form-label" for="basic-default-name">Registration Number:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="registration-number" name="registration-number" placeholder="Enter Registration Number" required />
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="date-of-incorporation" class="col-form-label col-sm-3">Date of Incorporation:</label>
								                <div class="col-sm-9">
								                    <input type="date" class="form-control" id="date-of-incorporation" name="date-of-incorporation" required>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="place-of-incorporation" class="col-form-label col-sm-3">Country of Incorporation:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="place-of-incorporation" name="place-of-incorporation" required>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="business-type" class="col-form-label col-sm-3">Business Type:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="business-type" name="business-type" required>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="tax-identification-number" class="col-form-label col-sm-3">Tax Identification Number (TPIN):</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="tax-identification-number" name="tax-identification-number" required value="<?php echo $tpin?>">
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="registered-office-address" class="col-form-label col-sm-3">Registered Office Address:</label>
								                <div class="col-sm-9">
								                    <textarea class="form-control" id="registered-office-address" name="registered-office-address" rows="3" required></textarea>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="mailing-address" class="col-form-label col-sm-3">Mailing Address:</label>
								                <div class="col-sm-9">
								                    <textarea class="form-control" id="mailing-address" name="mailing-address" rows="3" required></textarea>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="contact-person" class="col-form-label col-sm-3">Contact Person:</label>
								                <div class="col-sm-9">
								                    <input type="text" class="form-control" id="contact-person" name="contact-person" required>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="contact-number" class="col-form-label col-sm-3">Contact Number:</label>
								                <div class="col-sm-9">
								                    <input type="tel" class="form-control" id="contact-number" name="contact-number" required>
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="email" class="col-form-label col-sm-3">Email:</label>
								                <div class="col-sm-9">
								                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo $email?>">
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="auditors" class="col-form-label col-sm-3">Name and address of Auditors:</label>
								                <div class="col-sm-9">
								                    <input type="text" id="auditors" name="auditors" class="form-control" placeholder="Enter the name and address of your auditors">
								                </div>
								            </div>
								            <div class="row mb-3">
								                <label for="financial-year-end" class="col-form-label col-sm-3">Financial year end:</label>
								                <div class="col-sm-9">
								                    <input type="text" id="financial-year-end" name="financial-year-end" class="form-control" placeholder="Enter your financial year end">
								                </div>
								            </div>
								        </div>
								    </div>

								    <div class="card mb-4">
								        <div class="card-header border-bottom">
								            <table class="table table-borderless">
								                <tr>
								                    <th><h5 class="mb-4">DETAILS OF DIRECTORS/TRUSTEES/SETTLEMENT/BENEFICIARY</h5></th>
								                    <td align="right"><button type="button" class="btn btn-primary btn-sm add-row"><i class="bi bi-person-plus"></i> Add More</button></td>
								                </tr>
								            </table>
								        </div>
								        <div class="card-body p-4">
								            <div class="row table-responsive mb-3">
								                <table id="directors-table" class="table">
								                    <thead>
								                        <tr>
								                            <th>Full Name</th>
								                            <th>Gender</th>
								                            <th>Marital Status</th>
								                            <th>Nationality</th>
								                            <th>Occupation</th>
								                            <th>Identity Type & No.</th>
								                            <th>Date & place of issue</th>
								                            <th>Residential address</th>
								                            <th>Contact details (mobile/email)</th>
								                            <th></th>
								                        </tr>
								                    </thead>
								                    <tbody>
								                        <tr>
								                            <td><input type="text" name="d-full-name[]" class="form-control"></td>
								                            <td><input type="text" name="d-gender[]" class="form-control"></td>
								                            <td><input type="text" name="d-marital-status[]" class="form-control"></td>
								                            <td><input type="text" name="d-nationality[]" class="form-control"></td>
								                            <td><input type="text" name="d-occupation[]" class="form-control"></td>
								                            <td><input type="text" name="d-identity-type[]" class="form-control"></td>
								                            <td><input type="text" name="d-date-place-issue[]" class="form-control"></td>
								                            <td><input type="text" name="d-residential-address[]" class="form-control"></td>
								                            <td><input type="text" name="d-contact-details[]" class="form-control"></td>
								                            <td><button type="button" class="btn btn-danger remove-row btn-sm">Remove</button></td>
								                        </tr>
								                    </tbody>
								                </table>
								            </div>
								        </div>
								    </div>

								    <div class="card mb-4">
										<div class="card-header border-bottom">
											<h5 class="card-title">DETAILS OF PERSON WITH AUTHORITY TO CONDUCT TRANSACTION ON BEHALF OF BUSINESS ENTITY/TRUST</h5>
										</div>
										<div class="card-body p-4">
											<div class="row mb-3">
												<label for="name" class="col-form-label col-sm-3">Name:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="name" name="name" required>
												</div>
											</div>
											<div class="row mb-3">
												<label for="marital-status" class="col-form-label col-sm-3">Marital status:</label>
												<div class="col-sm-9">
													<select type="text" class="form-control" id="marital-status" name="marital-status" required>
														<option value="">Select</option>
														<option value="Married">Married</option>
														<option value="Single">Single</option>
														<option value="Divorced">Divorced</option>
														<option value="Widowed">Widowed</option>
													</select>
												</div>
											</div>
											<div class="row mb-3">
												<label for="date-of-birth" class="col-form-label col-sm-3">Date of birth:</label>
												<div class="col-sm-9">
													<input type="date" class="form-control" id="date-of-birth" name="date-of-birth" required>
												</div>
											</div>
											<div class="row mb-3">
												<label for="sex" class="col-form-label col-sm-3">Gender:</label>
												<div class="col-sm-9">
													<select type="text" class="form-control" id="sex" name="sex" required>
														<option value="">Select</option>
														<option value="Male">Male</option>
														<option value="Female">Female</option>
													</select>
												</div>
											</div>
											<div class="row mb-3">
												<label for="profession" class="col-form-label col-sm-3">Profession:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="profession" name="profession" required>
												</div>
											</div>
											<div class="row mb-3">
												<label for="occupation" class="col-form-label col-sm-3">Occupation:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="occupation" name="occupation" required>
												</div>
											</div>
											<div class="row mb-3">
												<label for="nationality" class="col-form-label col-sm-3">Nationality:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="nationality" name="nationality" required>
												</div>
											</div>
											<div class="row mb-3">
												<label for="identity-type" class="col-form-label col-sm-3">Identity type:</label>
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
												<label for="identification-number" class="col-form-label col-sm-3">Identification Number:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="identification-number" name="identification-number">
												</div>
											</div>
											<div class="row mb-3">
												<label for="date-of-issue" class="col-form-label col-sm-3">Date of issue:</label>
												<div class="col-sm-9">
													<input type="date" class="form-control" id="date-of-issue" name="date-of-issue">
												</div>
											</div>
											<div class="row mb-3">
												<label for="place-of-issue" class="col-form-label col-sm-3">Place of issue:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="place-of-issue" name="place-of-issue">
												</div>
											</div>
											<div class="row mb-3">
												<label for="identification-issued-by" class="col-form-label col-sm-3">Identification issued by:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="identification-issued-by" name="identification-issued-by">
												</div>
											</div>
											<div class="row mb-3">
												<label for="TPN-no" class="col-form-label col-sm-3">TPN No.:</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" id="TPN-no" name="TPN-no">
												</div>
											</div>
											<div class="row mb-3">
												<label for="contact-details" class="col-form-label col-sm-3">Contact details (Telephone, mobile, Email, website):</label>
												<div class="col-sm-9">
													<textarea class="form-control" id="contact-details" name="contact-details" rows="3"></textarea>
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
											    <label for="representative_name" class="mb-2">Representative's name</label>
											    <input type="text" class="form-control" id="representative_name" name="representative_name">
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
											    <input type="date" class="form-control" id="date" name="date">
											  </div>

											  <div class="mb-3 col-md-12">
											    <label for="compliance_officer_name" class="mb-2">Compliance Officer's Name</label>
											    <input type="text" class="form-control" id="compliance_officer_name" name="compliance_officer_name">
											  </div>
											</div>
										</div>
									</div>

								    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
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

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- <script type="text/javascript" src="../dist/controls/corporate.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
		document.querySelectorAll('input[name="typeOfBusiness"]').forEach(checkbox => {
		  checkbox.addEventListener('change', function() {
		    if (this.checked) {
		      document.querySelectorAll('input[name="typeOfBusiness"]').forEach(box => {
		        if (box !== this) box.checked = false;
		      });
		    }
		  });
		});
		
		const form = document.getElementById('corporateFormKYC');
		const formFields = Array.from(form.querySelectorAll('input, textarea, select, table'));
		const submitBtn = document.querySelector('button[type="submit"]');
		const table = document.getElementById('directors-table');
		const tbody = table.getElementsByTagName('tbody')[0];

		// Function to save form data to localStorage
		function saveFormData() {
		    $('#loadingIndicator').show();
		    const formData = {};
		    formFields.forEach(field => {
		        formData[field.name] = field.value;
		    });

		    // Save table row data
		    const tableRows = Array.from(document.querySelectorAll('#directors-table tbody tr'));
		    formData['tableRows'] = tableRows.map(row => {
		        const inputs = Array.from(row.querySelectorAll('input'));
		        return inputs.map(input => input.value);
		    });

		    localStorage.setItem('formData', JSON.stringify(formData));
		    $('#loadingIndicator').hide();
		    alert("Data saved");
		}

		// Function to load form data from localStorage
		function loadFormData() {
		    const formData = JSON.parse(localStorage.getItem('formData'));
		    if (formData) {
		        // Load regular form fields
		        formFields.forEach(field => {
		            if (field.name in formData && formData[field.name] !== "") {
		                field.value = formData[field.name];
		            }
		        });

		        // Load table rows
		        const savedRows = formData['tableRows'];
		        if (savedRows) {
		            savedRows.forEach(savedRow => {
		                const newRow = table.insertRow(-1);
		                newRow.innerHTML = `
		                    <td><input type="text" name="d-full-name[]" class="" value="${savedRow[0]}"></td>
		                    <td><input type="text" name="d-gender[]" class="" value="${savedRow[1]}"></td>
		                    <td><input type="text" name="d-marital-status[]" class="" value="${savedRow[2]}"></td>
		                    <td><input type="text" name="d-nationality[]" class="" value="${savedRow[3]}"></td>
		                    <td><input type="text" name="d-occupation[]" class="" value="${savedRow[4]}"></td>
		                    <td><input type="text" name="d-identity-type[]" class="" value="${savedRow[5]}"></td>
		                    <td><input type="text" name="d-date-place-issue[]" class="" value="${savedRow[6]}"></td>
		                    <td><input type="text" name="d-residential-address[]" class="" value="${savedRow[7]}"></td>
		                    <td><input type="text" name="d-contact-details[]" class="" value="${savedRow[8]}"></td>
		                    <td><button type="button" class="btn btn-danger remove-row btn-sm">Remove</button></td>
		                `;

		                // Add event listener for the newly created "Remove" button
		                newRow.querySelector('.remove-row').addEventListener('click', () => {
		                    newRow.remove();
		                });
		            });
		        }
		    }
		}

		// Event listeners
		document.getElementById('save-btn').addEventListener('click', saveFormData);
		window.addEventListener('load', loadFormData);

		// Add row to the table
		document.querySelector('.add-row').addEventListener('click', () => {
		    const newRow = table.insertRow(-1);
		    newRow.innerHTML = `
		        <td><input type="text" name="d-full-name[]" class=""></td>
		        <td><input type="text" name="d-gender[]" class=""></td>
		        <td><input type="text" name="d-marital-status[]" class=""></td>
		        <td><input type="text" name="d-nationality[]" class=""></td>
		        <td><input type="text" name="d-occupation[]" class=""></td>
		        <td><input type="text" name="d-identity-type[]" class=""></td>
		        <td><input type="text" name="d-date-place-issue[]" class=""></td>
		        <td><input type="text" name="d-residential-address[]" class=""></td>
		        <td><input type="text" name="d-contact-details[]" class=""></td>
		        <td><button type="button" class="btn btn-danger remove-row btn-sm">Remove</button></td>
		    `;

		    // Add event listener for the newly created "Remove" button
		    newRow.querySelector('.remove-row').addEventListener('click', () => {
		        newRow.remove();
		    });
		});

		// Signatures

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

		$(document).ready(function() {
		    $('#corporateFormKYC').on('submit', function(e) {
		        e.preventDefault();

		        var formData = new FormData(this);

		        $.ajax({
		            type: 'POST',
		            url: 'parsers/processCorporateKYC',
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
		                alert(response);
		            },
		            error: function(xhr, status, error) {
		                // Hide loading indicator
		                $('#loadingIndicator').hide();
		                console.error(xhr);
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