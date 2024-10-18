<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-semi-dark"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
  <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName']?> Fee Notes</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		$query = $connect->prepare("
    SELECT 
        clientId, 
        COUNT(*) as note_count,
        SUM(file_size) as total_file_size
    FROM 
        fee_notes 
    WHERE 
        lawFirmId = :lawFirmId
    GROUP BY 
        clientId
");
	$query->bindParam(':lawFirmId', $lawFirmId);
	$query->execute();
	$results = $query->fetchAll(PDO::FETCH_ASSOC);

	
	?>
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
          						 <div class="card mt-5">
          						 		<div class="card-header">
									        	<h5 class="card-title mb-4">Client Fee Notes Summary</h5>
									        </div>
									        <div class="card-body">
										        <?php if (empty($results)): ?>
										            <p class="alert alert-info">No fee notes found for this law firm.</p>
										        <?php else: ?>
										            <table class="table table-striped">
										                <thead>
										                    <tr>
										                        <th>Client ID</th>
										                        <th>Number of Fee Notes</th>
										                        <th>Total File Size</th>
										                        <th>Actions</th>
										                    </tr>
										                </thead>
										                <tbody>
										                    <?php foreach ($results as $row): 
										                    	$clientNames = getClientNameById($row['clientId'], $lawFirmId);
										                    	?>
										                        <tr>
										                            <td><?php echo html_entity_decode($clientNames); ?></td>
										                            <td><?php echo $row['note_count']; ?></td>
										                            <td><?php echo formatFileSize($row['total_file_size']); ?></td>
										                            <td>
										                                <a href="docs/view_client_fee_notes?clientId=<?php echo urlencode($row['clientId']); ?>" 
										                                   class="btn btn-primary btn-sm">View Fee Notes</a>
										                            </td>
										                        </tr>
										                    <?php endforeach; ?>
										                </tbody>
										            </table>
										        <?php endif; ?>
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