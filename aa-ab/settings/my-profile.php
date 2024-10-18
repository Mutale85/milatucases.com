<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['names']?> Profile</title>
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
          							<div class="card">
									  <div class="card-header row">
									    <div class="col-md-4">
									      	<img id="userpicture" src="<?php echo get_gravatar($_SESSION['email'])?>" alt="user picture" style="display:block; max-width:200px;">
									    </div>
									    <div class="col-md-8" align="right">
									      <div id="userData">
									        <h4><?php echo $_SESSION['names']?></h4>
									        <strong></strong> <span id="userTpin"></span><br>
									        <strong></strong> <span id="address"></span><br>
									        <strong></strong> <span id="postalCode"></span><br>
									        <strong></strong> <span id="phone"></span><br>
									        <strong></strong> <span id="email"><?php echo $_SESSION['email']?></span><br>
									        <strong></strong> <span id="website"></span><br>
									        <strong></strong> <span id="linkedin"></span><br>
									      </div>
									    </div>
									  </div>
									</div>
          							<div class="card-footer">
          								<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                							Add Firm Information
            							</button>
          							</div>
          							<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
								        <div class="modal-dialog">
								            <div class="modal-content">
								                <div class="modal-header">
								                    <h5 class="modal-title" id="userInfoModalLabel">Personal Information Form</h5>
								                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								                </div>
								                <div class="modal-body">
													<form id="userInfoForm" enctype="multipart/form-data">
											            <input type="hidden" id="userId" name="userId" value="<?php echo $_SESSION['user_id']?>">
											            <div class="mb-3">
											            	<label>Title</label>
											            	<select class="form-control" name="title" id="title">
											            		<option value="">Select</option>
											            		<option value="Mr">Mr</option>
											            		<option value="Mrs">Mrs</option>
											            		<option value="Miss">Miss</option>
											            		<option value="Dr">Dr</option>
											            		<option value="Rev">Rev</option>
											            		<option value="Prof">Prof</option>
											            	</select>
											            </div>
											            <div class="mb-3">
											                <label for="user_name" class="form-label">Names</label>
											                <input type="text" class="form-control" id="user_nameData" name="user_name" required value="<?php echo $_SESSION['names']?>">
											            </div>
											            
											            <div class="mb-3">
											                <label for="phone" class="form-label">Phone</label>
											                <input type="tel" class="form-control" id="phoneData" name="phone">
											            </div>
											            <div class="mb-3">
											                <label for="email" class="form-label">Email</label>
											                <input type="email" class="form-control" id="emailData" name="email" value="<?php echo $_SESSION['email']?>">
											            </div>
											            <div class="mb-3">
											            	<label>Reset Password</label>
											            	<input type="password" name="password" id="password" class="form-control">
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
		    // Function to fetch user data
		    function fetchPersonalData(userId) {
		        $.ajax({
		            url: 'settings/fetchUserData', // PHP script to fetch data
		            type: 'POST',
		            data: { userId: userId },
		            dataType: 'json',
		            success: function(response) {
		                if(response.success) {
		                    // Display the user data
		                    $('#phone').text(response.data.phonenumber);
		                    $('#email').text(response.data.email);

		                    // if (response.data.picture) {
		                    //     $('#userpicture').attr('src', 'settings/'+response.data.picture).show();
		                    // } else {
		                    //     $('#userpicture').hide();
		                    // }

		                    $('#user_nameData').val(response.data.names);
		                    $('#phoneData').val(response.data.phonenumber);
		                    $('#emailData').val(response.data.email);

		                } else {
		                    // alert('No user data found.');
		                    // $('#userpicture').hide();
		                }
		            },
		            error: function(xhr, status, error) {
		                console.error(xhr.responseText);
		                alert('An error occurred while fetching user data.');
		                // $('#userpicture').hide();
		            }
		        });
		    }

		    var userId = document.getElementById('userId').value;
		    fetchPersonalData(userId);
		
		    $('#userInfoForm').on('submit', function(e) {
		        e.preventDefault(); // Prevent the default form submission

		        // Create a FormData object to handle file upload
		        var formData = new FormData(this);

		        $.ajax({
		            url: 'settings/processUserInfo', // PHP script to process the form data
		            type: 'POST',
		            data: formData,
		            contentType: false,
		            processData: false,
		            success: function(response) {
		                var result = JSON.parse(response);
		                if (result.success) {
		                    sweetSuccess('user details updated successfully.');
		                    fetchPersonalData(userId);
		                    // You can also update the UI with the new details if needed
		                    $("#userInfoModal").modal("hide");
		                } else {
		                    sweetError('Error: ' + result.message);
		                }
		            },
		            error: function(xhr, status, error) {
		                console.error(xhr.responseText);
		                sweetError('An error occurred while updating user details.');
		            }
		        });
		    });
		});
    </script>
</body>
</html>