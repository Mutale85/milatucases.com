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
			$caseId = base64_decode($_GET['caseId']);
			$caseNo = base64_decode($_GET['caseNo']);
			$clientId  = base64_decode($_GET['clientId']);
			$caseNumber = fetchCaseNoById($caseId);
			$client_tpin = getClientTpinById($clientId, $lawFirmId);
			$matterTitle = fetchCaseTitleById($caseId);
		}
	?>
	<link rel="stylesheet" type="text/css" href="../assets/custom/caseDetails.css">

	<style>
		.analysis-container {
			  font-family: Arial, sans-serif;
			    line-height: 1.6;
			    color: #333;
			}

			.analysis-section {
			    margin-bottom: 20px;
			}

			.analysis-section h3 {
			    color: #2c3e50;
			    border-bottom: 2px solid #3498db;
			    padding-bottom: 5px;
			}

			.analysis-section p, .analysis-section ul {
			    margin-left: 20px;
			}

			.analysis-section ul {
			    list-style-type: disc;
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
						    		<input type="hidden" id="caseIdentity" value="<?php echo $caseId?>">
						    		<input type="hidden" id="caseNumber" value="<?php echo $caseNo?>">
						    		<input type="hidden" id="lawFirmId" value="<?php echo $lawFirmId?>">
						    	</div>
						        <div class="col">
						            <div class="card">
						            	<div class="card-header">
													<div class="row">
														<div class="col-md-12">
															<div class="list-group flex-row" id="list-tab" role="tablist">
																<a class="list-group-item list-group-item-action active" id="list-matter-details" data-bs-toggle="list" href="#matter-details" role="tab" aria-controls="list-documents">Matter Details</a>
																<a class="list-group-item list-group-item-action" id="list-milestones-list" data-bs-toggle="list" href="#list-milestones" role="tab" aria-controls="list-milestones">Matter Milestones</a>
																<a class="list-group-item list-group-item-action" id="list-documents-list" data-bs-toggle="list" href="#list-documents" role="tab" aria-controls="list-documents">Documents</a>
																<a class="list-group-item list-group-item-action matterStatus" id="list-matter-status-list" data-bs-toggle="list" href="#list-matter-status" role="tab" aria-controls="list-matter-status">Matter Status</a>
																<a class="list-group-item list-group-item-action" id="list-reports-list" data-bs-toggle="list" href="#list-reports" role="tab" aria-controls="list-reports">Reports</a>
																<a class="list-group-item list-group-item-action" id="list-ai-analysis-list" data-bs-toggle="list" href="#ai-analysis" role="tab" aria-controls="ai-analysis" onclick="callLastAnalysis()">
																	<svg width="12" height="12" viewBox="0 0 102 102" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M80.3551 33.5267L74.9639 38.9177L85.1518 49.1049L90.5432 43.7138C90.3743 43.1375 90.1498 42.494 89.87 41.8213C89.086 39.9359 88.0341 38.2165 86.9437 37.1262C85.8532 36.0358 84.1337 34.984 82.2481 34.1999C81.5752 33.9201 80.9315 33.6956 80.3551 33.5267ZM77.7269 56.5292L67.539 46.3422L40.4288 73.4502C37.1417 76.737 35.1983 82.0836 34.1591 87.2806C33.9367 88.3926 33.7635 89.4576 33.6286 90.4412C34.611 90.3063 35.6746 90.1331 36.7852 89.9109C41.9796 88.8713 47.3266 86.9271 50.6173 83.6367L77.7269 56.5292ZM27.9102 96.1602C22.6602 96.1602 22.6602 96.1574 22.6602 96.1574L22.6602 96.1467L22.6603 96.1266L22.6606 96.0674L22.663 95.8742C22.6657 95.713 22.6708 95.4877 22.6805 95.2051C22.7 94.6404 22.7382 93.8435 22.8133 92.8674C22.9628 90.9225 23.2616 88.2288 23.8629 85.2217C25.0253 79.4088 27.4851 71.5442 33.0044 66.0253L76.3543 22.6783L78.5032 22.6604C80.8079 22.6411 83.7243 23.4422 86.2796 24.5047C88.9379 25.6101 91.9873 27.3208 94.368 29.7013C96.7488 32.0819 98.4596 35.1312 99.5651 37.7894C100.628 40.3447 101.429 43.261 101.41 45.5657L101.392 47.7147L58.0416 91.0616C52.5205 96.5823 44.6579 99.0435 38.8457 100.207C35.8392 100.808 33.1463 101.107 31.202 101.257C30.2263 101.332 29.4296 101.37 28.865 101.39C28.5825 101.4 28.3574 101.405 28.1962 101.407L28.003 101.41L27.9438 101.41L27.9237 101.41L27.9161 101.41C27.9161 101.41 27.9102 101.41 27.9102 96.1602ZM27.9102 96.1602V101.41H22.6602V96.1574L27.9102 96.1602Z" fill="Blue"/>
																	<path fill-rule="evenodd" clip-rule="evenodd" d="M15.2285 15.2285L20.3046 0H30.2658L35.3419 15.2285L50.5704 20.3046V30.2658L35.3419 35.3419L30.2658 50.5704H20.3046L15.2285 35.3419L0 30.2658V20.3046L15.2285 15.2285ZM25.2852 18.2621L24.3595 21.0391L21.0391 24.3595L18.2622 25.2852L21.0391 26.2109L24.3595 29.5312L25.2852 32.3082L26.2109 29.5312L29.5312 26.2109L32.3082 25.2852L29.5312 24.3595L26.2109 21.0391L25.2852 18.2621Z" fill="Blue"/>
																	</svg>

																AI Analysis </a>
															</div>
														</div>
														<div class="col-md-12">
															<div class="tab-content" id="nav-tabContent">
																<div class="tab-pane fade show active" id="matter-details" role="tabpanel" aria-labelledby="list-matter-details">
																	<div class="table-responsive">
																		<table class="table table-borderless">
																			<thead>
																				<tr>
																					<th>Matter ID</th>
																					<th>Matter Title</th>
																					<th></th>
																				</tr>
																			</thead>
																			<tbody>
																				<tr>
																					<td><?php echo $caseNo?></td>
																					<td><?php echo $matterTitle ?> </td>
																					<td>
																						<button type='button' class='mb-3 btn btn-primary btn-sm editCase' data-bs-toggle='modal' data-bs-target='#addNewCaseModal' data-case-id='<?php echo $caseId?>'>
													                		<i class='bi bi-pen'></i> Edit
													            			</button>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																	<div id="displayCaseDetailsById"></div>
																</div>
																<div class="tab-pane fade show " id="list-documents" role="tabpanel" aria-labelledby="list-documents-list">
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
																<div class="tab-pane fade" id="list-matter-status" role="tabpanel" aria-labelledby="list-matter-status-list">
																	<h5>Matter Trail</h5>
																	<?php include 'matter-status.php';?>
																</div>
																<div class="tab-pane fade" id="list-milestones" role="tabpanel" aria-labelledby="list-milestones-list">
																		<button type='button' class='mb-5 btn btn-dark add-milestone-btn' data-toggle='modal' data-target='#milestoneModal' data-client-id='<?php echo $clientId?>' data-case-no='<?php echo $caseNo?>'> <i class='bi bi-file-bar-graph'></i> Add Matter Milestone </button>
																		<div class="card-bodys" id="milestoneAdded"></div>
																</div>
																<div class="tab-pane fade" id="list-reports" role="tabpanel" aria-labelledby="list-reports-list">
																	<button type='button' class='mb-3 btn btn-dark btn-sm add-report-btn' data-toggle='modal' data-target='#reportModal' data-client-id='<?php echo $clientId?>' data-case-no='<?php echo $caseNo?>'>Add Report To Client</button>
																</div>
																<div class="tab-pane fade" id="ai-analysis" role="tabpanel" aria-labelledby="list-ai-analysis-list">
																    <button type='button' class='mb-3 btn btn-primary btn-sm' id="runAIAnalysis">
																        <svg width="12" height="12" viewBox="0 0 102 102" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<path fill-rule="evenodd" clip-rule="evenodd" d="M80.3551 33.5267L74.9639 38.9177L85.1518 49.1049L90.5432 43.7138C90.3743 43.1375 90.1498 42.494 89.87 41.8213C89.086 39.9359 88.0341 38.2165 86.9437 37.1262C85.8532 36.0358 84.1337 34.984 82.2481 34.1999C81.5752 33.9201 80.9315 33.6956 80.3551 33.5267ZM77.7269 56.5292L67.539 46.3422L40.4288 73.4502C37.1417 76.737 35.1983 82.0836 34.1591 87.2806C33.9367 88.3926 33.7635 89.4576 33.6286 90.4412C34.611 90.3063 35.6746 90.1331 36.7852 89.9109C41.9796 88.8713 47.3266 86.9271 50.6173 83.6367L77.7269 56.5292ZM27.9102 96.1602C22.6602 96.1602 22.6602 96.1574 22.6602 96.1574L22.6602 96.1467L22.6603 96.1266L22.6606 96.0674L22.663 95.8742C22.6657 95.713 22.6708 95.4877 22.6805 95.2051C22.7 94.6404 22.7382 93.8435 22.8133 92.8674C22.9628 90.9225 23.2616 88.2288 23.8629 85.2217C25.0253 79.4088 27.4851 71.5442 33.0044 66.0253L76.3543 22.6783L78.5032 22.6604C80.8079 22.6411 83.7243 23.4422 86.2796 24.5047C88.9379 25.6101 91.9873 27.3208 94.368 29.7013C96.7488 32.0819 98.4596 35.1312 99.5651 37.7894C100.628 40.3447 101.429 43.261 101.41 45.5657L101.392 47.7147L58.0416 91.0616C52.5205 96.5823 44.6579 99.0435 38.8457 100.207C35.8392 100.808 33.1463 101.107 31.202 101.257C30.2263 101.332 29.4296 101.37 28.865 101.39C28.5825 101.4 28.3574 101.405 28.1962 101.407L28.003 101.41L27.9438 101.41L27.9237 101.41L27.9161 101.41C27.9161 101.41 27.9102 101.41 27.9102 96.1602ZM27.9102 96.1602V101.41H22.6602V96.1574L27.9102 96.1602Z" fill="#FFFFFF"/>
																				<path fill-rule="evenodd" clip-rule="evenodd" d="M15.2285 15.2285L20.3046 0H30.2658L35.3419 15.2285L50.5704 20.3046V30.2658L35.3419 35.3419L30.2658 50.5704H20.3046L15.2285 35.3419L0 30.2658V20.3046L15.2285 15.2285ZM25.2852 18.2621L24.3595 21.0391L21.0391 24.3595L18.2622 25.2852L21.0391 26.2109L24.3595 29.5312L25.2852 32.3082L26.2109 29.5312L29.5312 26.2109L32.3082 25.2852L29.5312 24.3595L26.2109 21.0391L25.2852 18.2621Z" fill="#FFFFFF"/>
																				</svg> 
																				Run AI Analysis
																    </button>
																    <div class="row">
																    	<div class="col-md-9">
																    		<div id="aiAnalysisResult"></div>
																    	</div>
																    	<div class="col-md-3">
																    		<div id="pastAiAnalysis"></div>
																    	</div>
																  	</div>
																</div>
															</div>
														</div>
														</div>
													</div>
											</div>					                
			                <?php include 'caseModals.php'?>
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
		 	$(document).ready(function() {
		    $("#runAIAnalysis").on("click", function() {
		        var caseId = <?php echo json_encode($caseId); ?>;
		        var clientId = <?php echo json_encode($clientId); ?>;
		        
		        $("#runAIAnalysis").prop("disabled", true).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analyzing...`);
		        $('#aiAnalysisResult').html(`<div class="text-center"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
		                                        <span class="visually-hidden">Loading...</span>
		                                    </div>
		                                    <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
		                                        <span class="visually-hidden">Loading...</span>
		                                    </div></div>`);
		        $.ajax({
		            url: 'cases/ai_analysis',
		            method: 'POST',
		            data: {
		                caseId: caseId,
		                clientId: clientId
		            },
		            dataType: 'json',
		            success: function(data) {
		                if (data.success) {
		                    var analysis = displayAnalysis(data.analysis);
		                    $('#aiAnalysisResult').html(analysis);
		                } else {
		                    console.error('Error:', data.message);
		                    $('#aiAnalysisResult').html('Error: ' + data.message);
		                }
		                pastAiAnalysis();
		            },
		            error: function(jqXHR, textStatus, errorThrown) {
		                console.error('Error:', textStatus, errorThrown);
		                $('#aiAnalysisResult').html('An error occurred while performing the analysis.');
		            },
		            complete: function() {
		                $("#runAIAnalysis").prop("disabled", false).html(`<svg width="12" height="12" viewBox="0 0 102 102" fill="none" xmlns="http://www.w3.org/2000/svg">
		                    <path fill-rule="evenodd" clip-rule="evenodd" d="M80.3551 33.5267L74.9639 38.9177L85.1518 49.1049L90.5432 43.7138C90.3743 43.1375 90.1498 42.494 89.87 41.8213C89.086 39.9359 88.0341 38.2165 86.9437 37.1262C85.8532 36.0358 84.1337 34.984 82.2481 34.1999C81.5752 33.9201 80.9315 33.6956 80.3551 33.5267ZM77.7269 56.5292L67.539 46.3422L40.4288 73.4502C37.1417 76.737 35.1983 82.0836 34.1591 87.2806C33.9367 88.3926 33.7635 89.4576 33.6286 90.4412C34.611 90.3063 35.6746 90.1331 36.7852 89.9109C41.9796 88.8713 47.3266 86.9271 50.6173 83.6367L77.7269 56.5292ZM27.9102 96.1602C22.6602 96.1602 22.6602 96.1574 22.6602 96.1574L22.6602 96.1467L22.6603 96.1266L22.6606 96.0674L22.663 95.8742C22.6657 95.713 22.6708 95.4877 22.6805 95.2051C22.7 94.6404 22.7382 93.8435 22.8133 92.8674C22.9628 90.9225 23.2616 88.2288 23.8629 85.2217C25.0253 79.4088 27.4851 71.5442 33.0044 66.0253L76.3543 22.6783L78.5032 22.6604C80.8079 22.6411 83.7243 23.4422 86.2796 24.5047C88.9379 25.6101 91.9873 27.3208 94.368 29.7013C96.7488 32.0819 98.4596 35.1312 99.5651 37.7894C100.628 40.3447 101.429 43.261 101.41 45.5657L101.392 47.7147L58.0416 91.0616C52.5205 96.5823 44.6579 99.0435 38.8457 100.207C35.8392 100.808 33.1463 101.107 31.202 101.257C30.2263 101.332 29.4296 101.37 28.865 101.39C28.5825 101.4 28.3574 101.405 28.1962 101.407L28.003 101.41L27.9438 101.41L27.9237 101.41L27.9161 101.41C27.9161 101.41 27.9102 101.41 27.9102 96.1602ZM27.9102 96.1602V101.41H22.6602V96.1574L27.9102 96.1602Z" fill="#FFFFFF"/>
		                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2285 15.2285L20.3046 0H30.2658L35.3419 15.2285L50.5704 20.3046V30.2658L35.3419 35.3419L30.2658 50.5704H20.3046L15.2285 35.3419L0 30.2658V20.3046L15.2285 15.2285ZM25.2852 18.2621L24.3595 21.0391L21.0391 24.3595L18.2622 25.2852L21.0391 26.2109L24.3595 29.5312L25.2852 32.3082L26.2109 29.5312L29.5312 26.2109L32.3082 25.2852L29.5312 24.3595L26.2109 21.0391L25.2852 18.2621Z" fill="#FFFFFF"/>
		                    </svg> Run AI Analysis`);
		            }
		        });
		    });

		    function displayAnalysis(analysis) {
		        let html = '<div class="analysis-container">';
		        
		        analysis.forEach(section => {
		            html += `<div class="analysis-section">
		                        <h3>${section.title}</h3>
		                        ${formatContent(section.content)}
		                     </div>`;
		        });
		        
		        html += '</div>';
		        
		        return html;  // Return the HTML string instead of trying to set innerHTML
		    }

		    function formatContent(content) {
		        // Split content into paragraphs
		        let paragraphs = content.split('\n');
		        
		        // Check if the content is a list
		        if (paragraphs.some(p => p.trim().match(/^\d+\./))) {
		            return '<ul>' + paragraphs.map(p => `<li>${p.replace(/^\d+\.\s*/, '')}</li>`).join('') + '</ul>';
		        } else {
		            return paragraphs.map(p => `<p>${p}</p>`).join('');
		        }
		    }

		    function pastAiAnalysis() {
		        var caseId = <?php echo json_encode($caseId); ?>;
		        var clientId = <?php echo json_encode($clientId); ?>;

		        $.ajax({
		            url: 'cases/fetch_ai_analysis',
		            method: 'POST',
		            data: {
		                caseId: caseId,
		                clientId: clientId
		            },
		            success: function(data) {
		                $('#pastAiAnalysis').html(data);
		            }
		        });
		    }
		    pastAiAnalysis();
			});
			/*
				function callLastAnalysis(){
		    	var caseId = <?php echo json_encode($caseId); ?>;
	        var clientId = <?php echo json_encode($clientId); ?>;
	        
	        $.ajax({
	            url: 'cases/fetch_ai_last_analysis',
	            method: 'POST',
	            beforeSend:function(){
	            	$("#aiAnalysisResult").prop("disabled", true).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analyzing...`);
	            },
	            dataType:"json",
	            data: {
	                caseId: caseId,
	                clientId: clientId
	            },
	            success: function(data) {
	              $('#aiAnalysisResult').html(data);
	            }
	        });
		    }
		  */
		  function callLastAnalysis() {
    var caseId = <?php echo json_encode($caseId); ?>;
    var clientId = <?php echo json_encode($clientId); ?>;
    
    $.ajax({
        url: 'cases/fetch_ai_last_analysis',
        method: 'POST',
        dataType: 'json',
        data: {
            caseId: caseId,
            clientId: clientId
        },
        beforeSend: function() {
            $("#aiAnalysisResult").html(`<div class="text-center"><span class="spinner-border" role="status" aria-hidden="true"></span> Fetching analysis...</div>`);
        },
        success: function(data) {
            if (data.success) {
                var analysisHtml = `
                    <div class='card'>
                        <div class='card-header d-flex justify-content-between align-items-center'>
                            <h5 class='mb-0'>Latest Analysis</h5>
                            <button class='btn btn-sm btn-outline-secondary copy-analysis' data-analysis-id='${data.analysis_id}'>Copy</button>
                        </div>
                        <div class='card-body'>
                            <p class='card-text'><small class='text-muted'>Created: ${data.created_at}</small></p>
                `;
                
                data.analysis.forEach(function(section) {
                    analysisHtml += `
                        <h6>${section.title}</h6>
                        <p>${section.content}</p>
                    `;
                });
                
                analysisHtml += `
                        </div>
                        <div class='full-analysis' style='display:none;'>${JSON.stringify(data.analysis)}</div>
                    </div>
                `;
                
                $('#aiAnalysisResult').html(analysisHtml);
                
                // Add copy functionality
                $('.copy-analysis').on('click', function() {
                    var fullAnalysis = $(this).closest('.card').find('.full-analysis').text();
                    var tempTextArea = $('<textarea>');
                    $('body').append(tempTextArea);
                    tempTextArea.val(fullAnalysis).select();
                    document.execCommand('copy');
                    tempTextArea.remove();
                    alert('Analysis copied to clipboard!');
                });
            } else {
                $('#aiAnalysisResult').html('<div class="alert alert-danger">' + data.message + '</div>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#aiAnalysisResult').html('<div class="alert alert-danger">An error occurred while fetching the analysis.</div>');
            console.error('AJAX Error:', textStatus, errorThrown);
        }
    });
}

	
			
			$(document).ready(function() {
		    // Delete analysis
		    $('.btn-close').on('click', function(e) {
		        e.preventDefault();
		        var analysisId = $(this).closest('.analysis-item').data('id');
		        $.ajax({
		            url: 'cases/delete_ai_analysis',
		            method: 'POST',
		            data: { id: analysisId },
		            success: function(response) {
		                if(response === 'success') {
		                  $(e.target).closest('.alert').remove();
		                }
		            }
		        });
		    });

		    // Show full analysis
		    // $('.analysis-item').on('click', function(e) {
		    //     if(!$(e.target).hasClass('btn-close')) {
		    //         $(this).find('.full-analysis').toggle();
		    //     }
		    // });
		});
		</script>
</body>
</html>