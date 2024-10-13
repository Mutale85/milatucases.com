<?php
	include "../../includes/db.php";
	include '../base/base.php';
	$lawFirmId = $_SESSION['parent_id'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
	<title><?php echo $_SESSION['lawFirmName']?>'s - Milatucases Account</title>
	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="../sampleLogo.png" />
	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
	<link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
	<link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
	<link rel="stylesheet" href="../assets/css/demo.css" />
	<!-- Vendors CSS -->
	<link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
	<link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
	<!-- DataTables -->
	<link rel="stylesheet" type="text/css" href="../assets/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="../assets/css/buttons.dataTables.min.css">
	<link rel="stylesheet" href="../assets/css/jquery-ui.css">
	<!-- Summer Note -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css" />
	<!-- Helpers -->
	<script src="../assets/vendor/js/helpers.js"></script>
	<script src="../assets/js/config.js"></script>
	<!-- Telephone -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
	<style>
	    body {
	      color: #000;
	      padding-bottom: 60px
	    }
	    table th {
	        text-transform: unset !important;
	    }
	    table td {
	        text-transform: unset !important;
	    }
	    a:link, a:active {
	      text-decoration: none;
	    }

	    #timer-container {
	        margin-bottom: 20px;
	    }

	    input[type="text"] {
	        width: calc(100%);
	        padding: 10px;
	        margin-right: 10px;
	        border: 1px solid #ddd;
	        border-radius: 4px;
	    }

	    .timer {
	        display: flex;
	        align-items: center;
	        margin-bottom: 10px;
	    }

	    .timer-description {
	        flex: 1;
	    }

	    .timer-controls {
	        margin-left: 10px;
	    }

	    .timer-controls button {
	        margin-left: 5px;
	    }

	    .sticky-footer {
	        position: fixed;
	        bottom: 0;
	        right: 0;
	        width: 100%;
	        background: #f8f9fa;
	        border-top: 1px solid #dee2e6;
	        padding: 10px;
	        z-index: 1000;
	    }
	    .timer-display {
	        font-weight: bold;
	    }
	    .action-buttons {
	        display: none;
	    }
	    .action-buttons.show {
	        display: block;
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
								<div class="card">
									<div class="card-header" id="subscription-info"></div>
									<div class="card-body">
									<!-- Subscription details will be displayed here -->
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
			const lawFirmId = "<?php echo $_SESSION['parent_id']?>";
			$.ajax({
				url: 'billings/fetch_subscription', // PHP script to fetch the data
				type: 'POST',
				data: { lawFirmId: lawFirmId },
				dataType: 'json',
				success: function(data) {
					if(data.profile_locked) {
						$('#subscription-info').html('<p>Your profile is locked due to expired subscription.</p>');
					} else {
						$('#subscription-info').html('<p>Time remaining: ' + data.remaining_time + '</p>');
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error: ' + error);
				}
			});
		});
	</script>
</body>
</html>