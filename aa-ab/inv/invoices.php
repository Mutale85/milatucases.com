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
	<title><?php echo $_SESSION['lawFirmName']?> Invoices</title>
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
          								<h5 class="card-title">Create Invoice</h5>
          							</div>
          							<div class="card-body">
									    <form id="invoiceForm" method="POST">
									    	<div id="printableDiv">
										        <div class="invoice-header">
										            <div class="row align-items-center">
										                <div class="col-md-12 mb-3">
                                        <?php echo displayCompanyData($lawFirmId)?>
                                    </div>
										            </div>
										        </div>
										        <div class="invoice-body">
										            <div class="row">
										                <div class="col-md-6">
										                    <h4>Bill To:</h4>
										                    <select class="form-select" id="client" name="clientId" required>
										                    	<option value="">Select Client</option>
										                        <?php
											                        $clients = fetchClientContacts($_SESSION['parent_id']);
											                        foreach ($clients as $client) {
											                        	if($client['type'] == 'Individual'){
											                        		$clientName = decrypt($client['name']);
											                        	}else{
											                        		$clientName = $client['name'];
											                        	}
											                        	$clientTpin = decrypt($client['tpin']);
											                        	$clientEmail = decrypt($client['email']);
											                        	$id = $client['id'];
											                            echo "<option value='{$id}' data-email='{$clientEmail}' data-tpin='{$clientTpin}'>{$clientName} ({$client['type']})</option>";
											                        }
										                        ?>
										                    </select>
										                    <div class="mt-3">
										                    	<p id="client-email"></p>
										                    	<p id="client-tpin"></p>
										                    	<input type="hidden" name="clientEmail" id="clientEmail">
										                    	<input type="hidden" name="client_tpin" id="client_tpin">
										                    </div>
										                </div>
										            </div>
										            <div class="row mt-4">
										                <div class="col-md-6">
										                    <div class="mb-3">
										                        <label for="invoiceNumber" class="form-label"><strong>Invoice#:</strong></label>
										                        <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" required>
										                    </div>
										                    <div class="mb-3">
										                        <label for="invoiceDate" class="form-label"><strong>Invoice Date:</strong></label>
										                        <input type="date" class="form-control" id="invoiceDate" name="date" value="<?php echo date('Y-m-d'); ?>">
										                    </div>
										                    <div class="mb-3">
										                        <label for="dueDate" class="form-label"><strong>Due Date:</strong></label>
										                        <input type="date" class="form-control" id="dueDate" name="due_date" value="<?php echo date('Y-m-d'); ?>">
										                    </div>
										                </div>
										            </div>
										            <div class="row mt-4">
												        <div class="col-12">
												            <h4>Invoice Items:</h4>
												            <div class="tabl table-responsive">
													            <table class="table table-bordered" id="invoiceItemsTable">
													                <thead>
													                    <tr>
													                        <th>Description</th>
													                        <th>Quantity</th>
													                        <th>Unit Price</th>
													                        <th>Total</th>
													                        <th id="action">Action</th>
													                    </tr>
													                </thead>
													                <tbody>
													                    <tr>
													                        <td><input type="text" class="form-control item-description" name="items[0][description]" required></td>
													                        <td><input type="number" class="form-control item-quantity" name="items[0][quantity]" required></td>
													                        <td><input type="number" class="form-control item-price" name="items[0][price]" step="0.01" required></td>
													                        <td><input type="text" class="form-control item-total" name="items[0][total]" readonly></td>
													                        <td id="removeBtn"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="bi bi-x-circle"></i></button></td>
													                    </tr>
													                </tbody>
													            </table>
													        </div>
												            <button type="button" class="btn btn-dark btn-sm" id="addItem"><i class="bi bi-node-plus"></i> Add Item</button>
												        </div>
												    </div>
												    
													<div class="row mt-4">
													    <div class="col-md-6 offset-md-6">
													        <div class="row mb-3">
													        	<div class="col-md-6">
														            <label for="taxType" class="form-label"><strong>Tax Type:</strong></label>
														            <select class="form-control" id="taxType" name="tax_type" required>
														            	<option value="">Select</option>
														                <option value="vat">VAT</option>
														                <option value="withholding">Withholding</option>
														            </select>
														        </div>
														        <div class="col-md-6">
														            <label for="taxRate" class="form-label"><strong>Tax Rate (%):</strong></label>
														            <input type="number" class="form-control" id="taxRate" name="tax_rate" step="any" min="0" value="0">
														            <input type="hidden" name="invoiceSubtotal" id="invoiceSubtotalHidden">
														            <input type="hidden" name="invoiceTax" id="invoiceTaxHidden">
														            <input type="hidden" name="invoiceTotal" id="invoiceTotalHidden">
														            <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $_SESSION['parent_id']?>">
														        </div>
													        </div>
													        <p>Subtotal: <span id="invoiceSubtotal">0.00</span></p>
													        <p>Tax (<span id="taxTypeLabel">VAT</span>): <span id="invoiceTax">0.00</span></p>
													        <p>Total: <span id="invoiceTotal">0.00</span></p>
													    </div>
													</div>
												    <div class="row mt-3 mb-4">
												    	<div class="col-6">
												    		<label>Invoice Terms</label>
												    		<textarea class="form-control" id="terms" name="terms" placeholder="Add terms of the invoice" rows="2"></textarea>
												    	</div>

												    	<div class="col-6">
												    		<label>Notes</label>
												    		<textarea class="form-control" id="notes" name="notes" placeholder="It was great doing business with you." rows="2">It was great doing business with you.</textarea>
												    	</div>
												    </div>
										        </div>
										    </div>
									        <div class="row mt-3 mb-4">
											    <div class="col-12">
											        <div class="form-check">
											            <input class="form-check-input" type="checkbox" id="emailInvoice" name="email_invoice">
											            <label class="form-check-label" for="emailInvoice">
											                Email Invoice to Client
											            </label>
											        </div>
											    </div>
											</div>

									        <button type="submit" class="btn btn-primary" id="submitBtn"> <i class="bi bi-receipt-cutoff"></i> Create Invoice</button>
									    </form>
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
    <script type="text/javascript" src="../assets/custom/invoice.js"></script>
</body>
</html>