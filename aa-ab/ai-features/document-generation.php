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
	<title>Document Generation</title>
	<?php include '../addon_header.php'; ?>
	<style>
        #generatedDocument {
            white-space: pre-wrap;
            font-family: 'Courier New', Courier, monospace;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-top: 1rem;
            max-height: 500px;
            overflow-y: auto;
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
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Generated Docs</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="fetchGeneratedDocs"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
        					    
                                <h4 class="mb-0 card-title border-bottom pb-3">AI Document Generator</h4>
                                <div id="generatedDocumentSection" class="mt-4 mb-5" style="display: none;">                                    
                                    <div id="generatedDocument" class="border p-3 bg-light"></div>
                                    <button id="copyBtn" class="btn btn-sm btn-outline-primary copy-btn">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                    <button id="retryBtn" class="btn btn-primary btn-sm">
                                        <i class="bi bi-arrow-repeat me-1"></i> Retry
                                    </button>
                                </div>
        					    <form id="docGenerationForm">
        					        <div class="mb-3">
        					            <label for="docType" class="form-label">Select Document Type</label>
        					            <select id="docType" name="docType" class="form-select" required>
        					                <option value="contract">Contract</option>
        					                <option value="NDA">Non-Disclosure Agreement</option>
        					                <option value="employment">Employment Agreement</option>
        					                <option value="proposal">Business Proposal</option>
        					                <option value="MOU">Memorandum of Understanding</option>
        					                <option value="lease">Lease Agreement</option>
                                            <option value="memo">Legal Memo</option>
                                            <option value="demand letter">Demand Letter</option>
                                            <option value="pleading">Pleading</option>
                                            <option value="legal brief">Legal Brief</option>
                                            <option value="agreement">Agreement</option>
                                            <option value="affidavit">Affidavit</option>
                                            <option value="motion">Motion</option>
        					                <option value="custom">Custom Document</option>
        					            </select>
        					        </div>
        					        <div class="mb-3" id="customDocTypeDiv" style="display: none;">
        					            <label for="customDocType" class="form-label">Custom Document Type</label>
        					            <input type="text" class="form-control" id="customDocType" name="customDocType">
        					        </div>
        					        <div class="mb-3">
        					            <label for="selected_client_tpin" class="form-label">Client Name</label>
        					            <div class="input-group">
        				                	<select class="form-select" id="selected_client_tpin" name="clientId" required></select>
        				                </div>
        					        </div>
        					        <div class="mb-3">
        					            <label for="startDate" class="form-label">Start Date</label>
        					            <input type="date" class="form-control" id="startDate" name="startDate" required>
        					        </div>
        					        <div class="mb-3">
        					            <label for="description" class="form-label">Description</label>
        					            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter specific terms or leave it to AI"></textarea>
        					        </div>
        					        <button type="submit" class="btn btn-primary" id="generateBtn">Generate Document</button>
        					    </form>
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
    	function fetchLawFirmClients() {
		  var lawFirmId = "<?php echo $_SESSION['parent_id']?>";
		  $.ajax({
		    type: 'POST',
		    url: 'cc/fetchLawFirmClients',
		    data: { lawFirmId: lawFirmId },
		    dataType: 'json',
		    success: function(response) {
		      if (response.success) {
		        var clients = response.clients;
		        var select = $("#selected_client_tpin");
		        select.empty();
		        select.append('<option value="">Select Client</option>');

		        // Populate the select element with new options
		        clients.forEach(function(client) {
		          var clientLabel = client.client_names;
		          if (client.client_type === 'Corporate') {
		            clientLabel = ` [${client.business_name}] ${client.client_names}`;
		          }
		          
		          var option = $('<option></option>')
				  .attr('value', client.id)
				  .attr('data-email', client.client_email)
				  .attr('data-tpin', client.client_tpin)
				  .attr('data-id', client.id)
				  .text(clientLabel);
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

    	
        function generateDocument(formData, isRetry = false) {
            var $form = $('#docGenerationForm');
            var $button = $('#generateBtn');
            var $result = $('#generatedDocument');
            var $copyBtn = $('#copyBtn');
            var $retryBtn = $('#retryBtn');

            $button.prop('disabled', true).text('Generating...');
            if (!isRetry) {
                $result.empty();
            }
            $copyBtn.hide();
            $retryBtn.hide();

            $.ajax({
                url: 'ai-features/parsers/ai_document_generate',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {

                    if (response.success && response.chunks) {
                        $("#generatedDocumentSection").show();
                        var i = 0;
                        function typeNextChunk() {
                            if (i < response.chunks.length) {
                                if (response.chunks[i] === "\n") {
                                    $result.append('<br>');
                                } else {
                                    $result.append(response.chunks[i]);
                                }
                                i++;
                                setTimeout(typeNextChunk, 20);
                            } else {
                                $button.prop('disabled', false).text('Generate Document');
                                $copyBtn.show();
                                $retryBtn.show().data('generationId', response.generationId);
                                if (!isRetry) {
                                    $form[0].reset();
                                }
                            }
                        }
                        typeNextChunk();
                        fetchGeneratedData();
                    } else {
                        var errorMessage = response.error || 'Unknown error occurred';
                        $result.html('<div class="alert alert-danger">Error: ' + errorMessage + '</div>');
                        $button.prop('disabled', false).text('Generate Document');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    var errorMessage = "An error occurred. Please try again.";
                    if (xhr.responseText) {
                        try {
                            var jsonResponse = JSON.parse(xhr.responseText);
                            if (jsonResponse.error) {
                                errorMessage += " Error details: " + jsonResponse.error;
                            }
                        } catch (e) {
                            errorMessage += " Unable to parse error details.";
                        }
                    }
                    $result.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    $button.prop('disabled', false).text('Generate Document');
                }
            });
        }

        fetchLawFirmClients();
        $(document).ready(function() {
            $('#docType').change(function() {
                if ($(this).val() === 'custom') {
                    $('#customDocTypeDiv').show();
                } else {
                    $('#customDocTypeDiv').hide();
                }
            });

            $('#docGenerationForm').submit(function(e) {
                e.preventDefault();
                generateDocument($(this).serialize());
            });

            $('#copyBtn').click(function() {
                var $temp = $("<textarea>");
                $("body").append($temp);
                $temp.val($('#generatedDocument').text()).select();
                document.execCommand("copy");
                $temp.remove();
                
                $(this).text('Copied!');
                setTimeout(() => {
                    $(this).html('<i class="bi bi-clipboard"></i> Copy');
                }, 2000);
            });

            $('#retryBtn').click(function() {
                var generationId = $(this).data('generationId');
                generateDocument($('#docGenerationForm').serialize() + '&generationId=' + generationId, true);
            });
        });

        function fetchGeneratedData(){
            var fetchGeneratedDocs = "fetchGeneratedDocs";
            $.ajax({
                url: 'ai-features/parsers/fetchAiGeneratedDocs',
                method:"POST", 
                data:{fetchGeneratedDocs:fetchGeneratedDocs},
                success: function(data) {
                    $('#fetchGeneratedDocs').html(data);
                }
                
            });
        }
        fetchGeneratedData();

        $(document).on('click', '.showDocument', function(e) {
            e.preventDefault();
            var docId = $(this).data('doc-id');             
            $.ajax({
                url: 'ai-features/parsers/getGeneratedDocument',
                method: 'POST',
                data: { docId: docId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#generatedDocument').html(response.document);
                        $('#generatedDocumentSection').show();
                        $("#retryBtn").hide();                          
                        $('html, body').animate({
                            scrollTop: $("#generatedDocumentSection").offset().top
                        }, 500);
                    } else {
                        sweetSuccess('Error: ' + response.message);
                    }
                },
                error: function() {
                    sweetError('Error fetching the document. Please try again.');
                }
            });
        });
	</script>
</body>
</html>