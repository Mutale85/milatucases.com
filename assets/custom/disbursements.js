$(document).ready(function() {
    $('#client').change(function() {
        var selectedClient = $(this).find('option:selected');
        var email = selectedClient.data('email');
        var tpin = selectedClient.data('tpin');

        if ($(this).val() !== "") {
            $('#client_tpin').val(tpin);
            $('#clientEmail').val(email);
        } else {
            $('#client_tpin').val('');
            $('#clientEmail').val('');
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
        $('#disbursementItemsTable tbody tr').each(function() {
            var quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            var price = parseFloat($(this).find('.item-price').val()) || 0;
            var total = quantity * price;
            subtotal += total;
            $(this).find('.item-total').val(total.toFixed(2));
        });
        var total = subtotal;
        $('#disbursementTotal').text(total.toFixed(2));
        $('#disbursementTotalHidden').val(total.toFixed(2));
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
        $('#disbursementItemsTable tbody').append(newRow);
        itemIndex++;
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Calculate totals when quantity or price changes
    $(document).on('input', '.item-quantity, .item-price', function() {
        calculateTotals();
    });

    // Initial calculation
    calculateTotals();


    $("#disbursementForm").on("submit", function(e){
    	e.preventDefault();
    	var content = $(this).serialize();
    	$.ajax({
    		url:"finance/createDisbursement",
    		method:"POST",
    		data:content,
    		beforeSend:function(){
    			$("#submitBtn").prop("disabled", true).html("Processing...");
    		},
    		success:function(response){
    			if(response.includes("Disbursement Successfully posted")){
                    sweetSuccess(response);
                    setTimeout(function(){
                        location.reload();
                    }, 1500);

                }else{
                    sweetError(response);
                }
    			$("#submitBtn").prop("disabled", false).html('<i class="bi bi-receipt-cutoff"></i> Create Disbursement');
    			
    		}
    	})
    })
});

/*========= Buttons for printing and downloading PDF ==========*/


