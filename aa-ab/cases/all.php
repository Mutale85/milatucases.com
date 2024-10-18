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
	<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.3/css/dataTables.dateTime.min.css"> -->
    <!-- Your other CSS files -->

    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.8.0/css/searchBuilder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.3/css/dataTables.dateTime.min.css"> -->
    
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
          									<!-- <div class="row align-items-center g-3">
														    <div class="col-md-6">
														        <div class="input-group">
														            <span class="input-group-text">Minimum date:</span>
														            <input type="text" class="form-control" id="min" name="min">
														        </div>
														    </div>
														    <div class="col-md-6">
														        <div class="input-group">
														            <span class="input-group-text">Maximum date:</span>
														            <input type="text" class="form-control" id="max" name="max">
														        </div>
														    </div>
														</div> -->
          									<table class="table table-striped table-sm" id="example">
          										<thead>
          											<tr>
          												<th>Matter ID</th>
          												<th>Client</th>
          												<th>Matter Title</th>
          												<th>Matter Status</th>
          												<th>Start Date</th>
          												<th>Matter Documents</th>
          												<th>Reponsible</th>
          												<th>Matter Category</th>
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
												        	
												        	if($case['caseCategory'] == ""){
												        		$caseCategory = "";
												        	}else{
												        		$caseCategory = decrypt($case['caseCategory']);
												        	}
												        	$caseId = $case['id'];
												        	$status = $case['caseStatus'];
												        	$date = $case['caseDate'];
												        	if (userHasAccessToCase($_SESSION['user_id'], $caseId, $_SESSION['parent_id'])) {?>
													            <tr>
														            <td><a href="cases/cases-details?caseId=<?php echo base64_encode($caseId)?>&caseNo=<?php echo base64_encode($caseNo)?>&clientId=<?php echo base64_encode($clientId)?>"><?php echo $caseNo?></a></td>
														            <td><?php echo getClientNameById($clientId, $lawFirmId)?></td>
														            <td><?php echo decrypt($caseTitle) ?></td>
														            <td><?php echo htmlspecialchars($status)?> </td>
														            <td><small><?php echo date("Y-m-d", strtotime($date)) ?></small></td>
														            <td><?php echo countDocumentsinCase($caseId, $lawFirmId)?></td>
														            <td><small><?php echo fetchCaseLayersAsFullNames($caseId)?></small></td>
														            <td><?php echo ucwords($caseCategory)?></td>
														           
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

    <script>
				$(document).ready(function() {
				    new DataTable('#example', {
				        layout: {
						        top1: 'searchBuilder'
						    },
				        
				        // Enable individual column searching
				        initComplete: function () {
				            this.api().columns().every(function () {
				                var column = this;
				                var select = $('<select><option value=""></option></select>')
				                    .appendTo($(column.footer()).empty())
				                    .on('change', function () {
				                        var val = $.fn.dataTable.util.escapeRegex(
				                            $(this).val()
				                        );
				 
				                        column
				                            .search(val ? '^' + val + '$' : '', true, false)
				                            .draw();
				                    });
				 
				                column.data().unique().sort().each(function (d, j) {
				                    select.append('<option value="' + d + '">' + d + '</option>')
				                });
				            });
				        }
				    });
				});
		</script>
</body>
</html>