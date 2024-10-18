<?php 
	include "../../includes/db.php";
	include '../base/base.php';
	if($_GET['invoiceId']){
		$invoiceId = base64_decode($_GET['invoiceId']);
		
	}
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Template</title>
	<?php include '../addon_header.php'; ?>
	<!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"> -->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
        					<div class="card invoice-preview-card p-sm-12 p-4 col-md-9 mb-3">
                      <div id="invoiceDataDisplay"></div>
									</div>
        					<div class="col-md-3 col-12 invoice-actions">
								    <div class="card">
								      <div class="card-body">
								        <button class="btn btn-primary d-grid w-100 mb-4" data-bs-toggle="offcanvas" data-bs-target="#sendInvoiceOffcanvas">
								          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="bx bx-paper-plane bx-sm me-2"></i>Send Invoice</span>
								        </button>
								        
								        <button class="btn btn-dark d-grid w-100 mb-4" data-bs-toggle="offcanvas" data-bs-target="#addPaymentOffcanvas">
								          <span class="d-flex align-items-center justify-content-center text-nowrap">Add Payment</span>
								        </button>

							        	<button class="btn btn-secondary d-grid w-100" data-invoice-id="<?php echo $invoiceId?>" id="generatePdf">
								          <span class="d-flex align-items-center justify-content-center text-nowrap"><i class="bi bi-file-pdf"></i> Generate PDF</span>
								        </button>
								      </div>
								    </div>
								  </div>
        				</div>
        										
        			</div>
        			<?php include '../addon_footer.php';?>

        			<div class="content-backdrop fade"></div>
        			<!-- Send Invoice -->
								<div class="offcanvas offcanvas-end" id="sendInvoiceOffcanvas" aria-hidden="true" aria-modal="true" role="dialog">
							        <div class="offcanvas-header mb-6 border-bottom">
							            <h5 class="offcanvas-title">Send Invoice</h5>
							            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
							        </div>
							        <div class="offcanvas-body pt-0 flex-grow-1">
							            <form id="sendInvoiceForm" method="POST" enctype="multipart/form-data">
							                <div class="mb-6" style="display: none;">
							                    <label for="invoice-from" class="form-label">From</label>
							                    <input type="hidden" class="form-control" id="invoice-from" value="" placeholder="">
							                </div>
							                <div class="mb-6 mb-3 mt-3">
							                    <label for="invoice-to" class="form-label mb-2">To</label>
							                    <input type="text" class="form-control" id="invoice-to" name="invoice-to" value="" placeholder="company@email.com" required>
							                    <input type="hidden" class="form-control" id="invoiceId" name="invoiceId" value="<?php echo $invoiceId ?>">
							                    <input type="hidden" class="form-control" id="lawFirmId" name="lawFirmId" value="<?php echo $lawFirmId ?>">
							                </div>
							                <div class="mb-6 mb-3">
							                    <label for="invoice-subject" class="form-label mb-2">Subject</label>
							                    <input type="text" class="form-control" id="invoice-subject" name="invoice-subject" value="Invoice For Legal Services" placeholder="" required>
							                </div>
							                <div class="mb-6 mb-3">
							                    <label for="invoice-message" class="form-label mb-2">Message</label>
							                    <textarea class="form-control" name="invoice-message" id="invoice-message" cols="3" rows="8"></textarea>
							                </div>
							                <p class="mb-3">Note: A PDF document will be created and sent to the email</p>
							                <!-- <div class="mb-6 mb-3">
							                    <label for="invoice-attachment" class="form-label mb-2">Attachment</label>
							                    <input type="file" class="form-control" id="invoice-attachment" name="invoice-attachment">
							                </div>
							                <div class="mb-6 mb-3">
							                    <span class="badge bg-label-primary">
							                        <i class="bx bx-link bx-xs"></i>
							                        <span class="align-middle">Invoice Attached</span>
							                    </span>
							                </div> -->
							                <div class="mb-6 d-flex flex-wrap">
							                    <button type="submit" class="btn btn-primary me-4" id="sendInvoiceBtn">Send</button>
							                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
							                </div>
							            </form>
							        </div>
							    </div>
								<!-- End of Invoice Sending -->
								<!-- Add Payment -->
								<div class="offcanvas offcanvas-end " id="addPaymentOffcanvas" aria-hidden="true" aria-modal="true" role="dialog">
								  <div class="offcanvas-header border-bottom">
								    <h5 class="offcanvas-title">Add Payment</h5>
								    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
								  </div>
								  <div class="offcanvas-body flex-grow-1">
								    <div class="d-flex justify-content-between bg-lighter p-2 mb-4">
								      <p class="mb-0">Invoice Balance:</p>
								      <p class="fw-medium mb-0"><?php echo fetchInvoiceBalance($invoiceId)?></p>
								    </div>
								    <form method="POST" id="addPaymentForm">
								      <div class="mb-6 mb-3">
								        <label class="form-label" for="invoiceAmount">Payment Amount</label>
								        <div class="input-group">
								          <span class="input-group-text"></span>
								          <input type="text" id="invoiceAmount" name="invoiceAmount" class="form-control invoice-amount" required placeholder="100">
								          <input type="hidden" name="invoiceId" id="invoiceId" value="<?php echo $invoiceId?>">
								          <input type="hidden" name="lawFirmId" id="lawFirmId" value="<?php echo $lawFirmId?>">
								          <input type="text" name="clientId" id="clientId" value="<?php echo fetchInvoiceClientI($invoiceId)?>">
								        </div>
								      </div>
								      <div class="mb-6 mb-3">
								        <label class="form-label" for="payment-date">Payment Date</label>
								        <input id="payment-date" class="form-control invoice-date flatpickr-input" name="payment-date" type="date" required>
								      </div>
								      <div class="mb-6 mb-3">
								        <label class="form-label" for="payment-method">Payment Method</label>
								        <select class="form-select" id="payment-method" name="payment-method">
								          <option value="" selected="" disabled="">Select payment method</option>
								          <option value="Cash">Cash</option>
								          <option value="Bank Transfer">Bank Transfer</option>
								          <option value="Mobile Money">Mobile Money</option>
								          <!-- <option value="Debit Card">Debit Card</option>
								          <option value="Credit Card">Credit Card</option>
								          <option value="Paypal">Paypal</option> -->
								        </select>
								      </div>
								      <div class="mb-6 mb-3">
								        <label class="form-label" for="payment-note">Internal Payment Note</label>
								        <textarea class="form-control" id="payment-note" name="payment-note" rows="2"></textarea>
								      </div>
								      <div class="mb-6 mb-3 d-flex flex-wrap">
								        <button type="submit" class="btn btn-primary me-4" id="sendPaymentBtn">Send</button>
								        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
								      </div>
								    </form>
								  </div>
								</div>
								<!-- End of Add Payment -->
        		</div>
        	</div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <?php include '../addon_footer_links.php';?>
  <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script> -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	  <script>
			function toastSuccess(message){
				toastr["success"](message)

					toastr.options = {
					  "closeButton": true,
					  "debug": false,
					  "newestOnTop": false,
					  "progressBar": false,
					  "positionClass": "toast-top-right",
					  "preventDuplicates": false,
					  "onclick": null,
					  "showDuration": "300",
					  "hideDuration": "1000",
					  "timeOut": "2500",
					  "extendedTimeOut": "1000",
					  "showEasing": "swing",
					  "hideEasing": "linear",
					  "showMethod": "fadeIn",
					  "hideMethod": "fadeOut"
					}
			}
			// toastSuccess("Testing");

			function toastError(msg){
				toastr["error"](msg)

					toastr.options = {
					  "closeButton": true,
					  "debug": false,
					  "newestOnTop": false,
					  "progressBar": false,
					  "positionClass": "toast-top-right",
					  "preventDuplicates": false,
					  "onclick": null,
					  "showDuration": "300",
					  "hideDuration": "1000",
					  "timeOut": "2500",
					  "extendedTimeOut": "1000",
					  "showEasing": "swing",
					  "hideEasing": "linear",
					  "showMethod": "fadeIn",
					  "hideMethod": "fadeOut"
					}
			}

	    $(document).ready(function() {
	        $('#sendInvoiceForm').on('submit', function(e) {
	            e.preventDefault();

	            var form = $(this);
	            var formData = new FormData(this);

	            $.ajax({
	                url: 'billings/CreateAndSendInvoice',  // Replace with your server-side processing script
	                type: 'POST',
	                data: formData,
	                contentType: false,
	                processData: false,
	                beforeSend: function() {
	                  $("#sendInvoiceBtn").prop('disabled', true).html('<i class="bx bx-loader bx-spin me-2"></i>Processing...');
	                },
	                success: function(response) {
	                    // Handle success
	                    sweetSuccess('Invoice sent successfully!');	                    
	                    form[0].reset();
	                    
	                    $('#sendInvoiceOffcanvas').offcanvas('hide');
	                    $("#sendInvoiceBtn").prop('disabled', false).html('Send Invoice');
	                },
	                error: function() {
	                    // Handle error
	                    sweetError('An error occurred. Please try again.');
	                },
	                complete: function() {
	                    $("#sendInvoiceBtn").prop('disabled', false).html('Send Invoice');
	                }
	            });
	        });

	        // generate pdf 

	        $('#generatePdf').on('click', function(e) {
				    var invoiceId = $(this).data('invoice-id');
				    $.ajax({
				        url: 'billings/CreatePdfInvoice',
				        type: 'POST',
				        data: {invoiceId: invoiceId},
				        dataType: 'json',
				        beforeSend: function() {
				            $("#generatePdf").prop('disabled', true).html('<i class="bx bx-loader bx-spin me-2"></i>Processing...');
				        },
				        success: function(response) {
				            if (response.success) {
				                toastSuccess('Invoice PDF created successfully');
				                // Construct the full URL to the PDF
				                
				                var pdfUrl = 'billings/' + response.path;
				                
				                // Open the PDF in a new window
				                window.open(pdfUrl, '_blank');
								fetchCreatedInvoice("<?php echo $invoiceId?>");
				            } else {
				                toastError('Error creating PDF: ' + response.message);
				            }
				        },
				        error: function(xhr, status, error) {
				            toastError('An error occurred. Please try again.');
				            console.error('Error:', error);
				        },
				        complete: function() {
				            $("#generatePdf").prop('disabled', false).html('<i class="bi bi-file-pdf"></i> Generate PDF');
				        }
				    });
					});
	    });

	    $(document).ready(function() {
			  $('#addPaymentForm').on('submit', function(e) {
			    e.preventDefault();
			    
			    var formData = $(this).serialize();
			    $.ajax({
			      url: 'billings/createPayment',
			      type: 'POST',
			      data: formData,
			      dataType: 'json',
			      beforeSend: function() {
              $("#sendPaymentBtn").prop('disabled', true).html('<i class="bx bx-loader bx-spin me-2"></i>Processing...');
            },
			      success: function(response) {
			        if (response.status === 'success') {
								toastSuccess('Payment processed successfully!');
			          $('#addPaymentOffcanvas').offcanvas('hide');
			          fetchCreatedInvoice("<?php echo $invoiceId?>");
			        } else {
			          toastError('Error: ' + response.message);
			        }
			        $("#sendPaymentBtn").prop('disabled', false).html('Send');
			      },
			      error: function() {
			        toastError('An error occurred while processing the payment.');
			        $("#sendPaymentBtn").prop('disabled', false).html('Send');
			      }
			    });
			  });
			});

			function fetchCreatedInvoice(invoiceId) {
		    $.ajax({
		      url: 'billings/getInvoiceDetails',
		      type: 'POST',
		      data: { invoiceId: invoiceId },
		      
		      success: function(response) {
		        $("#invoiceDataDisplay").html(response);
		      },
		      error: function() {
		        console.error('An error occurred while fetching updated invoice details.');
		      }
		    });
		  }
		  fetchCreatedInvoice("<?php echo $invoiceId?>");

    </script>
</body>
</html>