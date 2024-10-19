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
	<title>Time Entries</title>
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
                                        <h5 class="card-title">AI Time Entry</h5>
                                    </div>
                                    <div class="card-body">
                                        <h4>Coming Soon</h4>
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
</body>
</html>