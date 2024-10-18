<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Active Cases</title>
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
          								<h5 class="card-title text-primary">All Active Cases</h5>
          							</div>
          							<div class="card-body">
          								<div class="table table-responsive">
          									<table class="table table-borderless" id="allTables">
          										<thead>
          											<tr>
          												<th>Client</th>
          												<th>Matter</th>
          												<th>Case Status</th>
          												<th>Case Date</th>
          												<th>Access</th>
          											</tr>
          										</thead>
          										<tbody>
											        <?php
												        $query = $connect->prepare("
												            SELECT * 
												            FROM `cases` 
												            WHERE `lawFirmId` = ? AND `caseStatus` = 'Active Case'
												        ");
												        $query->execute([$_SESSION['parent_id']]);
												        $cases = $query->fetchAll(PDO::FETCH_ASSOC);

												        foreach ($cases as $case) {
												        	$tpin = htmlspecialchars($case['clientId']);
												        	$caseNo = $case['caseNo'];
												        	$lawFirmId = $case['lawFirmId'];
												        	$caseTitle = $case['caseTitle'];
												        	if (userHasAccessToCase($_SESSION['user_id'], $caseNo, $_SESSION['parent_id'])) {?>
													            <tr>
														            <td><a href="cases/cases-details?caseId=<?php echo encrypt($case['id'])?>&caseNo=<?php echo encrypt($caseNo)?>&Tpin=<?php echo encrypt($tpin)?>"><?php echo getClientNameByTPIN($lawFirmId, $tpin)?></a></td>
														            <td><?php echo $caseTitle ?></td>
														            <td><?php echo htmlspecialchars($case['caseStatus']) ?></td>
														            <td><?php echo date("D d M, Y", strtotime($case['caseDate'])) ?></td>
														            <td><?php echo fetchCaseLayersAsAcronyms($caseNo)?></td>
													            </tr>
													    <?php    }
												        }
											        ?>
											    </tbody>
          									</table>
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

</body>
</html>