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
	<title>Corporate Clients</title>
	<?php include '../addon_header.php';
		$lawFirmId = $_SESSION['parent_id']; 
		if(isset($_GET['caseId'])){
			$caseId = $_GET['caseId'];
			$caseNo = decrypt($_GET['caseNo']);
			$clientId  = decrypt($_GET['clientId']);
			$caseNumber = fetchCaseNoById($caseId);
			$client_tpin = getClientTpinById($clientId, $lawFirmId);
		}
	?>
	<link rel="stylesheet" type="text/css" href="../assets/custom/caseDetails.css">
	<style>
		.modal-fullscreen {
		    max-width: 100%;
		    margin: 0;
		    height: 100%;
		}

		.modal-fullscreen .modal-content {
		    height: 100%;
		    border-radius: 0;
		}

		.modal-fullscreen .modal-body {
		    overflow-y: auto;
		}
		.modal-body {
		    position: relative;
		    flex: 1 1 auto;
		    padding: 1.5rem;
		    background: #fff;
		}
		.modal-footer {
			background: #fff;
		}

	    .editor-toolbar {
	      background-color: #f1f1f1;
	      padding: 10px;
	      border-bottom: 1px solid #ddd;
	    }

	    .editor-toolbar button {
	      background-color: transparent;
	      border: none;
	      color: #333;
	      cursor: pointer;
	      padding: 5px 10px;
	    }

	    .editor-toolbar button:hover {
	      background-color: #e6e6e6;
	    }

	    .editor-toolbar button.active {
	      background-color: #ddd;
	    }

	    .editor-content {
	      padding: 10px;
	      border: 1px solid #ddd;
	      min-height: 200px;
	      resize: vertical;
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
					    <div class="row">
					    	<div class="col-md-12">
					    		<ul class="list-group list-group-flush mb-3">
					    			<li class="list-group-item"><a href="./">Home</a> / Cases / <?php echo getClientNameById($clientId, $lawFirmId)?></li>
					    		</ul>
					    	</div>
					        <div class="col">
					            <div class="card">
					            		
					                <?php echo displayCaseDetailsById($caseId, $lawFirmId)?>
					                <div class="card-header">
					                	<h5 class="mb-3 card-title">Milestones <i class="bi bi-graph"></i></h5>
					                </div>
					                <div class="timeline">
						                <div class="card-body" id="milestoneAdded"></div>
						            	</div>
					                <div class="card-footer">
														<div class="modal fade" id="addNewCaseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
														    <div class="modal-dialog modal-fullscreen bg-primary">
														        <div class="modal-content">
														            <div class="modal-header">
														                <h5 class="modal-title" id="exampleModalLabel">Edit Case</h5>
														                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
														            </div>

														            <form id="addCaseForm" method="POST" enctype="multipart/form-data">
														            	<div class="modal-body">
															                <div class="row">
															                	<!-- Select Client -->
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
															                    <div class="col-md-6 mb-3">
															                        <label class="mb-1" for="caseTitle">Case Title</label>
															                        <input type="text" class="form-control" id="caseTitle" name="caseTitle" placeholder="Enter case title" required>
															                        <input type="hidden" name="clientId" id="clientId">
															                        <input type="hidden" name="client_tpin" id="client_tpin">
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
														        </div>
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

														<!-- Timeline Modal -->
														<div class="modal fade" id="caseTimelineModal" tabindex="-1" aria-hidden="true">
														    <div class="modal-dialog modal-lg">
														        <div class="modal-content">
														            <div class="modal-header">
														                <h5 class="modal-title">Case Status Timeline</h5>
														                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
														            </div>
														            <div class="modal-body">
														                <div class="timeline" id="caseTimeline">
														                    <!-- Timeline content will be appended here -->
														                </div>
														            </div>
														            <div class="modal-footer">
														                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    
	<script type="text/javascript" src="../dist/controls/clientCases.js"></script>
  	<script>
	  function formatText(command, value) {
	    if (command === 'heading') {
	      document.execCommand('formatBlock', false, '<' + value + '>');
	    } else {
	      document.execCommand(command, false, value);
	    }
	    document.getElementById('editor-content').focus();
	  }
	</script>
</body>
</html>