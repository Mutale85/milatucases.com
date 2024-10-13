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
		$clientId = isset($_GET['clientId']) ? $_GET['clientId'] : null;

		if (!$clientId) {
		    die("No client ID provided.");
		}

		// Fetch fee notes for the specific client
		$query = $connect->prepare("
		    SELECT 
		        id, case_id, case_no, file_path, file_size, created_at
		    FROM 
		        fee_notes 
		    WHERE 
		        lawFirmId = :lawFirmId AND clientId = :clientId
		    ORDER BY 
		        created_at DESC
		");
		$query->bindParam(':lawFirmId', $lawFirmId);
		$query->bindParam(':clientId', $clientId, PDO::PARAM_INT);
		$query->execute();
		$feeNotes = $query->fetchAll(PDO::FETCH_ASSOC);
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
							        	<h5 class="card-title mb-4">Fee Notes for: <?php echo html_entity_decode(getClientNameById($clientId, $lawFirmId)); ?></h5>
							        </div>
							        <div class="card-body">
								        <?php if (empty($feeNotes)): ?>
								            <p class="alert alert-info">No fee notes found for this client.</p>
								        <?php else: ?>
								            <table class="table table-striped">
								                <thead>
								                    <tr>
								                        <th>Case ID</th>
								                        <th>Case No</th>
								                        <th>File Size</th>
								                        <th>Created At</th>
								                        <th>Actions</th>
								                    </tr>
								                </thead>
								                <tbody>
								                    <?php foreach ($feeNotes as $note): 
								                    	$caseTitle = fetchCaseTitle($note['case_id'], $clientId);;
								                    ?>
								                        <tr>
								                            <td><?php echo html_entity_decode($caseTitle); ?></td>
								                            <td><?php echo htmlspecialchars($note['case_no']); ?></td>
								                            <td><?php echo formatFileSize($note['file_size']); ?></td>
								                            <td><?php echo htmlspecialchars($note['created_at']); ?></td>
								                            <td>
								                                <a href="cases/<?php echo htmlspecialchars($note['file_path']); ?>" 
								                                   class="btn btn-primary btn-sm" target="_blank">View File</a>
								                            </td>
								                        </tr>
								                    <?php endforeach; ?>
								                </tbody>
								            </table>
								        <?php endif; ?>
								        <a href="javascript:history.back()" class="btn btn-secondary btn-sm mt-4">Back to Summary</a>
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