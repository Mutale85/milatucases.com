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
	<title>AI-Powered Features</title>
	<?php include '../addon_header.php'; ?>
	
	<!-- Custom CSS for AI Links -->
	<style>
		.ai-links-container {
			margin: 20px;
			display: flex;
			flex-wrap: wrap;
			justify-content: space-around;
		}
		.ai-link-box {
			background-color: #f1f1f1;
			border-radius: 8px;
			padding: 20px;
			width: 300px;
			margin: 10px;
			text-align: center;
			box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
			transition: transform 0.3s ease;
		}
		.ai-link-box:hover {
			transform: scale(1.05);
		}
		.ai-link-box a {
			text-decoration: none;
			color: #007bff;
			font-weight: bold;
			font-size: 18px;
		}
		.ai-link-box a:hover {
			color: #0056b3;
		}
		.ai-link-box p {
			color: #333;
			margin-top: 10px;
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
          				
          				<!-- AI Links Section -->
          				<div class="ai-links-container">
          					
          					<!-- Automated Document Generation Link -->
          					<div class="ai-link-box">
          						<a href="ai-features/document-generation">Automated Document Generation</a>
          						<p>Create legal documents using templates and client data instantly.</p>
          					</div>

          					<!-- AI-Powered Time Tracking Link -->
          					<div class="ai-link-box">
          						<a href="ai-features/time-tracking.php">AI-Powered Time Tracking</a>
          						<p>Automatically track billable hours based on user activity.</p>
          					</div>

          					<!-- Smart Task Management Link -->
          					<div class="ai-link-box">
          						<a href="ai-features/task-management.php">Smart Task Management</a>
          						<p>Manage and prioritize tasks intelligently with AI-driven insights.</p>
          					</div>

          					<!-- Predictive Analytics Link -->
          					<div class="ai-link-box">
          						<a href="ai-features/predictive-analytics.php">Predictive Analytics</a>
          						<p>Analyze past cases and client data to predict case outcomes.</p>
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

</body>
</html>
