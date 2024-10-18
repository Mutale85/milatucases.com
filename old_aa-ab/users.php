<?php include '../includes/db.php';?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
  	<head>
    	<?php include 'addon_header.php'; ?>
  	</head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<!-- Menu -->
        	<?php include 'addon_side_nav.php'; ?>
        	<!-- / Menu -->

        	<div class="layout-page">
          		<?php include 'addon_top_nav.php'; ?>
          		<div class="content-wrapper">

		            <div class="container-xxl flex-grow-1 container-p-y">

		            	<div class="row">
									    <?php
												// Assuming $connect is your PDO database connection
												$members = fetchLawFirmMembers($_SESSION['parent_id']);
												?>

												<div class="row">
												    <div class="col-md-12">
												        <table class="table table-borderless">
												            <tr>
												                <th>Firm Members</th>
												                <?php if($_SESSION['user_role'] == 'superAdmin'):?>
												                <td align="right">
												                    <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
												                        <i class="bi bi-person-bounding-box"></i> Add New Member
												                    </button>
												                </td>
												              <?php endif?>
												            </tr>
												        </table>
												    </div>

												    <?php foreach ($members as $member): ?>
												        <?php
												            // Fetch the permissions for the current user
												            $permissions = [];
												            $sql = $connect->prepare("SELECT `edit_`, `delete_`, `add_`, `view_` FROM `permissions` WHERE `userId` = ?");
												            $sql->execute([$member['id']]);
												            $result = $sql->fetch(PDO::FETCH_ASSOC); // Make sure to use PDO::FETCH_ASSOC to get associative array
												            if ($result) {
												                $permissions = $result;
												            }
												        ?>
												        <div class="col-md-4 mb-4">
												            <div class="card">
												                <div class="card-body">
												                    <div class="d-flex align-items-center mb-3">
												                        <img src="<?php echo get_gravatar($member['email'])?>" class="rounded-circle me-3" width="50" height="50" alt="<?php echo htmlspecialchars($member['names']); ?>">
												                        <h5 class="card-title mb-0"><?php echo html_entity_decode($member['names']); ?></h5>
												                    </div>
												                    <p class="card-text">
												                        <strong>Email:</strong> <?php echo htmlspecialchars($member['email']); ?><br>
												                        <strong>Phone Number:</strong> <?php echo htmlspecialchars($member['phonenumber']); ?><br>
												                        <strong>Added Date:</strong> <?php echo date("D d M, Y", strtotime($member['joinDate'])); ?><br>
												                        <strong>Designation:</strong> <?php echo htmlspecialchars($member['job']); ?><br>
												                        <strong>Role:</strong> <?php echo htmlspecialchars($member['userRole']); ?>
												                    </p>

												                    <!-- Display permissions as checkboxes -->
												                    <div class="mb-3">
												                        <strong>Permissions:</strong><br>
												                        <div class="form-check">
												                            <input type="checkbox" class="form-check-input" id="viewPermission<?php echo $member['id']; ?>" disabled <?php echo $permissions['view_'] == 1 ? 'checked' : ''; ?>>
												                            <label class="form-check-label" for="viewPermission<?php echo $member['id']; ?>">View</label>
												                        </div>
												                        <div class="form-check">
												                            <input type="checkbox" class="form-check-input" id="addPermission<?php echo $member['id']; ?>" disabled <?php echo $permissions['add_'] == 1 ? 'checked' : ''; ?>>
												                            <label class="form-check-label" for="addPermission<?php echo $member['id']; ?>">Add</label>
												                        </div>
												                        <div class="form-check">
												                            <input type="checkbox" class="form-check-input" id="editPermission<?php echo $member['id']; ?>" disabled <?php echo $permissions['edit_'] == 1 ? 'checked' : ''; ?>>
												                            <label class="form-check-label" for="editPermission<?php echo $member['id']; ?>">Edit</label>
												                        </div>
												                        <div class="form-check">
												                            <input type="checkbox" class="form-check-input" id="deletePermission<?php echo $member['id']; ?>" disabled <?php echo $permissions['delete_'] == 1 ? 'checked' : ''; ?>>
												                            <label class="form-check-label" for="deletePermission<?php echo $member['id']; ?>">Delete</label>
												                        </div>
												                    </div>
												                    <div class="d-flex justify-content-end">
												                         <?php if($_SESSION['user_role'] == 'superAdmin'):?> 
												                          <a href="#" class="btn btn-primary btn-sm editMemberData" data-id="<?php echo $member['id']; ?>">
												                              <i class="bi bi-pen"></i> Edit
												                          </a>
												                          <button class="btn btn-danger btn-sm deleteMember ms-2" data-id="<?php echo $member['id']; ?>" data-phone="<?php echo $member['phonenumber']; ?>" data-email="<?php echo $member['email']; ?>" data-names="<?php echo $member['names']; ?>">
												                              <i class="bi bi-trash2"></i> Remove
												                          </button>
												                        	<?php endif?>
												                        
												                    </div>
												                </div>
												            </div>
												        </div>
												    <?php endforeach; ?>
												</div>

									</div>
		            </div>
            		<?php include 'addon_footer.php';?>

            		<div class="content-backdrop fade"></div>
          		</div>
        	</div>
      	</div>
      	<div class="layout-overlay layout-menu-toggle"></div>

      	<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
				    <div class="modal-dialog" role="document">
				        <div class="modal-content">
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
									            <label for="memberName">Names <i class="bi bi-braces-asterisk text-danger"></i></label>
									            <input type="text" class="form-control" id="memberName" name="names" required>
									        </div>
									        <div class="form-group mb-3">
									            <label for="memberEmail">Email <i class="bi bi-braces-asterisk text-danger"></i></label>
									            <input type="email" class="form-control" id="memberEmail" name="email" required>
									        </div>
									        <div class="form-group mb-3">
									            <label for="memberPhone">Phone Number <i class="bi bi-braces-asterisk text-danger"></i></label>
									            <input type="text" class="form-control" id="memberPhone" name="phonenumber" required value="260">
									        </div>
									        <div class="form-group mb-3">
									            <label for="desgination">Designation <i class="bi bi-braces-asterisk text-danger"></i></label>
									            <select class="form-control" id="desgination" name="job" required>
									                <option value="">Select</option>
									                <option value="Advocate">Advocate</option>
									                <option value="Lawyer">Lawyer / Intern</option>
									                <option value="Secretary">Secretary / Admin Officer / Front Desk</option>
									                <option value="Financial Officer">Financial Officer</option>
									            </select>
									        </div>
									        <div class="form-group mb-3">
									            <label for="accessRole">Access Role <i class="bi bi-braces-asterisk text-danger"></i></label>
									            <select class="form-control" id="accessRole" name="userRole" required>
									                <option value="">Select</option>
									                <option value="Admin">Admin</option>
									                <option value="User">User</option>
									                <option value="superAdmin">Super Admin</option>
									            </select>
									        </div>
									        <div class="form-group mb-3">
									            <label for="memberPassword">Password <i class="bi bi-braces-asterisk text-danger"></i></label>
									            <div class="input-group">
									                <input type="password" class="form-control" id="memberPassword" name="password" required>
									                <div class="input-group-append">
									                    <button type="button" class="btn btn-secondary" id="seePasswordBtn">See</button>
									                    <button type="button" class="btn btn-secondary" id="generatePasswordBtn">Generate</button>
									                </div>
									            </div>
									        </div>
									        <div class="form-group mb-3">
									            <label>Permissions</label>
									            <div class="form-check">
									                <input class="form-check-input" type="checkbox" id="checkAll" name="checkAll">
									                <label class="form-check-label" for="checkAll">Check All</label>
									            </div>
									            <div class="form-check">
									                <input class="form-check-input permission-checkbox" type="checkbox" id="addPermission" name="permissions[]" value="add">
									                <label class="form-check-label" for="addPermission">Add</label>
									            </div>
									            <div class="form-check">
									                <input class="form-check-input permission-checkbox" type="checkbox" id="editPermission" name="permissions[]" value="edit">
									                <label class="form-check-label" for="editPermission">Edit</label>
									            </div>
									            <div class="form-check">
									                <input class="form-check-input permission-checkbox" type="checkbox" id="deletePermission" name="permissions[]" value="delete">
									                <label class="form-check-label" for="deletePermission">Delete</label>
									            </div>
									            <div class="form-check">
									                <input class="form-check-input permission-checkbox" type="checkbox" id="viewPermission" name="permissions[]" value="view">
									                <label class="form-check-label" for="viewPermission">View</label>
									            </div>
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
    <?php include 'addon_footer_links.php';?>
    <script type="text/javascript" src="../dist/controls/users.js"></script>
    <script>
	    document.getElementById('checkAll').addEventListener('change', function() {
	        var isChecked = this.checked;
	        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
	            checkbox.checked = isChecked;
	        });
	    });

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