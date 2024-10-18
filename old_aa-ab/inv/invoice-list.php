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
									        $query = $connect->prepare("SELECT * FROM invoices WHERE lawFirmId = ? ");
									        $query->execute([$lawFirmId]);
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
																		<th>View PDF</th>
																		<th>Amount (ZMW)</th>
																		<th>Status</th>
																		<th>Action</th>
										            </tr>
										        </thead>
										        <tbody>
										            <?php if ($invoices): ?>
										                <?php foreach ($invoices as $invoice):
										                	$clientId = $invoice['clientId'];
										                	$lawFirmId = $invoice['lawFirmId']; 
										                	$invoiceId = $invoice['id']; 
										                	$filePath = fetchInvoicePDF($lawFirmId, $clientId);
										                	$link = preg_replace("#[^0-9.A-Za-z-]#", " ", $filePath);
										                	$link = removeFirstWord($link);
										                	if($invoice['status'] == '0'){
										                		$status = '<button data-invoice="'.$invoiceId.'" data-number="'.$invoice['invoice_number'].'" id="'.$invoice['total'].'" class="btn btn-danger btn-sm updatePayment">Unpaid</button>';
										                	}else{
										                		$status = '<button href="'.$invoiceId.'" class="btn btn-success btn-sm">Paid</button>';
										                	}
										                ?>
										                    <tr>
																<td><?php echo $invoice['invoice_number']; ?></td>
										                        <td><a href="crm/?clientId=<?php echo encrypt($clientId) ?>"> <?php echo getClientNameById($clientId, $lawFirmId); ?></a></td>
																<td><?php echo date("D d M, Y", strtotime($invoice['created_at'])); ?></td>
																<td><?php echo date("D d M, Y", strtotime($invoice['due_date'])); ?></td>
										                        <td>
										                        	<a href="inv/<?php echo $filePath; ?>" target="_blank">
										                        		<i class="bi bi-file-pdf"></i> View
										                        	</a>
										                        </td>
										                        <td><?php echo number_format($invoice['total'], 2); ?></td>
										                        <td><?php echo $status?></td>
										                        <td><button data-link="<?php echo $filePath?>" class="btn btn-primary btn-sm sendInvoice" data-id="<?php echo $clientId?>" data-invoice="<?php echo $invoiceId ?>" data-email="<?php echo fetchClientEmailByTPIN($clientId, $lawFirmId)?>"> Send Invoice </button></td>
										                    </tr>
										                <?php endforeach; ?>
										            <?php else: ?>
										                
										            <?php endif; ?>
										        </tbody>
										        <tfoot>
										        	<tr>
										        		<th>Total Invoices</th>
										        		<th></th>
										        		<th></th>
										        		<th></th>
										        		<th></th>
										        		<th><?php echo number_format(totalInvoicesByLawfirmId($lawFirmId), 2)?></th>
										        		<th></th>
										        		<th></th>
										        	</tr>
										        </tfoot>
										    </table>
										</div>
									</div>
									<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document">
										    <div class="modal-content">
										      	<div class="modal-header">
										        	<h5 class="modal-title" id="emailModalLabel">Send Invoice</h5>
										        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
										        	</button>
										      	</div>
										      	<div class="modal-body">
										        	<form id="sendEmailForm">
										          		<div class="form-group">
										            		<label for="emailInput">Email address</label>
										            		<input type="email" class="form-control" id="emailInput" required>
										          		</div>
										          		<input type="hidden" id="clientId">
										          		<input type="hidden" id="invoiceId">
										          		<input type="hidden" id="filepath">
										        	</form>
										      	</div>
										      	<div class="modal-footer">
										        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										        	<button type="button" class="btn btn-primary" id="sendEmailBtn">Send</button>
										      	</div>
										    </div>
										</div>
									</div>

									<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document">
										    <div class="modal-content">
										      	<div class="modal-header">
										        	<h5 class="modal-title" id="paymentModalLabel">Invoice Payment Update </h5>
										        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
										        	</button>
										      	</div>
										      	<div class="modal-body">
										        	<form id="paymentForm">
										          		<div class="form-check">
													        <input type="checkbox" class="form-check-input" id="postPayment" name="postPayment">
													        <label class="form-check-label" for="postPayment">Post Payment to Incomes Table</label>
													    </div>
										          		<input type="hidden" id="invoice_id">
										          		<input type="hidden" id="invoice_no">
										          		<input type="hidden" id="amountId">
										        	</form>
										      	</div>
										      	<div class="modal-footer">
										        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										        	<button type="button" class="btn btn-primary" id="confirmBtn">Confirm Payment</button>
										      	</div>
										    </div>
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
    	$(document).on('click', '.sendInvoice', function() {
	        var clientId = $(this).data('id');
	        var invoiceId = $(this).data('invoice');
	        var clientEmail = $(this).data('email');
	        var filepath = $(this).data("link");
	        $('#emailInput').val(clientEmail);
	        $('#clientId').val(clientId);
	        $('#invoiceId').val(invoiceId);
	        $("#filepath").val(filepath);
	        $('#emailModal').modal('show');
	    });

	    $('#sendEmailBtn').on('click', function() {
	        var email = $('#emailInput').val();
	        var clientId = $('#clientId').val();
	        var invoiceId = $('#invoiceId').val();
	        var filepath = $("#filepath").val();

	        if (!email) {
	            sweetError('Please enter an email address.');
	            return;
	        }

	        $.ajax({
	            url: 'inv/sendInvoice',
	            method: 'POST',
	            data: {
	                clientId: clientId,
	                invoiceId: invoiceId,
	                clientEmail: email,
	                filepath:filepath
	            },
	            beforeSend:function(){
	                $("#sendEmailBtn").prop("disabled", true).html("Processing...");
	            },
	            success: function(response) {
	               
	                sweetSuccess(response);
	                $('#emailModal').modal('hide');
	                
	                $("#sendEmailBtn").prop("disabled", false).html('Send as Email');

	            },
	            error: function() {
	                sweetError('An error occurred while sending the email.');
	                $("#sendEmailBtn").prop("disabled", false).html('Send as Email');

	            }
	        });
	    });

	    $(document).on("click", ".updatePayment", function(e){
	    	e.preventDefault();
	    	var invoiceId = $(this).data('invoice');
	    	var invoiceNo = $(this).data('number');
	    	var amount = $(this).attr("id");

			$('#invoice_id').val(invoiceId);
			$('#invoice_no').val(invoiceNo);
			$("#amountId").val(amount);
			$("#paymentModal").modal("show");
	    })

	    $("#confirmBtn").click(function() {
		    var invoiceId = $("#invoice_id").val();
		    var invoiceNo = $("#invoice_no").val();
		    var postPaymentChecked = $("#postPayment").is(':checked');
		    var amount = $("#amountId").val();
		    
		    $.ajax({
		        url: 'inv/confirmPayment',
		        method: 'POST',
		        data: {
		            invoiceId: invoiceId,
		            invoiceNo: invoiceNo,
		            postPayment: postPaymentChecked,
		            amount:amount
		        },
		        beforeSend: function() {
		            $("#confirmBtn").prop("disabled", true).html("Processing...");
		        },
		        success: function(response) {
		            sweetSuccess(response);
		            $('#paymentModal').modal('hide');
		            $("#confirmBtn").prop("disabled", false).html('Confirm Payment');
		            setTimeout(function(){
		            	location.reload();
		            }, 2000)
		        },
		        error: function() {
		            sweetError('An error occurred while processing the request.');
		            $("#confirmBtn").prop("disabled", false).html('Confirm Payment');
		        }
		    });
		});

    </script>
</body>
</html>