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
	<title>All Cases</title>
	<?php include '../addon_header.php'; ?>
	<?php 
			$query = $connect->prepare("
	    SELECT 
	        clientId, 
	        caseId, 
	        SUM(TIMESTAMPDIFF(SECOND, start_time, end_time)) as total_seconds,
	        SUM(total_amount) as total_amount
	    FROM 
	        task_billing 
	    WHERE 
	        lawFirmId = :lawFirmId
	    GROUP BY 
	        clientId, caseId
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
          						<div class="card">
          							<div class="card-header">
          								<h5 class="card-title text-primary">Logged Time</h5>
          							</div>
          							<div class="card-body">
          									<div class="table table-responsive">
          										<?php if (empty($results)): ?>
											            <p class="alert alert-info">No time logs found for this law firm.</p>
											        <?php else: ?>
											            <table class="table table-striped" id="allTables">
											                <thead>
											                    <tr>
											                    		<th>Total Time</th>
											                        <th>Client ID</th>
											                        <th>Case ID</th>
											                        <th>Total Amount</th>
											                    </tr>
											                </thead>
											                <tbody>
											                    <?php foreach ($results as $row): 
											                    		$clientNames = getClientNameById($row['clientId'], $lawFirmId);
											                    		$caseTitle = fetchCaseTitle($row['caseId'], $row['clientId']);
											                    		$caseNo = fetchCaseNumber($row['caseId'], $lawFirmId);
											                    	?>
											                        <tr>
											                        	<td>
											                                <a href="cases/track-timer?clientId=<?php echo $row['clientId']?>&caseId=<?php echo $row['caseId']?>"><?php
											                                $hours = floor($row['total_seconds'] / 3600);
											                                $minutes = floor(($row['total_seconds'] % 3600) / 60);
											                                $seconds = $row['total_seconds'] % 60;
											                                echo sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
											                                ?></a>
											                            </td>
											                            <td><a href="cases/track-timer?clientId=<?php echo $row['clientId']?>&caseId=<?php echo $row['caseId']?>"><?php echo html_entity_decode($clientNames); ?></a></td>
											                            <td><a href="cases/track-timer?clientId=<?php echo $row['clientId']?>&caseId=<?php echo $row['caseId']?>"><?php echo html_entity_decode($caseNo); ?></a></td>
											                            <td><?php echo number_format($row['total_amount'], 2); ?></td>
											                        </tr>
											                    <?php endforeach; ?>
											                </tbody>
											                <tfoot>
											                	<?php 
											                		$totalBillableTime = getTotalBillableTime($lawFirmId);
                                          $totalHours = $totalBillableTime['hours'];
                                          $totalMinutes = $totalBillableTime['minutes'];
                                          $time = "<small> {$totalHours} HRS : {$totalMinutes} MIN</small>";
											                	?>
											                	<tr>
											                		<td><?php echo $time?></td>
											                		<td></td>
											                		<td></td>
											                		<td></td>
											                	</tr>
											                </tfoot>
											            </table>
											        <?php endif; ?>
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
    <!-- <script type="text/javascript" src="../dist/controls/workflow.js"></script> -->
</body>
</html>