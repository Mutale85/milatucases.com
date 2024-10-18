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
        #docResult {
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
        .copy-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
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
					    <h4 class="fw-bold py-3 mb-4 border-bottom">AI Document Generation</h4>
					    <!-- <div id="docResult" class="mt-4 mb-4"></div> -->

					    <div class="position-relative mt-4 mb-5">
				            <div id="docResult"></div>
				            <button id="copyBtn" class="btn btn-secondary btn-sm copy-btn" style="display: none;"><i class="bi bi-copy"></i> Copy</button>
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
                var $form = $(this);
                var $button = $('#generateBtn');
                var $result = $('#docResult');
                var $copyBtn = $('#copyBtn');

                $button.prop('disabled', true).text('Generating...');
                $result.empty();
                $copyBtn.hide();

                $.ajax({
                    url: 'ai-features/parsers/ai_document_generate',
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.chunks) {
                            var i = 0;
                            function typeNextChunk() {
                                if (i < response.chunks.length) {
                                    if (response.chunks[i] === "\n") {
                                        $result.append('<br>');
                                    } else {
                                        $result.append(response.chunks[i]);
                                    }
                                    i++;
                                    setTimeout(typeNextChunk, 20); // Adjusted timing for smoother effect
                                } else {
                                    $button.prop('disabled', false).text('Generate Document');
                                    $copyBtn.show();
                                    $form[0].reset();
                                }
                            }
                            typeNextChunk();
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
            });

            $('#copyBtn').click(function() {
                var $temp = $("<textarea>");
                $("body").append($temp);
                $temp.val($('#docResult').text()).select();
                document.execCommand("copy");
                $temp.remove();
                
                $(this).text('Copied!');
                setTimeout(() => {
                    $(this).text('Copy');
                }, 2000);
            });
        });
	</script>
</body>
</html>