<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName']?> Invoice List</title>
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
          								<h5 class="card-title">Created Invoice </h5>
          							</div>
          							<div class="card-body">
									    <?php 
									        $query = $connect->prepare("SELECT * FROM lawFirmInvoices WHERE lawFirmId = ? ");
									        $query->execute([$_SESSION['parent_id']]);
									        $invoices = $query->fetchAll(PDO::FETCH_ASSOC);
									    ?>
									    <div class="table table-responsive">
										    <table class="table table-striped" id="allTables">
										        <thead>
										            <tr>
										                <th>Client</th>
										                <th>File Link</th>
										                <th>File Size (MB)</th>
										                <th>Date Created</th>
										                <th>Amount (ZMW)</th>
										            </tr>
										        </thead>
										        <tbody>
										            <?php if ($invoices): ?>
										                <?php foreach ($invoices as $invoice): 
										                	$link = preg_replace("#[^0-9.A-Za-z-]#", " ", $invoice['pdfFilePath']);
										                	$link = removeFirstWord($link);
										                	$amount = totalInvoiceAmountByCaseId($invoice['invoice_id']);
										                ?>
										                    <tr>
										                        <td><?php echo getClientNameById($invoice['clientId'], $invoice['lawFirmId']); ?></td>
										                        <td><a href="inv/<?php echo htmlspecialchars($invoice['pdfFilePath']); ?>" target="_blank">
										                        	<?php echo ucwords($link) ?></a>
										                        </td>
										                        <td><?php echo number_format($invoice['fileSize'] / 1024, 2); ?> MB</td>
										                        <td><?php echo date("D d M, Y", strtotime($invoice['date_created'])); ?> <em><small>(<?php echo time_ago_check($invoice['date_created']) ?>)</small></em></td>
										                        <td><?php echo number_format($amount, 2) ?></td>
										                    </tr>
										                <?php endforeach; ?>
										            <?php else: ?>
										                <tr>
										                    <td colspan="5">No invoices found.</td>
										                </tr>
										            <?php endif; ?>
										        </tbody>
										        <tfoot>
										        	<tr>
										        		<th>Total Invoices</th>
										        		<th></th>
										        		<th></th>
										        		<th></th>
										        		<th><?php echo number_format(totalInvoicesByLawfirmId($_SESSION['parent_id']), 2)?></th>
										        	</tr>
										        </tfoot>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- <script type="text/javascript" src="../assets/custom/invoice.js"></script> -->
</body>
</html>