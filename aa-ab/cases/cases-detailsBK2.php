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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css"/>
	<link rel="stylesheet" type="text/css" href="../assets/custom/caseDetails.css">

	<style>
		.ai-analysis {
		    font-family: Arial, sans-serif;
		    line-height: 1.6;
		    color: #333;
		    max-width: 800px;
		    margin: 20px auto;
		    padding: 20px;
		    background-color: #f9f9f9;
		    border-radius: 8px;
		    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}

		.ai-analysis h2 {
		    color: #2c3e50;
		    border-bottom: 2px solid #3498db;
		    padding-bottom: 10px;
		}

		.ai-analysis h3 {
		    color: #2980b9;
		    margin-top: 20px;
		}

		.ai-analysis ol {
		    padding-left: 20px;
		}

		.ai-analysis li {
		    margin-bottom: 10px;
		}

		.ai-analysis p {
		    text-align: justify;
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
																<a class="list-group-item list-group-item-action" id="list-ai-analysis-list" data-bs-toggle="list" href="#ai-analysis" role="tab" aria-controls="ai-analysis">AI Analysis</a>
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
																        <i class="bi bi-cpu me-2"></i> Run AI Analysis
																    </button>
																    <div id="aiAnalysisResult"></div>
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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>    
		<!-- <script type="text/javascript" src="../dist/controls/clientCases.js"></script> -->
		<script>
			function fetchCases(tpin){
	$.ajax({
		url:'cases/fetchLawFirmClientsCases',
		method:"POST",
		data:{tpin:tpin},
		success:function(response){
			$("#displayClientCases").html(response);
		}
	})
}

$(document).ready(function() {
        // Handle Milestone button click
    $(document).on('click', '.add-milestone-btn', function() {
        
        $("#milestoneModal").modal("show");
    });

    // Handle Report button click
    $(document).on('click', '.add-report-btn', function() {
        
        $("#reportModal").modal("show");
    });


    // $('#reportForm').on('submit', function(event) {
    //     event.preventDefault();
    //     // Add your AJAX code here to handle the form submission
    //     alert('Report form submitted');
    // });
});


$(document).ready(function() {
    initializeTrumbowyg();
});

function initializeTrumbowyg() {
    $('#caseDescription2').trumbowyg({
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
            upload: {
                serverPath: null
            },
            emoji: {
                svgPath: null
            }
        }
    });
}



$(document).on("click", ".editCase", function(e) {
    var caseId = $(this).data('case-id');
    document.getElementById('caseId').value = caseId;
    $('#addNewCaseModal').modal("show");

    $.ajax({
        url: 'cases/fetchSelectedCase', // PHP file to fetch data submitCase
        type: 'POST',
        data: { caseId: caseId },
        success: function(response) {
            // Parse JSON response
            var data = JSON.parse(response);
            $("#causeId").val(data.case.causeId);
            $("#caseId").val(caseId);
            $('#caseNo').val(data.case.caseNo);
            $('#client_tpin').val(data.case.client_tpin);
            $("#clientId").val(data.case.clientId);
            $('#caseTitle').val(data.case.caseTitle);
            $('#caseCategory').val(data.case.caseCategory);
            $('#caseDescription2').val(data.case.caseDescription); // Updated to reflect actual textarea id
            $('#caseDate').val(data.case.caseDate);
            $('#feeMethod').val(data.case.feeMethod);
            $('#currency').val(data.case.currency);
            $('#custom-status-input').val(data.case.other_case_status);
            var status =  $('#caseStatus').val(data.case.caseStatus);
            // Handle Case Status
            if (status == 'custom') {
                $('#caseStatus').val('custom');
                $('#custom-status-input').show();
                $('#custom-status').val(data.case.other_case_status); // Assume customStatus is returned in the response
            } else {
                $('#caseStatus').val(data.case.caseStatus);
                $('#custom-status-input').hide();
            }

            // Handle Fee Method
            if (data.case.feeMethod === 'Hourly Rate') {
                $('#hourlyRateInput').show();
                $('#hourlyRate').val(data.case.hourlyRate);
            } else if (data.case.feeMethod) {
                $('#fixedFeeInput').show();
                $('#fixedFee').val(data.case.fixedFee);
            }

            $("#docBtn").data('case-id', caseId);
            $("#docBtn").data('case-no', data.case.caseNo);

            // Check assigned lawyers
            $.each(data.case_access, function(index, access) {
                $('#accessControl' + access.lawyerId).prop('checked', true);
            });

            // Reinitialize Trumbowyg after setting the content
            $('#caseDescription2').trumbowyg('destroy');
            initializeTrumbowyg();
        }
    });
});


$("#addCaseBtn").click(function(){
    $("#addCaseForm")[0].reset();
})

$(document).on("click", ".displayDocument", function(e){
    var caseId = $(this).data("case-id");
    var caseNo = $(this).data('caseNo');
    $.ajax({
        url:"cases/fetchCaseDocuments",
        method:"POST",
        data:{caseId:caseId, caseNo:caseNo},
        success:function(response){
            $("#documentModal").modal("show");
            $("#showDocuments").html(response);
        }
    })
})

$('#caseStatus').on('change', function() {
    if ($(this).val() === 'custom') {
        $('#custom-status-input').show();
    } else {
        $('#custom-status-input').hide();
    }
});

$('#addCaseForm').submit(function (e) {
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
    if(navigator.onLine){
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
                // if(response.includes("Case successfully saved") || response.includes("Case successfully updated")){
                alert(response);
                setTimeout(function(){
                    // location.reload();
                    $("#addNewCaseModal").modal("hide");
                    displayCaseDetailsById();
                }, 1000)
                // }else{
                //     sweetError(response);
                // }
                $("#submitCase").prop("disabled", false).html("Save Case");
            },
            error: function (error) {
                sweetError('Error: ' + error.responseText);
                $("#submitCase").prop("disabled", false).html("Save Case");
            }
        });
    }else{
        sweetError("Check your internet connection");
    }
});

function displayCaseDetailsById(){
    var caseId = document.getElementById('caseIdentity').value;
    var lawFirmId = document.getElementById('lawFirmId').value;
    $.ajax({
        url:"cases/fetchAndDisplayCaseDetailsById",
        method:"POST",
        data: {caseId:caseId, lawFirmId:lawFirmId},
        success:function(response){
            $("#displayCaseDetailsById").html(response);
        }
    })
}

displayCaseDetailsById();

$(document).on("submit", "#uploadDocumentsForm", function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: "cases/uploadCaseDocuments", // A separate PHP file to handle the upload
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend:function(){
            $("#uploadMoreBtn").html("Processing...").prop("disabled", true);
        },
        success: function(response) {
            // Reload the documents
            var caseId = $("input[name='newcaseId']").val();
            var caseNo = $("input[name='newcaseNo']").val();
            $.ajax({
                url: "cases/fetchCaseDocuments",
                method: "POST",
                data: { caseId: caseId, caseNo: caseNo },
                success: function(response) {
                    $("#showDocuments").html(response);
                }
            });
            $("#uploadMoreBtn").html("Upload").prop("disabled", false);
        }
    });
});

function callDocument(caseId, caseNo){
    $.ajax({
        url: "cases/fetchCaseDocuments",
        method: "POST",
        data: { caseId: caseId, caseNo: caseNo },
        success: function(response) {
            $("#showDocuments").html(response);
        }
    });
}

$(document).on("click", ".remove-file", function() {
    var docId = $(this).data("id");
    if (confirm("You wish to remove this document")) {
        $.ajax({
            url: "cases/removeCaseDocument", // A separate PHP file to handle the removal
            method: "POST",
            data: { docId: docId },
            success: function(response) {
                fetchFiles();
            }
        });
    }else{
        return false;
    }
});
var caseId = document.getElementById("milestoneCaseId").value;
fetchMileStoneByCaseId(caseId);


$(document).ready(function() {
  $('#milestoneForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the form data
    var formData = {
        clientId: $('#milestoneClientId').val(),
        client_tpin: $('#milestoneClient_tpin').val(),
        caseNo: $('#milestoneCaseNo').val(),
        caseId: $('#milestoneCaseId').val(),
        userId: $('#userId').val(),
        lawFirmId: $('#lawFirmId').val(),
        milestoneId: $('#milestoneId').val(),
        milestoneTitle: $('#milestoneTitle').val(),
        milestoneDescription: $('#editor-content').html(),
        agree: $('#agree').is(':checked')
    };

    // Validate the form
    if (formData.milestoneTitle.trim() === '') {
        sweetError('Milestone title cannot be empty.');
        return;
    }

    if (formData.milestoneDescription.trim() === '') {
        sweetError('Milestone description cannot be empty.');
        return;
    }

    if (!formData.agree) {
        sweetError('You must agree that the information provided is correct.');
        return;
    }

    // Submit the form
    $.ajax({
        type: "POST",
        url: "cases/createCaseMilestone",
        data: formData,
        beforeSend:function(){
            $("#milestoneBtn").html("Processing...").prop("disabled", true);
        },
        success: function(response) {
            if(response.includes("Milestone saved successfully") || response.includes("Milestone updated successfully")){
                sweetSuccess(response);
                $("#milestoneModal").modal("hide");
            }else{
                sweetError(response);
            }
            $('#milestoneForm')[0].reset();
            fetchMileStoneByCaseId(caseId);
            clearForm();
            $("#milestoneBtn").html("Submit Milestone").prop("disabled", false);
        },
        error: function() {
            sweetSuccess('Error submitting milestone.');
            $("#milestoneBtn").html("Submit Milestone").prop("disabled", false);
        }
    });
  });
});

function clearForm() {
    $('#milestoneId').val('');
    $('#milestoneTitle').val('');
    $('#editor-content').html("");
    $('#agree').prop('checked', false);
}

function fetchMileStoneByCaseId(caseId){
    $.ajax({
        type: 'POST',
        url: 'cases/fetchCaseMilestone',
        data: {caseId:caseId},
        
        success: function(response) {
            $('#milestoneAdded').html(response);
        },
        error: function() {
            alert('An error occurred while saving the milestone');
        }
    });
}

$(document).ready(function() {
    $(document).on('click', '.editMilestoneBtn', function() {
        var milestoneId = $(this).data('id');
        $.ajax({
            url: 'cases/fetchSelectedMilestone',
            type: 'POST',
            data: { id: milestoneId },
            success: function(response) {
                var milestone = JSON.parse(response);
                $('#milestoneId').val(milestone.id);
                $('#milestoneTitle').val(milestone.milestoneTitle);
                $('#editor-content').html(milestone.milestoneDescription);
                $('#milestoneModal').modal('show');
            }
        });
    });
});

$(document).on("click", ".editCaseStatus", function(e){
    e.preventDefault();
    var caseId = $(this).attr("id");

    $.ajax({
        url: 'cases/fetch_case_status',
        type: 'POST',
        data: { caseId: caseId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $("#caseStatus").val(response.caseStatus);
                $("#caseStatusModal").modal("show");
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Error fetching case status');
        }
    });
});

$(document).on("submit", "#caseStatusForm", function(e){
    e.preventDefault();

    var formData = $(this).serialize();

    $.ajax({
        url: 'cases/submit_case_status',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Case status updated successfully');
                $("#caseStatusModal").modal("hide");
                // Optionally, refresh the page or update the UI
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Error updating case status');
        }
    });
});

$(document).on("click", ".viewCaseTimeline", function(e){
    e.preventDefault();
    var caseId = $(this).data("case-id");

    $.ajax({
        url: 'cases/fetch_case_timeline',
        type: 'POST',
        data: { caseId: caseId },
        dataType: 'json',
        success: function(response) {
            if (response) {
                var timelineContent = '';

                $.each(response, function(index, item) {
                    var alignment = (index % 2 === 0) ? 'left' : 'right';
                    timelineContent += `
                        <div class="timeline-content ${alignment}">
                            <h5>${item.case_status}</h5>
                            <p>${item.date_added}</p>
                        </div>
                    `;
                });

                $("#caseTimeline").html(timelineContent);
                $("#caseTimelineModal").modal("show");
            } else {
                alert('Error fetching case timeline');
            }
        },
        error: function() {
            alert('Error fetching case timeline');
        }
    });
});


$(function(){
    $("#createFolderForm").submit(function(e){
        e.preventDefault();
        $.ajax({
            url:"cases/createSubFolder",
            method:"POST",
            data:$(this).serialize(),
            beforeSend:function(){
                $("#createFolderBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Creating...");
            },
            success:function(response){
                if(response.includes("Folder created successfully")){
                    sweetSuccess(response);
                    fetchSubFolders();
                    fetchFiles();
                }else{
                    sweetError(response);
                }
                $("#createFolderBtn").prop("disabled", false).html("Create Folder");
                $("#createFolderModal").modal("hide");
            }
        })
    })
})

    function fetchSubFolders(){
        var caseId = document.getElementById('caseIdentity').value;
        var caseNo = document.getElementById('caseNumber').value;
        var lawFirmId = document.getElementById('lawFirmId').value;

        $.ajax({
            url:"cases/fetchSubFolders",
            method:"POST",
            data:{caseId:caseId, lawFirmId:lawFirmId},

            success:function(response){
                $("#fetchFolders").html(response);
            }
        })
    }
    fetchSubFolders();
        function fetchFiles(){
        var caseId = document.getElementById('caseIdentity').value;
        var caseNo = document.getElementById('caseNumber').value;
        var lawFirmId = document.getElementById('lawFirmId').value;
        $.ajax({
        url:"cases/fetchCaseFiles",
        method:"POST",
        data:{caseId:caseId, caseNo:caseNo, lawFirmId:lawFirmId},

        success:function(response){
            $("#filesDisplay").html(response);
        }
        })
    }

    fetchFiles();

    const navTabs = document.querySelectorAll('.my-nav-tabs a');
    const tabPanes = document.querySelectorAll('.my-tab-pane');

    navTabs.forEach((tab, index) => {
      tab.addEventListener('click', (event) => {
        event.preventDefault();

        // Remove active class from all tabs and panes
        navTabs.forEach(t => t.classList.remove('active'));
        tabPanes.forEach(p => p.classList.remove('active'));

        // Add active class to the clicked tab and its corresponding pane
        event.target.classList.add('active');
        tabPanes[index].classList.add('active');

        // Check which tab was clicked (files or folders)
        const clickedTab = event.target.dataset.tab;
        if (clickedTab === 'files') {
          // Call the function to fetch files
          fetchFiles();
        } else if (clickedTab === 'folders') {
          // Call the function to fetch folders
          fetchSubFolders();
        }
      });
    });


    $(document).on('click', '.previewFile', function(e) {
        e.preventDefault();
        var fileName = $(this).data('file');
        if (!fileName) {
            console.error('File name is undefined');
            return;
        }

        var fileExtension = fileName.split('.').pop().toLowerCase();
        var filePath = fileName;
        var fileContent = '';

        if (fileExtension === 'pdf') {
            fileContent = '<embed src="' + filePath + '" type="application/pdf" width="100%" height="600px" />';
        } else if (['jpg', 'jpeg', 'png', 'gif'].indexOf(fileExtension) !== -1) {
            fileContent = '<img src="' + filePath + '" class="img-fluid" />';
        } else if (['doc', 'docx'].indexOf(fileExtension) !== -1) {
            var encodedUrl = encodeURIComponent(window.location.origin + '/' + filePath);
            fileContent = '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' + encodedUrl + '" width="100%" height="600px"></iframe>';
        } else {
            fileContent = '<p>File type not supported for preview.</p>';
        }

        $('#filePreviewContent').html(fileContent);
        $('#filePreviewModal').modal('show');
    });

    $("#sendEmail").click(function(){
        $("#emailModal").modal("show");
    })

    $('#sendEmailForm').submit('click', function(e) {
        e.preventDefault();
        var sendEmailForm = $(this).serialize();
        $.ajax({
            url: 'cases/sendMatterStatus',
            method: 'POST',
            data:sendEmailForm,
            beforeSend:function(){
                $("#sendEmailBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
            },
            success: function(response) {
                sweetSuccess(response);
                $('#emailModal').modal('hide');
                $("#sendEmailBtn").prop("disabled", false).html('Send as Email');
            },
            error: function() {
                sweetError('An error occurred while sending the email.');
                $("#sendEmailBtn").prop("disabled", false).html('Send as Email');

            }
        });
    })

    $('#generatePdf').on('click', function() {
        var clientId = $(this).data('client-id');
        var caseId = $(this).data('case-id');
        $("#pdfModal").modal("show");
    })

    $("#createPdfForm").submit(function(e){
        e.preventDefault();
        var createPdfForm = $(this).serialize();
        $.ajax({
            url: 'cases/generateMatterStatusPDF',
            method: 'POST',
            data: createPdfForm,
            beforeSend:function(){
                $("#submitPdfBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing...");
            },
            success: function(response) {
                if (response.success) {
                sweetSuccess("PDF REPORT CREATED");
                $("#pdfModal").modal("hide");
                  setTimeout(function(){
                      window.open("cases/" + response.pdfUrl, '_blank');
                  }, 2000)
              }else{
                sweetError(response);
              }
              $("#submitPdfBtn").prop("disabled", false).html('Create PDF');
            },
            error: function() {
                sweetError('An error occurred while creating pdf.');
                $("#submitPdfBtn").prop("disabled", false).html('Create PDF');

            },
            dataType:"json"
        });
    })
    function setEndDate() {
            const fromDate = document.getElementById('from').value;
            const endDateInput = document.getElementById('end');
            const fromDateObj = new Date(fromDate);
            const endDateObj = new Date(fromDateObj.getTime() + (7 * 24 * 60 * 60 * 1000));
            const endDateStr = endDateObj.toISOString().slice(0, 10);
            endDateInput.value = endDateStr;
            endDateInput.min = fromDate;
        }
        function setEndDate2() {
            const fromDate = document.getElementById('fromdate').value;
            const endDateInput = document.getElementById('todate');
            const fromDateObj = new Date(fromDate);
            const endDateObj = new Date(fromDateObj.getTime() + (7 * 24 * 60 * 60 * 1000));
            const endDateStr = endDateObj.toISOString().slice(0, 10);
            endDateInput.value = endDateStr;
            endDateInput.min = fromDate;
        }

    /*move files to folder*/
    $(document).ready(function() {
        const $checkboxes = $('.fileCheckbox');
        const $moveButton = $('#moveToFolderButton');
        const $selectedFilesInput = $('#selectedFiles');
        const $folderSelect = $('#folderSelect');
        const $moveFilesForm = $('#moveFilesForm');

        $(document).on('change', '.fileCheckbox', function() {
            // alert("Good");
            const $checkedCheckboxes = $('.fileCheckbox:checked');
            if ($checkedCheckboxes.length > 0) {
                $('#moveToFolderButton').show();
            } else {
                $('#moveToFolderButton').hide();
            }
        });
        
        $('#moveToFolderButton').on('click', function(event) {
            event.preventDefault();
            const selectedFiles = [];
            const $checkedCheckboxes = $('.fileCheckbox:checked');
            $checkedCheckboxes.each(function() {
                selectedFiles.push($(this).val());
            });
            $selectedFilesInput.val(JSON.stringify(selectedFiles));
            const selectFolderModal = new bootstrap.Modal($('#selectFolderModal')[0]);
            selectFolderModal.show();
        });

        $("#moveFilesForm").on('submit', function(event) {
            event.preventDefault();
            const folderId = $folderSelect.val();
            const selectedFiles = $selectedFilesInput.val();
            const caseId = $('#caseId').val();

            $.ajax({
                url: 'cases/process/moveFiles',
                type: 'POST',
                data:{
                    folderId: folderId,
                    files:selectedFiles,
                    caseId:caseId
                },
                // contentType: 'application/json',
                beforeSend:function(){
                    $("#moveBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Moving....");
                },
                success: function(data) {
                    sweetSuccess(data);
                    fetchFiles();
                    $("#moveBtn").prop("disabled", false).html("Move Files");
                    $("#selectFolderModal").modal("hide");
                    
                },
                error: function(error) {
                    sweetError('Error:', error);
                    $("#moveBtn").prop("disabled", false).html("Move Files");
                }
            });
        });
            
    });

        $("#moveToFolderButton").click(function(){
            fetchandLoadFolder();
        })

        function fetchandLoadFolder(){
            var caseId = document.getElementById('caseIdentity').value;
            var lawFirmId = document.getElementById('lawFirmId').value;
            $.ajax({
                url:'cases/process/fetchFolders',
                method:"POST",
                data:{caseId:caseId, lawFirmId:lawFirmId},
                success:function(response){
                    $("#folderSelect").html(response);
                }
            })
        }

        function editFolder(folderId) {
            $.ajax({
                url: 'cases/process/fetchSelectedFolder',
                type: 'POST',
                data: { folder_id: folderId },
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        $("#folderName").val(data.folder_name);
                        $("#folderId").val(folderId);
                        $("#createFolderModal").modal("show"); // Assuming a modal with ID editFolderModal
                    } else {
                        console.error("Folder data not found");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr, status, error);
                }
            });
        }

		</script>
  	<script>
		  function formatText(command, value) {
		    if (command === 'heading') {
		      document.execCommand('formatBlock', false, '<' + value + '>');
		    } else {
		      document.execCommand(command, false, value);
		    }
		    document.getElementById('editor-content').focus();
		  }
		 
			$("#runAIAnalysis").on("click", function() {
		    var caseId = <?php echo json_encode($caseId); ?>;
		    var clientId = <?php echo json_encode($clientId); ?>;
		    
		    $("#runAIAnalysis").prop("disabled", true).html('<i class="bi bi-hourglass-split me-2"></i> Analyzing...');

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
		                $('#aiAnalysisResult').html(data.analysis);
		            } else {
		                console.error('Error:', data.message);
		                $('#aiAnalysisResult').html('Error: ' + data.message);
		            }
		        },
		        error: function(jqXHR, textStatus, errorThrown) {
		            console.error('Error:', textStatus, errorThrown);
		            $('#aiAnalysisResult').html('An error occurred while performing the analysis.');
		        },
		        complete: function() {
		            $("#runAIAnalysis").prop("disabled", false).html('<i class="bi bi-cpu me-2"></i> Run AI Analysis');
		        }
		    });
			});

		
		</script>
</body>
</html>