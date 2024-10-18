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
                fetchCorporateInitialData();
            },
            error: function(xhr, status, error) {
                $('#saveClient').prop("disabled", false).html("Save Client");
                sweetError(error);
            }
        });
    });
});


function fetchCorporateInitialData(){
    
    $.ajax({
        url: "base/fetchCorporateInitialData",
        type: "GET",
        success: function(response) {
            $("#fetchCorporateInitialTable").html(response);
        },
    });
}

fetchCorporateInitialData();

$(document).on("click", '.editClient', function(e){
    e.preventDefault();
    var clientId = $(this).data('id');
    $.ajax({
        url: 'base/fetchSelectedCorporateInitialData',
        type: 'POST',
        data: { id: clientId },
        dataType: 'JSON',
        success: function(data) {
            // Populate the form fields with the fetched data
            $('#client_id').val(clientId);
            $('#business_entity_name').val(data.business_name);
            $('#business_tpin').val(data.client_tpin);
            $('#representativeName').val(data.client_names);
            $('#representative_email').val(data.client_email);
            $('#representative_phone').val(data.client_phone);
            $('#allow_login').prop('checked', data.allow_login === 1);
            $('#corporateFields').show();
            $('#individualFields').hide();
            $('#business_entity_name, #business_tpin, #representativeName, #representative_email, #representative_phone').prop('required', true);
            $('#client_names, #client_phone, #client_email, #clientTpin').prop('required', false);

            $('#clientModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching client data:', error);
        }
    });

})


$(document).on("click", '.removeClient', function(e){
    e.preventDefault();
    var clientId = $(this).data('id');
    if (confirm('Are you sure you want to remove this client?')) {
        fetch(`base/removeClientInitialData?id=${clientId}`, { method: 'DELETE' })
        .then(response => response.text())
        .then(result => {
            alert(result);
            fetchCorporateInitialData(); // Refresh the client list
        })
        .catch(error => console.error('Error:', error));
    }
})


$(document).on("click", '.sendKYC', function(e){
    e.preventDefault();
    var clientId = $(this).data('id');
    var email = $(this).data('email');
    var names = $(this).data('names');
    var firm = $(this).data('firm');
    var tpin = $(this).data('tpin');
    var busiName = $(this).data('bname');
    if(confirm("Confirm you wish to send KYC form to: "+ busiName)){
        $('#processingModal').modal('show');
        $.ajax({
            url: 'base/sendFormToCorporateClient',
            type: 'POST',
            data: {
                clientId: clientId,
                email: email,
                names: names,
                firm: firm,
                tpin: tpin,
                busiName: busiName
            },
            success: function(response) {
                if(response.includes("Email sent successfully")){
                    sweetSuccess(response);
                }else{
                    sweetError(response);
                }
                fetchCorporateInitialData();
                $('#processingModal').modal('hide');
            },
            error: function(xhr, status, error) {
                sweetError(error);
                $('#processingModal').modal('hide');
            }
        });
    }
        
})
