$(document).ready(function() {
    $('#client').change(function() {
        var selectedClient = $(this).find('option:selected');
        var email = selectedClient.data('email');
        var tpin = selectedClient.data('tpin');
        var clientId = $(this).val();

        if (clientId !== "") {
            $('#client-email').text('Email: ' + email);
            $('#client-tpin').text('TPIN: ' + tpin);
            $('#client_tpin').val(tpin);
            $('#clientEmail').val(email);

            // AJAX request to check for unpaid disbursement records
            $.ajax({
                url: 'inv/fetchDisbursement', // PHP script to fetch disbursement data
                type: 'POST',
                data: {clientId: clientId},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the form fields with the disbursement data
                        $('#invoiceSubtotal').text(response.total);
                        $('#invoiceTotal').text(response.total);
                        $('#invoiceSubtotalHidden').val(response.total);
                        $('#invoiceTotalHidden').val(response.total);

                        // Update the invoice items table with a single disbursement row
                        var tbody = $('#invoiceItemsTable tbody');
                        tbody.empty(); // Clear the existing rows
                        var row = `
                            <tr>
                                <td><input type="text" class="form-control item-description" name="items[0][description]" value="Disbursement" required></td>
                                <td><input type="number" class="form-control item-quantity" name="items[0][quantity]" value="1" required></td>
                                <td><input type="number" class="form-control item-price" name="items[0][price]" step="0.01" value="${response.total}" required></td>
                                <td><input type="text" class="form-control item-total" name="items[0][total]" value="${response.total}" readonly></td>
                                <td id="removeBtn"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="bi bi-x-circle"></i></button></td>
                            </tr>
                        `;
                        tbody.append(row);

                        // Attach the remove item event handler again
                        $('.remove-item').click(function() {
                            $(this).closest('tr').remove();
                        });
                    } else {
                        // Clear the disbursement data if no unpaid disbursement found
                        $('#invoiceSubtotal').text('0.00');
                        $('#invoiceTotal').text('0.00');
                        $('#invoiceSubtotalHidden').val('0.00');
                        $('#invoiceTotalHidden').val('0.00');
                        $('#invoiceItemsTable tbody').empty(); // Clear the invoice items table
                    }
                }
            });
        } else {
            $('#client-email').text('');
            $('#client-tpin').text('');
            $('#client_tpin').val('');
            $('#clientEmail').val('');

            // Clear the disbursement data if no client is selected
            $('#invoiceSubtotal').text('0.00');
            $('#invoiceTotal').text('0.00');
            $('#invoiceSubtotalHidden').val('0.00');
            $('#invoiceTotalHidden').val('0.00');
            $('#invoiceItemsTable tbody').empty(); // Clear the invoice items table
        }
    });

    // Trigger change to load the details of the initially selected client
    $('#client').trigger('change');
});

$(document).ready(function() {
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

    // Add new item row
    $('#addItem').click(function() {
        var newRow = `
            <tr>
                <td><input type="text" class="form-control item-description" name="items[${itemIndex}][description]" required></td>
                <td><input type="number" class="form-control item-quantity" name="items[${itemIndex}][quantity]" required></td>
                <td><input type="number" class="form-control item-price" name="items[${itemIndex}][price]" step="0.01" required></td>
                <td><input type="text" class="form-control item-total" name="items[${itemIndex}][total]" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="bi bi-x-circle"></i></button></td>
            </tr>`;
        $('#invoiceItemsTable tbody').append(newRow);
        itemIndex++;
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Calculate totals when quantity or price changes
    $(document).on('input', '.item-quantity, .item-price, #taxRate', function() {
        calculateTotals();
    });

    // Initial calculation
    calculateTotals();


    $("#invoiceForm").on("submit", function(e){
    	e.preventDefault();
    	var content = $(this).serialize();
    	$.ajax({
    		url:"inv/createInvoice",
    		method:"POST",
    		data:content,
    		beforeSend:function(){
    			$("#submitBtn").prop("disabled", true).html("Processing...");
    		},
    		success:function(response){
    			if(response.includes("Invoice created")){
                    sweetSuccess(response);
                }else{
                    sweetError(response);
                }
    			// location.reload();
    			$("#submitBtn").prop("disabled", false).html('<i class="bi bi-receipt-cutoff"></i> Create Invoice');
    			
    		}
    	})
    })
});

const taxTypeSelect = document.getElementById('taxType');
taxTypeSelect.addEventListener('change', updateTaxType);

// Add event listener to the tax rate input field
const taxRateInput = document.getElementById('taxRate');
taxRateInput.addEventListener('input', calculateInvoiceTotal);

// Function to update the tax type label
function updateTaxType() {
    const taxTypeLabel = document.getElementById('taxTypeLabel');
    const selectedTaxType = taxTypeSelect.value;
    taxTypeLabel.textContent = selectedTaxType.toUpperCase();
    calculateInvoiceTotal();
}

// Function to calculate the invoice total
function calculateInvoiceTotal() {
    const invoiceSubtotalElement = document.getElementById('invoiceSubtotal');
    const invoiceTaxElement = document.getElementById('invoiceTax');
    const invoiceTotalElement = document.getElementById('invoiceTotal');
    const taxRateInput = document.getElementById('taxRate');
    const invoiceSubtotal = parseFloat(invoiceSubtotalElement.textContent);
    const taxRate = parseFloat(taxRateInput.value) / 100;
    let invoiceTax = 0;
    let invoiceTotal = 0;

    if (taxRateInput.value !== '') {
        invoiceTax = invoiceSubtotal * taxRate;
        invoiceTotal = invoiceSubtotal + invoiceTax;
    }

    invoiceTaxElement.textContent = invoiceTax.toFixed(2);
    invoiceTotalElement.textContent = invoiceTotal.toFixed(2);

    // Update the hidden input fields
    document.getElementById('invoiceTaxHidden').value = invoiceTax.toFixed(2);
    document.getElementById('invoiceTotalHidden').value = invoiceTotal.toFixed(2);
}



/*========= Buttons for printing and downloading PDF ==========*/


