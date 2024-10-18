<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Corporate Clients</title>
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
          								<img id="companyLogo" src="" alt="Company Logo" style="display:none; max-width:200px;">
          							</div>
          							<div class="card-body">
          								<div id="companyData">

									        <h4><?php echo $_SESSION['lawFirmName']?></h4>
									        <p><strong></strong> <span id="companyName"></span></p>
									        <p><strong></strong> <span id="address"></span></p>
									        <p><strong></strong> <span id="postalCode"></span></p>
									        <p><strong></strong> <span id="telephone"></span></p>
									        <p><strong></strong> <span id="email"><?php echo $_SESSION['email']?></span></p>
									    </div>
          								
          							</div>
          							<div class="card-footer">
          								<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#companyInfoModal">
                							Add Firm Information
            							</button>
          							</div>
          							<div class="modal fade" id="companyInfoModal" tabindex="-1" aria-labelledby="companyInfoModalLabel" aria-hidden="true">
								        <div class="modal-dialog">
								            <div class="modal-content">
								                <div class="modal-header">
								                    <h5 class="modal-title" id="companyInfoModalLabel">Company Information Form</h5>
								                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								                </div>
								                <div class="modal-body">
													<form id="companyInfoForm" enctype="multipart/form-data">
											            <input type="hidden" id="lawFirmId" name="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
											            <div class="mb-3">
											                <label for="company_name" class="form-label">Company Name</label>
											                <input type="text" class="form-control" id="company_nameData" name="company_name" required value="<?php echo $_SESSION['lawFirmName']?>">
											            </div>
											            <div class="mb-3">
											                <label for="address_line1" class="form-label">Address</label>
											                <textarea type="text" class="form-control" id="addressData" name="address" placeholder="Enter Address" rows="3" required></textarea>
											            </div>
											            
											            <div class="mb-3">
											                <label for="postal_code" class="form-label">Postal Code</label>
											                <input type="text" class="form-control" id="postal_codeData" name="postal_code">
											            </div>
											            <div class="mb-3">
											                <label for="telephone" class="form-label">Telephone</label>
											                <input type="tel" class="form-control" id="telephoneData" name="telephone">
											            </div>
											            <div class="mb-3">
											                <label for="email" class="form-label">Email</label>
											                <input type="email" class="form-control" id="emailData" name="email" value="<?php echo $_SESSION['email']?>">
											            </div>
											            <div class="mb-3">
											                <label for="logo" class="form-label">Company Logo</label>
											                <input type="file" class="form-control" id="logo" name="logo">
											            </div>
											            <button type="submit" class="btn btn-primary">Update Firm Details</button>
											        </form>
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
		    // Function to fetch company data
		    function fetchCompanyData(lawFirmId) {
		        $.ajax({
		            url: 'settings/fetchLawFirmData', // PHP script to fetch data
		            type: 'POST',
		            data: { lawFirmId: lawFirmId },
		            dataType: 'json',
		            success: function(response) {
		                if(response.success) {
		                    // Display the company data
		                    $('#companyName').text(response.data.company_name);
		                    $('#address').text(response.data.address.replace(/&#13;&#10;/g, '\n'));
		                    $('#postalCode').text(response.data.postal_code);
		                    $('#telephone').text(response.data.telephone);
		                    $('#email').text(response.data.email);
		                    
		                    // Display the logo
		                    if (response.data.logo) {
		                        $('#companyLogo').attr('src', 'settings/'+response.data.logo).show();
		                    } else {
		                        $('#companyLogo').hide();
		                    }

		                    $('#company_nameData').val(response.data.company_name);
		                    $('#addressData').val(response.data.address.replace(/&#13;&#10;/g, '\n'));
		                    $('#postal_codeData').val(response.data.postal_code);
		                    $('#telephoneData').val(response.data.telephone);
		                    $('#emailData').val(response.data.email);

		                } else {
		                    // alert('No company data found.');
		                    $('#companyLogo').hide();
		                }
		            },
		            error: function(xhr, status, error) {
		                console.error(xhr.responseText);
		                alert('An error occurred while fetching company data.');
		                $('#companyLogo').hide();
		            }
		        });
		    }

		    // Fetch company data for a specific lawFirmId
		    var lawFirmId = document.getElementById('lawFirmId').value; // Replace with the actual lawFirmId you want to fetch data for
		    fetchCompanyData(lawFirmId);
		
		    $('#companyInfoForm').on('submit', function(e) {
		        e.preventDefault(); // Prevent the default form submission

		        // Create a FormData object to handle file upload
		        var formData = new FormData(this);

		        $.ajax({
		            url: 'settings/processCompanyInfo', // PHP script to process the form data
		            type: 'POST',
		            data: formData,
		            contentType: false,
		            processData: false,
		            success: function(response) {
		                var result = JSON.parse(response);
		                if (result.success) {
		                    sweetSuccess('Company details updated successfully.');
		                    fetchCompanyData(lawFirmId);
		                    // You can also update the UI with the new details if needed
		                    $("#companyInfoModal").modal("hide");
		                } else {
		                    sweetError('Error: ' + result.message);
		                }
		            },
		            error: function(xhr, status, error) {
		                console.error(xhr.responseText);
		                sweetError('An error occurred while updating company details.');
		            }
		        });
		    });
		});
    </script>
</body>
</html>