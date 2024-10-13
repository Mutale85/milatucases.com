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
          					<div class="col-md-12 mb-5">
          							<div class="card">
												  <div class="card-widget-separator-wrapper">
												    <div class="card-body card-widget-separator">
												      <div class="row gy-4 gy-sm-1">
												        <div class="col-sm-6 col-lg-3">
												          <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">
												            <div>
												              <h4 class="mb-0"><?php echo CountInvoicedClients($lawFirmId)?></h4>
												              <p class="mb-0">Invoiced Clients</p>
												            </div>
												            <div class="avatar me-sm-6">
												              <span class="avatar-initial rounded bg-label-secondary text-heading">
												                <i class="bx bx-user bx-26px"></i>
												              </span>
												            </div>
												          </div>
												          <hr class="d-none d-sm-block d-lg-none me-6">
												        </div>
												        <div class="col-sm-6 col-lg-3">
												          <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
												            <div>
												              <h4 class="mb-0"><?php echo CountTotalInvoices($lawFirmId)?></h4>
												              <p class="mb-0">Invoices</p>
												            </div>
												            <div class="avatar me-lg-6">
												              <span class="avatar-initial rounded bg-label-secondary text-heading">
												                <i class="bx bx-file bx-26px"></i>
												              </span>
												            </div>
												          </div>
												          <hr class="d-none d-sm-block d-lg-none">
												        </div>
												        <div class="col-sm-6 col-lg-3">
												          <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0 card-widget-3">
												            <div>
												              <h4 class="mb-0"><?php echo CalculateTotalPaidAmount($lawFirmId)?></h4>
												              <p class="mb-0">Paid</p>
												            </div>
												            <div class="avatar me-sm-6">
												              <span class="avatar-initial rounded bg-label-secondary text-heading">
												                <i class="bx bx-check-double bx-26px"></i>
												              </span>
												            </div>
												          </div>
												        </div>
												        <div class="col-sm-6 col-lg-3">
												          <div class="d-flex justify-content-between align-items-center">
												            <div>
												              <h4 class="mb-0"><?php echo CalculateTotalUnpaidAmount($lawFirmId)?></h4>
												              <p class="mb-0">Unpaid</p>
												            </div>
												            <div class="avatar">
												              <span class="avatar-initial rounded bg-label-secondary text-heading">
												                <i class="bx bx-error-circle bx-26px"></i>
												              </span>
												            </div>
												          </div>
												        </div>
												      </div>
												    </div>
												  </div>
												</div>
          					</div>
          					<div class="col-md-12">
          						<div class="card">
          							<div class="card-header d-flex justify-content-between align-items-center">
												    <h5 class="card-title text-primary">Invoices</h5>
												    <button class="btn btn-primary btn-sm" id="newBillBtn">Create New Invoice</button>
												</div>
          							<div class="card-body">
          								<div class="table table-responsive">
	          								<table class="table table-striped" id="allTables">
											        <thead>
											            <tr>
											                <th>Invoice No.</th>
											                <th>Client</th>
											                <th>Created</th>
											                <th>Due Date</th>
																			<th>Total</th>
																			<th>Paid</th>
																			<th>Balance</th>
																			<th>Status</th>
																			<th>View</th>
											            </tr>
											        </thead>
											        <tbody id="fetchCreatedBills">
											        	<?php echo fetchCreatedInvoice($lawFirmId);?>
										    			</tbody>
										    		</table>
										    	</div>
												</div>
												<!-- Invoice Modal -->
												<div class="modal fade" id="billModal" tabindex="-1" aria-labelledby="billModalLabel" aria-hidden="true">
												    <div class="modal-dialog modal-xl">
												        <div class="modal-content">
												            <div class="modal-header">
												                <h5 class="modal-title" id="billModalLabel">Create Invoice</h5>
												                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												            </div>
												            <div class="modal-body">
												            	<!-- Billing Modal -->
												            	<form id="invoiceForm" method="POST">
																	    	<div id="printableDiv">
																		        <div class="invoice-body">
																		            <div class="row">
																		                <div class="col-md-4">
																		                    <label class="form-label mb-1">Select Client</label>
																		                    <!-- <select class="form-select" id="client" name="clientId" required>
																												    <option value="">Select Client</option>
																											    	<?php foreach ($clientResult as $client) : ?>
																											        <option value="<?php echo htmlspecialchars($client['clientId']); ?>">
																											            <?php
																											            if ($client['client_type'] === 'Corporate') {
																											                echo html_entity_decode($client['clientName']);
																											            } else {
																											                echo html_entity_decode(decrypt($client['clientName']));
																											            }
																											            ?>
																											        </option>
																											    	<?php endforeach; ?>
																												</select> -->
																												<select class="form-select" id="client" name="clientId" required>
																											    <option value="">Select Client</option>
																											    <?php
																	                        $clients = fetchClientContacts($lawFirmId);
																	                        foreach ($clients as $client) {
																	                        	if($client['type'] == 'Individual'){
																	                        		$clientName = decrypt($client['name']);
																	                        	}else{
																	                        		$clientName = $client['name'];
																	                        	}
																	                        	
																	                        	$clientId = $client['id'];
																	                            echo "<option value='{$clientId}'>{$clientName} ({$client['type']})</option>";
																	                        }
																                        ?>
																											</select>
																		                </div>
																		                <div class="col-md-4">
																		                    <label class="form-label mb-1">Select Case</label>
																		                    <select class="form-select" id="case" name="caseId" required>
																		                        <option value="">Select Case</option>
																		                        <!-- Options will be populated via AJAX after client selection -->
																		                    </select>
																		                </div>
																		                <div class="col-md-4">
																		                    <label class="form-label mb-1">Date Range</label>
																		                    <div class="input-group">
																		                        <input type="date" class="form-control" id="dateFrom" name="dateFrom" required>
																		                        <span class="input-group-text">to</span>
																		                        <input type="date" class="form-control" id="dateTo" name="dateTo" required>
																		                    </div>
																		                </div>
																		            </div>
																		            <div class="row mt-4">
																                    <div class="col-md-4 mb-3">
																                        <label for="invoiceNumber" class="form-label"><strong>Invoice#:</strong></label>
																                        <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" required>
																                    </div>
																                    <div class="col-md-4 mb-3">
																                        <label for="invoiceDate" class="form-label"><strong>Invoice Date:</strong></label>
																                        <input type="date" class="form-control" id="invoiceDate" name="date" value="<?php echo date('Y-m-d'); ?>">
																                    </div>
																                    <div class="col-md-4 mb-3">
																                        <label for="dueDate" class="form-label"><strong>Due Date:</strong></label>
																                        <input type="date" class="form-control" id="dueDate" name="due_date" value="<?php echo date('Y-m-d'); ?>">
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
												<!-- End of invoice modal -->

												<!--  -->
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
											                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											            </div>
											            <div class="modal-body">
											                <form id="paymentForm">
											                    <div class="mb-3">
											                        <label for="totalAmount" class="form-label">Total Amount</label>
											                        <input type="text" class="form-control" id="totalAmount" readonly>
											                    </div>
											                    <div class="mb-3">
											                        <label for="amountPaid" class="form-label">Amount Paid</label>
											                        <input type="number" class="form-control" id="amountPaid" name="amountPaid" step="0.01" required>
											                    </div>
											                    <div class="mb-3">
											                        <label for="remainingBalance" class="form-label">Remaining Balance</label>
											                        <input type="text" class="form-control" id="remainingBalance" readonly>
											                    </div>
											                    <div class="form-check">
											                        <input type="checkbox" class="form-check-input" id="postPayment" name="postPayment" checked>
											                        <label class="form-check-label" for="postPayment">Post Payment to Incomes Table</label>
											                    </div>
											                    <input type="hidden" id="invoice_id">
											                    <input type="hidden" id="invoice_no">
											                </form>
											            </div>
											            <div class="modal-footer">
											                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
											                <button type="button" class="btn btn-primary" id="confirmBtn">Confirm Payment</button>
											            </div>
											        </div>
											    </div>
												</div>

												<!-- End of modals -->
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
    <script type="text/javascript" src="../assets/custom/bill.js"></script>
    <script>
			$(document).ready(function() {
				$("#newBillBtn").click(function(){
					$("#billModal").modal("show");
				});
				var itemIndex = 1;

				// Function to calculate totals
				function calculateTotals() {
					var subtotal = 0;
					$('#invoiceItemsTable tbody tr').each(function() {
						var quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
						var price = parseFloat($(this).find('.item-price').val()) || 0;
						var total = quantity * price;
						subtotal += total;
						$(this).find('.item-total').val(total.toFixed(2));
					});

					var taxRate = parseFloat($('#taxRate').val()) || 0;
					var tax = subtotal * (taxRate / 100);
					var total = subtotal + tax;

					$('#invoiceSubtotal').text(subtotal.toFixed(2));
					$('#invoiceTax').text(tax.toFixed(2));
					$('#invoiceTotal').text(total.toFixed(2));

					$('#invoiceSubtotalHidden').val(subtotal.toFixed(2));
					$('#invoiceTaxHidden').val(tax.toFixed(2));
					$('#invoiceTotalHidden').val(total.toFixed(2));
				}

				// Calculate totals when quantity or price changes
				$(document).on('input', '.item-quantity, .item-price, #taxRate', function() {
					calculateTotals();
				});

				// Initial calculation
				calculateTotals();

				// Form submission
				$("#invoiceForm").on("submit", function(e){
					e.preventDefault();
					var content = $(this).serialize();
					$.ajax({
						url:"billings/createBill",
						method:"POST",
						data:content,
						beforeSend:function(){
							$("#submitBtn").prop("disabled", true).html("Processing...");
						},
						success:function(response){
							$("#billModal").modal("hide");
							// if(response.includes("Invoice created")){
							// 	sweetSuccess(response);
							// }else{
							// 	sweetError(response);
							// }
							sweetSuccess(response);
							$("#submitBtn").prop("disabled", false).html('<i class="bi bi-receipt-cutoff"></i> Create Invoice');
							fetchCreatedBills()
						}
					});
				});

				// Tax type changes
				$('#taxType').change(function() {
					$('#taxTypeLabel').text($(this).val().toUpperCase());
					calculateTotals();
				});

				/*
				function populateInvoiceItems(timeEntries) {
			    var tbody = $('#invoiceItemsTable tbody');
			    tbody.empty();

			    timeEntries.forEach(function(entry, index) {
			        var hours = parseFloat(entry.hours) || 0;
			        var minutes = parseFloat(entry.minutes) || 0;
			        var totalHours = hours + (minutes / 60);
			        var hourlyRate = parseFloat(entry.hourlyRate) || 0;
			        var totalCost = totalHours * hourlyRate;

			        var row = `
			            <tr>
			                <td><input type="text" class="form-control item-description" name="items[${index}][description]" value="${entry.description || ''}" required></td>
			                <td><input type="number" class="form-control item-quantity" name="items[${index}][quantity]" value="${totalHours.toFixed(2)}" step="0.01" required></td>
			                <td><input type="number" class="form-control item-price" name="items[${index}][price]" value="${hourlyRate.toFixed(2)}" step="0.01" required></td>
			                <td><input type="text" class="form-control item-total" name="items[${index}][total]" value="${totalCost.toFixed(2)}" readonly></td>
			                <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="bi bi-x-circle"></i></button></td>
			                <input type="hidden" name="items[${index}][timeId]" value="${entry.id || ''}">
			            </tr>
			        `;
			        tbody.append(row);
			    });

			    calculateTotals();
				}
				*/
				function populateInvoiceItems(timeEntries) {
			    var tbody = $('#invoiceItemsTable tbody');
			    tbody.empty();
			    timeEntries.forEach(function(entry, index) {
			        // Parse hours and minutes as integers
			        var hours = parseInt(entry.hours) || 0;
			        var minutes = parseInt(entry.minutes) || 0;
			        
			        // Calculate total time in hours (to 2 decimal places)
			        var totalHours = hours + (minutes / 60);
			        totalHours = parseFloat(totalHours.toFixed(2));

			        // Parse hourlyRate and cost as floats
			        var hourlyRate = parseFloat(entry.hourlyRate) || 0;
			        var cost = parseFloat(entry.cost) || 0;

			        // Calculate total cost based on the provided cost
			        // If cost is not provided, calculate it based on totalHours and hourlyRate
			        var totalCost = cost > 0 ? cost : totalHours * hourlyRate;
			        totalCost = parseFloat(totalCost.toFixed(2));

			        console.log("Entry:", entry);
			        console.log("Calculated - Total Hours:", totalHours, "Hourly Rate:", hourlyRate, "Total Cost:", totalCost);

			        var row = `
			            <tr>
			                <td><input type="text" class="form-control item-description" name="items[${index}][description]" value="${entry.description || ''}" required></td>
			                <td><input type="number" class="form-control item-quantity" name="items[${index}][quantity]" value="${totalHours}" step="0.01" required></td>
			                <td><input type="number" class="form-control item-price" name="items[${index}][price]" value="${hourlyRate.toFixed(2)}" step="0.01" required></td>
			                <td><input type="text" class="form-control item-total" name="items[${index}][total]" value="${totalCost.toFixed(2)}" readonly></td>
			                <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="bi bi-x-circle"></i></button></td>
			                <input type="hidden" name="items[${index}][timeId]" value="${entry.id || ''}">
			            </tr>
			        `;
			        tbody.append(row);
			    });
			    calculateTotals();
			}

				function fetchTimeEntries() {
					var clientId = $('#client').val();
					var caseId = $('#case').val();
					var dateFrom = $('#dateFrom').val();
					var dateTo = $('#dateTo').val();

					if (clientId && caseId && dateFrom && dateTo) {
						$.ajax({
							url: 'billings/fetch_time_entries',
							type: 'POST',
							data: {
								clientId: clientId,
								caseId: caseId,
								dateFrom: dateFrom,
								dateTo: dateTo
							},
							dataType: 'json',
							success: function(response) {
								if (response.success) {
									populateInvoiceItems(response.timeEntries);
								} else {
									// alert('No time entries found for the selected criteria.');
									clearInvoiceItems();
								}
							},
							error: function() {
								alert('Error fetching time entries. Please try again.');
								clearInvoiceItems();
							}
						});
					}
				}

				// Clear invoice items
				function clearInvoiceItems() {
					$('#invoiceItemsTable tbody').empty();
					calculateTotals();
				}

				// Event listeners for date inputs
				$('#dateFrom').change(function() {
					var dateFrom = $(this).val();
					$('#dateTo').attr('min', dateFrom);
					if ($('#dateTo').val() < dateFrom) {
						$('#dateTo').val('');
					}
				});

				$('#dateTo').change(fetchTimeEntries);

				// Client change event
				$('#client').change(function() {
					var clientId = $(this).val();
					if(clientId) {
						$.ajax({
							url: 'billings/get_cases',
							type: 'POST',
							data: {clientId: clientId},
							success: function(data) {
								$('#case').html(data);
							}
						});
					} else {
						$('#case').html('<option value="">Select Case</option>');
					}
				});

				function fetchCreatedBills(){
					var getInvoices = "getInvoices";
					$.ajax({
						url: 'billings/fetchCreatedInvoices',
						type: 'POST',
						data: {getInvoices: getInvoices},
						success: function(data) {
							$('#fetchCreatedBills').html(data);
						}
					});
				}
				fetchCreatedBills()
			
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
		            url: 'billings/sendInvoice',
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

		    /*
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
		    */

		    $(document).ready(function() {
			    $(document).on("click", ".updatePayment", function(e){
			        e.preventDefault();
			        var invoiceId = $(this).data('invoice');
			        var invoiceNo = $(this).data('number');
			        var amount = $(this).attr("id");
			        $('#invoice_id').val(invoiceId);
			        $('#invoice_no').val(invoiceNo);
			        $('#totalAmount').val(amount);
			        $('#amountPaid').val('');
			        $('#remainingBalance').val('');
			        $("#paymentModal").modal("show");
			    });

			    $('#amountPaid').on('input', function() {
			        var totalAmount = parseFloat($('#totalAmount').val()) || 0;
			        var amountPaid = parseFloat($(this).val()) || 0;
			        var remainingBalance = totalAmount - amountPaid;
			        $('#remainingBalance').val(remainingBalance.toFixed(2));
			    });
			  })


		    $("#confirmBtn").click(function() {
			    var invoiceId = $("#invoice_id").val();
			    var invoiceNo = $("#invoice_no").val();
			    var postPaymentChecked = $("#postPayment").is(':checked');
			    var amount = $("#amountId").val();
			    var remainingBalance = $("#remainingBalance").val();
			    var amountPaid = $("#amountPaid").val();
			    
			    $.ajax({
			        url: 'billings/confirmPayment',
			        method: 'POST',
			        data: {
			            invoiceId: invoiceId,
			            invoiceNo: invoiceNo,
			            postPayment: postPaymentChecked,
			            amount:amount,
			            amountPaid:amountPaid,
			            remainingBalance:remainingBalance
			        },
			        beforeSend: function() {
			            $("#confirmBtn").prop("disabled", true).html("Processing...");
			        },
			        success: function(response) {
			            sweetSuccess(response);
			            $('#paymentModal').modal('hide');
			            $("#confirmBtn").prop("disabled", false).html('Confirm Payment');
			            fetchCreatedBills();
			        },
			        error: function() {
			            sweetError('An error occurred while processing the request.');
			            $("#confirmBtn").prop("disabled", false).html('Confirm Payment');
			        }
			    });
			});
		});
			
    	
    </script>
</body>
</html>