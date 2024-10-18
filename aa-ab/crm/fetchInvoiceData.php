<?php 
	include "../../includes/db.php";
	if(isset($_POST['clientId'])){
		$clientId = base64_decode($_POST['clientId']);
		$lawFirmId = $_SESSION['parent_id'];
        $query = $connect->prepare("SELECT * FROM invoices WHERE lawFirmId = ? AND clientId = ? ");
        $query->execute([$lawFirmId, $clientId]);
        $invoices = $query->fetchAll(PDO::FETCH_ASSOC);
	    ?>
	    <div class="table table-responsive">
		    <table class="table table-striped" id="allTables">
		        <thead>
		            <tr>
		                <th>Invoice No.</th>
		                <th>Client</th>
		                <th>Created</th>
		                <th>Due Date</th>
						<th>Amount (ZMW)</th>
						<th>Status</th>
		            </tr>
		        </thead>
		        <tbody>
		            <?php if ($invoices): ?>
		                <?php foreach ($invoices as $invoice):
		                	$clientId = $invoice['clientId'];
		                	$lawFirmId = $invoice['lawFirmId']; 
		                	$filePath = fetchInvoicePDF($invoice['id'], $lawFirmId, $clientId);
		                	
		                	if($invoice['status'] == '0'){
		                		$status = '<span class="text-danger">Unpaid</span>';
		                	}else{
		                		$status = '<span class="text-success">Paid</span>';
		                	}
		                ?>
		                    <tr>
								<td><a href="billings/invoice-preview?invoiceId=<?php echo base64_encode($invoice['id'])?>"><?php echo $invoice['invoice_number']; ?></td>
		                        <td><a href="crm/?clientId=<?php echo base64_encode($clientId) ?>"> <?php echo getClientNameById($clientId, $lawFirmId); ?></a></td>
								<td><?php echo date("D d M, Y", strtotime($invoice['created_at'])); ?></td>
								<td><?php echo date("D d M, Y", strtotime($invoice['due_date'])); ?></td>
		                        
		                        <td><?php echo number_format($invoice['total'], 2); ?></td>
		                        <td><?php echo $status?></td>
		                    </tr>
		                <?php endforeach; ?>
		            <?php else: ?>
		                <tr>
		                    <td colspan="6">No invoices found.</td>
		                </tr>
		            <?php endif; ?>
		        </tbody>
		        
		    </table>
		</div>
	</div>
	<?php
	}
?>