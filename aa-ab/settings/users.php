<?php 
	include '../../includes/db.php';
	include '../base/base.php';

?>
<!DOCTYPE html>
	<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
  	<head>
    	<?php include '../addon_header.php'; ?>
  	</head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<!-- Menu -->
        	<?php include '../addon_side_nav.php'; ?>
        	<!-- / Menu -->

        	<div class="layout-page">
          		<?php include '../addon_top_nav.php'; ?>
          		<div class="content-wrapper">

		            <div class="container-xxl flex-grow-1 container-p-y">

		            	<div class="row" id="teamMembersContainer"></div>
		            </div>
            		<?php include '../addon_footer.php';?>

            		<div class="content-backdrop fade"></div>
          		</div>
        	</div>
      	</div>
      	<div class="layout-overlay layout-menu-toggle"></div>

      	<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
				    <div class="modal-dialog modal-lg" role="document">
				        <div class="modal-content ">
				            <div class="modal-header">
				                <h5 class="modal-title" id="addMemberModalLabel">Add New Member</h5>
				                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
				                </button>
				            </div>
				            <div class="modal-body">
											<form id="addMemberForm">
									        <input type="hidden" id="parentId" name="parentId" value="<?php echo $_SESSION['parent_id']; ?>">
									        <input type="hidden" name="firmName" id="firmName" value="<?php echo $_SESSION['lawFirmName']?>">
									        <div class="form-group mb-3">
									            <label for="memberName"><i class="bi bi-braces-asterisk text-danger"></i> Title & Names </label>
									            <div class="input-group">
									            	<select class="form-control" name="title" id="title" required>
									            		<option value="">Select Title</option>
									            		<option value="Mr">Mr</option>
									            		<option value="Mrs">Mrs</option>
									            		<option value="Miss">Miss</option>
									            		<option value="Dr">Dr</option>
									            		<option value="Prof">Prof</option>
									            		<option value="State Councel">State Councel</option>
									            	</select>
									            	<input type="text" class="form-control" id="memberName" name="names" required placeholder="Enter User Names">
									            </div>
									        </div>
									        <div class="form-group mb-3">
									            <label for="memberEmail"><i class="bi bi-braces-asterisk text-danger"></i> Email </label>
									            <input type="email" class="form-control" id="memberEmail" name="email" required>
									        </div>
									        <div class="form-group mb-3">
									            <label for="memberPhone"><i class="bi bi-braces-asterisk text-danger"></i> Phone Number </label>
									            <input type="text" class="form-control" id="memberPhone" name="phonenumber" required value="260">
									        </div>
									        <div class="form-group mb-3">
									            <label for="desgination"><i class="bi bi-braces-asterisk text-danger"></i> Designation </label>
									            <select class="form-control" id="desgination" name="job" required>
									                <option value="">Select</option>
									                <option value="Advocate">Advocate</option>
									                <option value="Lawyer">Lawyer / Intern</option>
									                <option value="Secretary">Secretary / Admin Officer / Front Desk</option>
									                <option value="Financial Officer">Financial Officer</option>
									            </select>
									        </div>
									        
									        <div class="form-group mb-3">
									            <label for="memberPassword"><i class="bi bi-braces-asterisk text-danger"></i> Password </label>
									            <div class="input-group">
									                <input type="password" class="form-control" id="memberPassword" name="password" required>
									                <div class="input-group-append">
									                    <button type="button" class="btn btn-secondary" id="seePasswordBtn">See</button>
									                    <button type="button" class="btn btn-secondary" id="generatePasswordBtn">Generate</button>
									                </div>
									            </div>
									        </div>
									        <div class="form-group mb-3">
													    <label>Permissions (Select at least one)</label>
													    <div class="form-check">
													        <input class="form-check-input permission-checkbox" type="checkbox" id="adminPermission" name="permissions" value="superAdmin">
													        <label class="form-check-label" for="adminPermission">Administrator (Can use all functionalities)</label>
													    </div>
													    <div class="form-check">
													        <input class="form-check-input permission-checkbox" type="checkbox" id="legalAdminPermission" name="permissions" value="Legal Admin">
													        <label class="form-check-label" for="legalAdminPermission">Legal Admin (All Legal Matters, Not allowed on Finances)</label>
													    </div>
													    <div class="form-check">
													        <input class="form-check-input permission-checkbox" type="checkbox" id="financeAdminPermission" name="permissions" value="Finance Officer">
													        <label class="form-check-label" for="financeAdminPermission">Finance Admin (All Finance Matters, Not allowed on Legal Matters)</label>
													    </div>
													    <div class="form-check">
													        <input class="form-check-input permission-checkbox" type="checkbox" id="generalAdminPermission" name="permissions" value="General Administrator">
													        <label class="form-check-label" for="generalAdminPermission">General Admin (Can Add Users, Add Legal Matters, Not allowed on Finances)</label>
													    </div>
													    <div class="invalid-feedback">Please select at least one permission.</div>
													</div>
									        <div class="mb-3">
									            <button type="submit" class="btn btn-primary" id="submitMember">Add Member</button>
									        </div>
									        <div class="mt-3" id="answerDiv"></div>
									    </form>
				            </div>
				        </div>
				    </div>
				</div>
    </div>
    <?php include '../addon_footer_links.php';?>
    <script type="text/javascript" src="../dist/controls/users.js"></script>
    <script>
	    

	    document.getElementById('seePasswordBtn').addEventListener('click', function() {
	        var passwordInput = document.getElementById('memberPassword');
	        if (passwordInput.type === "password") {
	            passwordInput.type = "text";
	            this.textContent = "Hide";
	        } else {
	            passwordInput.type = "password";
	            this.textContent = "See";
	        }
	    });

	    document.getElementById('generatePasswordBtn').addEventListener('click', function() {
	        var passwordInput = document.getElementById('memberPassword');
	        var generatedPassword = Math.random().toString(36).slice(-8);
	        passwordInput.value = generatedPassword;
	        passwordInput.type = "text";
	        document.getElementById('seePasswordBtn').textContent = "Hide";
	    });
    </script>
</body>
</html>