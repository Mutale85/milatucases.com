<?php 
	include '../../includes/db.php';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$invoiceId = $_POST['invoiceId'];
		$lawFirmId = $_SESSION['parent_id'];
		displayCompanyData($lawFirmId);

		// Fetch invoice data
		$invoice_query = $connect->prepare("SELECT * FROM `invoices` WHERE `id` = ?");
		$invoice_query->execute([$invoiceId]);
		$invoice = $invoice_query->fetch(PDO::FETCH_ASSOC);

		if (!$invoice) {
		    die("Invoice not found");
		}

		// Fetch invoice items
		$items_query = $connect->prepare("
		    SELECT `id`, `invoice_id`, `description`, `quantity`, `price`, `total` 
		    FROM `invoice_items` 
		    WHERE `invoice_id` = ?
		");
		$items_query->execute([$invoiceId]);
		$invoice_items = $items_query->fetchAll(PDO::FETCH_ASSOC);
		$clientId = $invoice['clientId'];
		$clientInfo = fetchClientInfoById($clientId);
		$displayHtml = displayClientInfo($clientInfo);
		?>
		<div class="row  mt-4">
		    <div class="col-md-6">
		        <h4>Bill To:</h4>
		        <?php echo $displayHtml?>
		    </div>
		    <div class="col-md-6">
		        <div class="mb-3">
		            <strong>Invoice#:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?>
		        </div>
		        <div class="mb-3">
		            <strong>Invoice Date:</strong> <?php echo date('F j, Y', strtotime($invoice['date'])); ?>
		        </div>
		        <div class="mb-3">
		            <strong>Due Date:</strong> <?php echo date('F j, Y', strtotime($invoice['due_date'])); ?>
		        </div>
		    </div>
		    <div class="col-md-12">
		        <h4>Invoice Items:</h4>
		        <div class="table-responsive border border-bottom-0 border-top-0 rounded">
		            <table class="table m-0" id="invoiceItemsTable">
		                <thead>
		                    <tr>
		                        <th>Description</th>
		                        <th>Quantity</th>
		                        <th>Unit Price</th>
		                        <th>Total</th>
		                    </tr>
		                </thead>
		                <tbody>
		                    <?php foreach ($invoice_items as $item): ?>
		                    <tr>
		                        <td><?php echo html_entity_decode(decrypt($item['description'])); ?></td>
		                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
		                        <td><?php echo formatCurrency($item['price']); ?></td>
		                        <td><?php echo formatCurrency($item['total']); ?></td>
		                    </tr>
		                    <?php endforeach; ?>
		                </tbody>
		            </table>
	            </div>
	            <div class="table-responsive">
	                <table class="table m-0 table-borderless">
	                	<tbody>
	                		<tr>
		                		<td class="align-top pe-6 ps-0 py-6 text-body">
					                <p class="mb-1">
					                  	<span class="me-2 h6">Issued By:</span>
					                  	<span><?php echo fetchLawFirmMemberNames($invoice['createdBy']) ?></span>
					                </p>
					                <span><?php echo nl2br(html_entity_decode(decrypt($invoice['notes']))); ?></span>
					            </td>
					            <td class="px-0 py-6 w-px-100">
					                <p class="mb-2">Subtotal:</p>
					                <p class="mb-2">Tax:</p>
					                <p class="mb-2 border-bottom pb-2">Tax Rate:</p>
					                <p class="mb-2">Amount Paid:</p>
					                <p class="mb-0">Balance:</p>
					            </td>
					            <td class="text-end px-0 py-6 w-px-100 fw-medium text-heading">
					                <p class="fw-medium mb-2"><?php echo formatCurrency($invoice['subtotal']); ?></p>
					                <p class="fw-medium mb-2"><?php echo formatCurrency($invoice['tax']); ?></p>
					                <p class="fw-medium mb-2 border-bottom pb-2">(<?php echo htmlspecialchars($invoice['tax_rate']); ?>%)</p>
					                <p class="fw-medium mb-2"><?php echo formatCurrency($invoice['amountPaid']); ?></p>
					                <p class="fw-medium mb-0"><?php echo formatCurrency($invoice['remainingBalance']); ?></p>
					            </td>
		                    </tr>
		                </tbody>
		            </table>
		        </div>
		    </div>
		    <div class="col-md-12 mt-3 mb-3">
		        <h5>Terms and Conditions:</h5>
		        <p><?php echo nl2br(html_entity_decode(decrypt($invoice['terms']))); ?></p>
		    </div>
		    <div class="col-md-12 mb-3">
		    	<?php 
		    		if($invoice['pdfFilePath'] != ""){
	                    $filePath = '<a href="billings/'.$invoice['pdfFilePath'].'" target="_blank" class="dropdown-item"><i class="bi bi-file-pdf"></i> View PDF</a>'; 
	                }else{
	                    $filePath = "";
	                }
	                echo $filePath;
		    	?>
		    </div>
		</div>
		
		<?php
	}
?>