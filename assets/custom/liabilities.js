$(document).ready(function() {
    $('#liabilityForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'assets/createLiability',
            data: formData,
            beforeSend: function() {
                $("#submitBtn").prop('disabled', true).html('Processing...');
            },
            success: function(response) {
                $('#liabilityModal').modal('hide');
                alert(response);
                displayLiabilities(); // Assuming this function displays the updated list of liabilities
                $('#liabilityForm')[0].reset();
                $("#submitBtn").prop('disabled', false).html('Record Liability');
            },
            error: function(xhr, status, error) {
                alert('An error occurred while processing your request.');
                $("#submitBtn").prop('disabled', false).html('Record Liability');
            }
        });
    });

    function displayLiabilities() {
        $.ajax({
            type: 'GET',
            url: 'assets/fetchLiabilities',
            success: function(response) {
                $('#liabilitiesTable').html(response);
            }
        });

        $.ajax({
            type: 'GET',
            url: 'assets/fetchTotalLiabilities',
            success: function(response) {
                $('#liabilitiesTableTotal').html(response);
            }
        });
    }

    displayLiabilities();
});

$(document).on('click', '.editLiability', function() {
    var liabilityId = $(this).data('id');
    
    $.ajax({
        type: 'POST',
        url: 'assets/fetchSelectedLiability',
        data: {liabilityId: liabilityId},
        dataType: 'json',
        success: function(response) {
            $('#liabilityType').val(response.liability_type);
            $('#liabilityName').val(response.liability_name);
            $('#description').val(response.description);
            $('#amount').val(response.amount);
            $('#dueDate').val(response.due_date);
            $('#assignedTo').val(response.assigned_to);
            $('#liabilityId').val(liabilityId); // Set the liabilityId in a hidden field for later use
            $('#liabilityModal').modal('show');
        },
        error: function(xhr, status, error) {
            alert('An error occurred while fetching the liability details.');
        }
    });
});

$(document).on('click', '.deleteLiability', function() {
    if (confirm('Are you sure you want to delete this liability?')) {
        var liabilityId = $(this).data('id');
        
        $.ajax({
            type: 'POST',
            url: 'assets/deleteLiability',
            data: {liabilityId: liabilityId},
            success: function(response) {
                alert(response);
                displayLiabilities();
            },
            error: function(xhr, status, error) {
                alert('An error occurred while deleting the liability.');
            }
        });
    }
});
