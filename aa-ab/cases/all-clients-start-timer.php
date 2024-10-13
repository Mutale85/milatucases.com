<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Start Timers</title>
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
          								<h5 class="card-title text-primary">All Matters</h5>
          							</div>
          							<div class="card-body">
          								<div class="table table-responsive">
          									<table class="table table-striped table-sm" id="allTables">
          										<thead>
          											<tr>
          												<th>Start Timer</th>
          												<th>Client</th>
          												<th>Matter No</th>
          												<th>Matter Date</th>
          												<th>Responsible</th>
          											</tr>
          										</thead>
          										<tbody>
											        <?php
												        $query = $connect->prepare("
												            SELECT * 
												            FROM `cases` 
												            WHERE `lawFirmId` = ?
												        ");
												        $query->execute([$_SESSION['parent_id']]);
												        $cases = $query->fetchAll(PDO::FETCH_ASSOC);

												        foreach ($cases as $case) {
												        	$tpin = htmlspecialchars($case['client_tpin']);
												        	$clientId = $case['clientId'];
												        	$caseNo = $case['caseNo'];
												        	$lawFirmId = $case['lawFirmId'];
												        	$caseTitle = $case['caseTitle'];
												        	$caseId = $case['id'];
												        	$status = $case['caseStatus'];
												        	$date = $case['caseDate'];
												        	if (userHasAccessToCase($_SESSION['user_id'], $caseId, $_SESSION['parent_id'])) {?>
													            <tr>
													            	<td><a href="cases/track-timer?clientId=<?php echo $clientId?>&caseId=<?php echo $caseId?>"><i class="bi bi-alarm"></i> Time Your Self</a></td>
														            <td><a href="cases/cases-details?caseId=<?php echo $caseId?>&caseNo=<?php echo encrypt($caseNo)?>&clientId=<?php echo encrypt($clientId)?>"><?php echo getClientNameById($clientId, $lawFirmId)?> </a></td>
														            <td><a href="cases/cases-details?caseId=<?php echo $caseId?>&caseNo=<?php echo encrypt($caseNo)?>&clientId=<?php echo encrypt($clientId)?>"><?php echo $caseNo?></a></td>
														            
														            <td><?php echo date("D d M, Y", strtotime($date)) ?></td>
														            <td>
														            	<small><?php echo fetchCaseLayersAsFullNames($caseId)?></small>
														            </td>
														            
													            </tr>
													    <?php
															}
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