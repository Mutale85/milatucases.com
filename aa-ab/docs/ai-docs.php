<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-semi-dark" data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName'];?> AI Documents</title>
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
				                <div class="card">
				                    
				                    <div class="card-body">
				                    	<div id="generatedDocumentSection" class="mt-4" style="display: none;">
										    <div class="d-flex justify-content-between align-items-center mb-2">
										    	<h4 class="mb-0 card-title border-bottom pb-3">AI Document Generator</h4>
										    	<button id="copyDocumentBtn" class="btn btn-sm btn-outline-secondary">
										            <i class="bi bi-clipboard"></i> Copy
										        </button>
										    </div>
										    <div id="generatedDocument" class="border p-3 bg-light"></div>
										</div>
				                    </div>
				                    <div class="card-footer border-bottom pb-3">
				                    	<form id="documentForm">
				                            <div class="mb-3">
				                                <label for="documentType" class="form-label">Document Type</label>
				                                <select class="form-select" id="documentType" name="documentType" required>
				                                    <option value="" selected disabled>Select document type</option>
				                                    <option value="contract">Contract</option>
				                                    <option value="memo">Legal Memo</option>
				                                    <option value="letter">Demand Letter</option>
				                                    <option value="pleading">Pleading</option>
				                                </select>
				                            </div>
				                            <div class="mb-3">
				                                <label for="prompt" class="form-label">Prompt</label>
				                                <textarea class="form-control" id="prompt" name="prompt" rows="4" placeholder="Describe the document you need..." required></textarea>
				                            </div>
				                            <button type="submit" class="btn btn-primary w-100" id="submitButton">Generate Document</button>
				                        </form>
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
    <!-- <script type="text/javascript" src="../assets/custom/matterDocs.js"></script> -->
    <script>
    	
		$('#documentForm').on('submit', function(e) {
		    e.preventDefault();
		    const formData = new FormData(this);
		    
		    $.ajax({
		        url: 'docs/generatedDoc',
		        type: 'POST',
		        data: formData,
		        processData: false,
		        contentType: false,
		        beforeSend: function() {
		            $('#submitButton').prop("disabled", true).html("Processing... Please wait.");
		        },
		        success: function(response) {
		            // Parse the JSON response if it's a string
		            if (typeof response === 'string') {
		                try {
		                    response = JSON.parse(response);
		                } catch (e) {
		                    console.error('Error parsing JSON:', e);
		                    sweetError('An error occurred while processing the response.');
		                    return;
		                }
		            }
		            
		            if (response.generatedText) {
		                // Display the generated document
		                $('#generatedDocument').html(response.generatedText);
		                $('#generatedDocumentSection').show();
		                
		                // Scroll to the document section
		                $('html, body').animate({
		                    scrollTop: $("#generatedDocumentSection").offset().top
		                }, 500);
		                
		                fetchGeneratedData();
		            } else if (response.error) {
		                sweetError('Error: ' + response.error);
		            } else {
		                sweetError('An unexpected error occurred.');
		            }
		        },
		        error: function(xhr, status, error) {
		            console.error('Error:', error);
		            sweetError('An error occurred while generating the document. Please try again.');
		        },
		        complete: function() {
		            $('#submitButton').prop("disabled", false).html("Submit");
		        }
		    });
		});

		// Add this function to handle copying the generated document
		$('#copyDocumentBtn').on('click', function() {
		    var documentText = $('#generatedDocument').text();
		    
		    if (navigator.clipboard) {
		        navigator.clipboard.writeText(documentText)
		            .then(() => sweetSuccess('Document copied to clipboard!'))
		            .catch(err => {
		                console.error('Failed to copy: ', err);
		                fallbackCopyTextToClipboard(documentText);
		            });
		    } else {
		        fallbackCopyTextToClipboard(documentText);
		    }
		});

		function fallbackCopyTextToClipboard(text) {
		    var textArea = document.createElement("textarea");
		    textArea.value = text;
		    document.body.appendChild(textArea);
		    textArea.focus();
		    textArea.select();

		    try {
		        var successful = document.execCommand('copy');
		        var msg = successful ? 'successful' : 'unsuccessful';
		        console.log('Fallback: Copying text command was ' + msg);
		        sweetSuccess('Document copied to clipboard!');
		    } catch (err) {
		        console.error('Fallback: Oops, unable to copy', err);
		        sweetError('Failed to copy. Please try again or copy manually.');
		    }

		    document.body.removeChild(textArea);
		}
    	function fetchGeneratedData(){
    		var fetchGeneratedDocs = "fetchGeneratedDocs";
    		$.ajax({
		        url: 'docs/fetchGeneratedDocs',
		        method:"POST", 
		        data:{fetchGeneratedDocs:fetchGeneratedDocs},
		        success: function(data) {
		            $('#fetchGeneratedDocs').html(data);
		        }
		        
		    });
    	}
    	fetchGeneratedData();

	    $(document).ready(function() {
		    $(document).on('click', '.showDocument', function(e) {
		        e.preventDefault();
		        var docId = $(this).data('doc-id');		        
		        $.ajax({
		            url: 'docs/getFullDocument',
		            method: 'POST',
		            data: { docId: docId },
		            dataType: 'json',
		            success: function(response) {
		                if (response.success) {
		                    $('#generatedDocument').html(response.document);
		                    $('#generatedDocumentSection').show();		                    
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

		    // Copy functionality
		    $('#copyDocumentBtn').on('click', function() {
		        var documentText = $('#generatedDocument').text();		        
		        var tempTextArea = $('<textarea>');
		        $('body').append(tempTextArea);
		        tempTextArea.val(documentText).select();
		        
		        try {
		            document.execCommand('copy');
		            sweetSuccess('Document copied to clipboard!');
		        } catch (err) {
		            console.error('Unable to copy', err);
		            sweetError('Failed to copy. Please try again or copy manually.');
		        } finally {
		            tempTextArea.remove();
		        }
		    });
		});

		$(document).ready(function() {
		    $(document).on('click', '.deleteDocument', function(e) {
		        e.preventDefault();
		        var docId = $(this).data('doc-id');
		        if (confirm('Are you sure you want to delete this document?')) {
		            $.ajax({
		                url: 'docs/deleteDocument',
		                type: 'POST',
		                data: { docId: docId },
		                dataType: 'json',
		                success: function(response) {
		                    if (response.success) {
		                        $(`button[data-doc-id="${docId}"]`).closest('li').remove();
		                        sweetSuccess('Document deleted successfully.');
		                    } else {
		                        sweetError('Error: ' + response.message);
		                    }
		                    fetchGeneratedData();
		                },
		                error: function() {
		                    sweetError('An error occurred while deleting the document.');
		                }
		            });
		        }
		    });
		});
	</script>
</body>
</html>