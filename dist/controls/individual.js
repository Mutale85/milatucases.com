$(document).ready(function() {
    $('#addClientModal').on('click', function() {
        if ($(this).data("id") === 'Corporate') {
            $('#corporateFields').show();
            $('#individualFields').hide();
            $('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', true);
            $('#client_names, #client_phone, #client_email, #clientTpin').prop('required', false);
        } else if ($(this).data("id") === 'Individual') {
            $('#corporateFields').hide();
            $('#individualFields').show();
            $('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', false);
            $('#client_names, #client_phone, #client_email, #clientTpin').prop('required', true);
            $("#clientType").val("Individual").prop("readonly", true);
        } else {
            $('#corporateFields, #individualFields').hide();
        }
    });
});

$(document).ready(function() {
    $('#clientsForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'cc/createInitialClient',
            data: formData,
            beforeSend: function() {
                $('#saveClient').prop("disabled", true).html(`Processing ... <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
                </div>`);
            },
            success: function(response) {
                $('#saveClient').prop("disabled", false).html("Save Client");
                $('#clientModal').modal('hide');
                sweetSuccess(response);
                $('#clientsForm')[0].reset();
                fetchIndividualInitialData();
            },
            error: function(xhr, status, error) {
                $('#saveClient').prop("disabled", false).html("Save Client");
                sweetError(error);
            }
        });
    });
});

function fetchIndividualInitialData(){
    $.ajax({
        url: "base/fetchIndividualInitialData",
        type: "GET",
        success: function(response) {
            $("#fetchIndividualInitialTable").html(response);
        },
    });
}

fetchIndividualInitialData();

$(document).on("click", '.editClient', function(e){
    e.preventDefault();
    var clientId = $(this).data('id');
    $.ajax({
        url: 'base/fetchSelectedIndividualInitialData', // The URL for the server-side script
        type: 'POST',
        data: { id: clientId },
        dataType: 'JSON',
        success: function(data) {
            // Populate the form fields with the fetched data
            $('#client_id').val(clientId);
            $('#client_names').val(data.client_names);
            $('#client_phone').val(data.client_phone);
            $('#client_email').val(data.client_email);
            $('#clientTpin').val(data.client_tpin);
            $('#clientModal').modal('show');
            $('#corporateFields').hide();
            $('#individualFields').show();
            $('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', false);
            $('#client_names, #client_phone, #client_email, #clientTpin').prop('required', true);
            $("#clientType").val("Individual").prop("readonly", true);

        },
        error: function(xhr, status, error) {
            console.error('Error fetching client data:', error);
        }
    });
});

$(document).on("click", '.removeClient', function(e){
    e.preventDefault();
    var clientId = $(this).data('id');
    if (confirm('Are you sure you want to remove this client?')) {
        fetch(`base/removeClientInitialData?id=${clientId}`, { method: 'DELETE' })
        .then(response => response.text())
        .then(result => {
            alert(result);
            fetchIndividualInitialData(); // Refresh the client list
        })
        .catch(error => console.error('Error:', error));
    }
});

$(document).on("click", '.sendKYC', function(e){
    e.preventDefault();
    var clientId = $(this).data('id');
    var email = $(this).data('email');
    var names = $(this).data('names');
    var firm = $(this).data('firm');
    var tpin = $(this).data('tpin');
    
    if(confirm("Confirm you wish to send KYC form to: "+ names)){
       $('#processingModal').modal('show');
        $.ajax({
            url: 'base/sendFormToIndividualClient',
            type: 'POST',
            data: {
                clientId: clientId,
                email: email,
                names: names,
                firm: firm,
                tpin: tpin
            },
            beforeSend:function(){
                $(this).prop("disabled", true).html("Sending...");
            },
            success: function(response) {
                if(response.includes("Email sent successfully")){
                    sweetSuccess(response);
                    fetchIndividualInitialData();
                }else{
                   sweetError(response); 
                }
                $(this).prop("disabled", false).html(`<i class="bi bi-send"></i> Send KYC`);
                $('#processingModal').modal('hide');
            },
            error: function(xhr, status, error) {
                // Handle errors
                sweetError(error);
                $(this).prop("disabled", false).html(`<i class="bi bi-send"></i> Send KYC`);
            }
        });
    }else{
        return false;
    }
});

$(document).on("click", ".fetchClientId", function(e){
    e.preventDefault();
    var clientId = $(this).data("id");
    document.getElementById("clientId").value = clientId;
    $("#addNewCaseModal").modal("show");
})
