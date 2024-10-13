$(document).ready(function() {
    $('#assetForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var assetId = $('#assetId').val();
        var url =  'assets/createAsset';

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            beforeSend: function() {
                $("#submitBtn").prop('disabled', true).html('Processing...');
            },
            success: function(response) {
                $('#assetModal').modal('hide');
                alert(response);
                displayAssets(); // Assuming this function displays the updated list of assets
                // $('#assetForm')[0].reset();
                $("#submitBtn").prop('disabled', false).html('Record Asset');
            },
            error: function(xhr, status, error) {
                alert('An error occurred while processing your request.');
                $("#submitBtn").prop('disabled', false).html('Record Asset');
            }
        });
    });

    function displayAssets() {
        $.ajax({
            type: 'GET',
            url: 'assets/fetchAssets',
            success: function(response) {
                $('#assetsTable').html(response);
            }
        });

        $.ajax({
            type: 'GET',
            url: 'assets/fetchTotalAssets',
            success: function(response) {
                $('#assetsTableTotal').html(response);
            }
        });
    }

    displayAssets();
});

$(document).on('click', '.editAsset', function() {
    var assetId = $(this).data('id');
    
    $.ajax({
        type: 'POST',
        url: 'assets/fetchSelectedAsset',
        data: {assetId: assetId},
        dataType: 'json',
        success: function(response) {
            $('#categoryName').val(response.category_name);
            $('#assetName').val(response.asset_name);
            // $('#description').val(response.description);
            $('#location').val(response.location);
            $('#purchaseDate').val(response.purchase_date);
            $('#purchasePrice').val(response.purchase_price);
            $('#currentValue').val(response.current_value);
            $('#condition').val(response.status);
            // $('#maintenanceSchedule').val(response.maintenance_schedule);
            $('#assignedTo').val(response.assigned_to);
            $('#assetId').val(assetId); // Set the assetId in a hidden field for later use
            $('#assetModal').modal('show');
        },
        error: function() {
            alert('An error occurred while fetching the asset data.');
        }
    });
});

$(document).on('click', '.deleteAsset', function() {
    var assetId = $(this).data('id');
    
    if (confirm("Are you sure you want to delete this asset?")) {
        $.ajax({
            type: 'POST',
            url: 'assets/deleteAsset',
            data: {assetId: assetId},
            success: function(response) {
                alert(response);
                displayAssets();
            },
            error: function() {
                alert('An error occurred while deleting the asset.');
            }
        });
    }
});
