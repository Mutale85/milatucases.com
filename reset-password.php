<!DOCTYPE html>
<html
  lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Password Reset | Milatu cases - Your legal management system</title>

    <meta name="description" content="" />

    <!-- Favicon -->
  	<link rel="icon" type="image/png" href="sampleLogo.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"/>

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
      body {
        background-color: aliceblue;
      }
    </style>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              	<div class="app-brand justify-content-center">
	                <a href="./" class="app-brand-link gap-2">
	                  <span class="app-brand-logo demo text-center">
	                    <img src="sampleLogo.png" class="img-fluid" style="width:140px;height: 140px; border-radius:50%;">
	                  </span>
	                </a>
              	</div>
              	<h4 class="mb-3">Reset your password</h4>
              	<form id="resetPasswordForm" class="mb-3" action="" method="POST">
				    
				    <div class="mb-3">
				        <label for="newPassword" class="form-label">New Password</label>
				        <div class="input-group">
				            <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter new password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.">
				            <div class="input-group-append">
				                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
				                    <i class="bi bi-eye-slash"></i>
				                </button>
				            </div>
				        </div>
				    </div>
				    <div class="mb-3">
				        <label for="confirmPassword" class="form-label">Confirm Password</label>
				        <div class="input-group">
				            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
				            <div class="input-group-append">
				                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
				                    <i class="bi bi-eye-slash"></i>
				                </button>
				            </div>
				        </div>
				        <input type="hidden" name="passtoken" value="<?php echo $_GET['token']?>">
				        <input type="hidden" name="email" value="<?php echo $_GET['email']?>">
				    </div>
				    <p class="text-dark">Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.</p>
				    <div class="mb-3">
				        <button class="btn btn-primary d-grid w-100" id="resetPasswordBtn" type="submit">Submit</button>
				    </div>
				</form>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        
        $(document).ready(function() {
		    $('#toggleNewPassword').on('click', function() {
		        var passwordInput = $("#newPassword");
		        if (passwordInput.attr('type') === 'password') {
		            passwordInput.attr('type', 'text');
		            $(this).find('i').removeClass('bi-eye-slash').addClass('bi-eye');
		        } else {
		            passwordInput.attr('type', 'password');
		            $(this).find('i').removeClass('bi-eye').addClass('bi-eye-slash');
		        }
		    });

		    $('#toggleConfirmPassword').on('click', function() {
		        var passwordInput = $("#confirmPassword");
		        if (passwordInput.attr('type') === 'password') {
		            passwordInput.attr('type', 'text');
		            $(this).find('i').removeClass('bi-eye-slash').addClass('bi-eye');
		        } else {
		            passwordInput.attr('type', 'password');
		            $(this).find('i').removeClass('bi-eye').addClass('bi-eye-slash');
		        }
		    });

		    $('#resetPasswordForm').submit(function(e) {
		        e.preventDefault();
		        var newPassword = $('#newPassword').val();
		        var confirmPassword = $('#confirmPassword').val();

		        if (newPassword !== confirmPassword) {
		            sweetError('Passwords do not match.');
		            return;
		        }
		        var resetPasswordForm = $(this).serialize();
		        $.ajax({
		            type: 'POST',
		            url: 'parsers/reset-password',
		            data:resetPasswordForm,
		            beforeSend:function(){
                    	$("#resetPasswordBtn").prop("disabled", true).html("<i class='bi bi-hourglass-top'></i> Processing....");
                	},
		            success: function(response) {
		            	
	                	alert(response  + " Redirecting you to login");
	                	setTimeout(function(){
	                		window.location = 'login';
	                	}, 1500);
		            	
		            	$("#resetPasswordBtn").prop("disabled", false).html("Submit");
		            },
		            error: function(xhr, status, error) {
		                alert('Error: ' + error);
		                $("#resetPasswordBtn").prop("disabled", false).html("Submit");
		            }
		        });
		    });
		});

        
    </script>
  </body>
</html>