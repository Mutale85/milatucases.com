
    $(document).on("click", '.editClient', function(e){
        e.preventDefault();
        var clientId = $(this).data('id');
        $.ajax({
            url: 'base/fetchSelectedClientInitiaDetails',
            type: 'POST',
            data: { id: clientId },
            dataType: 'JSON',
            success: function(data) {            

                // Check client type and display fields accordingly
                if (data.client_type === 'Corporate') {
                    $('#business_entity_name').val(data.business_name);
                    $('#incorporation_number').val(data.incorporation_number);
                    $('#business_tpin').val(data.client_tpin);
                    $('#representativeName').val(data.client_names);
                    $('#representative_email').val(data.client_email);
                    $('#representative_phone').val(data.client_phone);
                    $('#business_address').val(data.address);
                    $('#corporateFields').show();
                    $('#individualFields').hide();
                    $('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', true);
                    $('#client_names, #client_phone, #nrc_passport_number, #client_address').prop('required', false);
                    
                } else {
                    $('#client_names').val(data.client_names);
                    $('#nrc_passport_number').val(data.nrc_passport_number);
                    $('#client_phone').val(data.client_phone);
                    $('#client_email').val(data.client_email);
                    $('#clientTpin').val(data.client_tpin);
                    $('#client_address').val(data.address);
                    $('#corporateFields').hide();
                    $('#individualFields').show();
                    $('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', false);
                    $('#client_names, #client_phone, #client_address, #nrc_passport_number').prop('required', true);

                }
                $('#client_id').val(data.id);

                $("#clientType").val(data.client_type).prop("readonly", true);

                $('#clientModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching client data:', error);
            }
        });
    });

    $(document).ready(function() {
        var typeClient = document.getElementById('typeClient').value;
        if(typeClient === 'Corporate'){
            $('#clientType').val(typeClient);
            $('#corporateFields').show();
            $('#individualFields').hide();
            $('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', false);
            $('#client_names, #client_phone, #client_address, #nrc_passport_number').prop('required', true);
        }else if(typeClient == 'Individual'){
            $('#clientType').val(typeClient);
            $('#corporateFields').hide();
            $('#individualFields').show();
            $('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', false);
            $('#client_names, #client_phone, #client_address, #nrc_passport_number').prop('required', true);
        }else{
            $('#clientType').val('');
            $('#corporateFields, #individualFields').hide();
        }
        $('#clientType').on('change', function() {
            if ($(this).val() === 'Corporate') {
                $('#corporateFields').show();
                $('#individualFields').hide();
                $('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', true);
                $('#client_names, #client_phone, #nrc_passport_number, #client_address').prop('required', false);
            } else if ($(this).val() === 'Individual') {
                $('#corporateFields').hide();
                $('#individualFields').show();
                $('#business_entity_name, #incorporation_number, #business_tpin, #representativeName, #representative_email, #representative_phone, #business_address').prop('required', false);
                $('#client_names, #client_phone, #client_address, #nrc_passport_number').prop('required', true);
            } else {
                $('#corporateFields, #individualFields').hide();
            }
        });
    });

    $(document).ready(function() {
        $('#clientsForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            // if(navigator.onLine){
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
                        fetchLawFirmClients();
                    },
                    error: function(xhr, status, error) {
                        $('#saveClient').prop("disabled", false).html("Save Client");
                        sweetError(error);
                    }
                });
            // }else{
            //     sweetError("Please check your internet connection ");
            // }
        });
    });

    $(document).on("click", '.sendKYC', function(e){
        e.preventDefault();
        var clientId = $(this).data('id');
        var email = $(this).data('email');
        var names = $(this).data('names');
        var firm = $(this).data('firm');
        var tpin = $(this).data('tpin');
        var busiName = $(this).data('bname');
        var clientType = $(this).data('type'); // Assuming client type is added as a data attribute

        // Determine the confirmation message and URL based on client type
        var confirmationMessage = clientType === 'Corporate' 
            ? "Confirm you wish to send KYC form to: " + busiName
            : "Confirm you wish to send KYC form to: " + names;
        
        var url = clientType === 'Corporate' 
            ? 'base/sendFormToCorporateClient'
            : 'base/sendFormToIndividualClient';
        
        if(confirm(confirmationMessage)){
            $('#processingModal').modal('show');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    clientId: clientId,
                    email: email,
                    names: names,
                    firm: firm,
                    tpin: tpin,
                    busiName: busiName // This will be ignored for individual clients
                },
                beforeSend: function() {
                    $(this).prop("disabled", true).html("Sending...");
                },
                success: function(response) {
                    if(response.includes("Email sent successfully")){
                        sweetSuccess(response);
                       	fetchLawFirmClients();
                    } else {
                        sweetError(response);
                    }
                    $(this).prop("disabled", false).html(`<i class="bi bi-send"></i> Send KYC`);
                    $('#processingModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    sweetError(error);
                    $(this).prop("disabled", false).html(`<i class="bi bi-send"></i> Send KYC`);
                }
            });
        } else {
            return false;
        }
    });


    $(document).on("click", '.removeClient', function(e) {
        e.preventDefault();
        var clientId = $(this).data('id');

        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You can recover the client's data in the archives folder.!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `base/removeClientInitialData?clientId=${clientId}`,
                    method: 'POST',
                    success: function(response) {
                        Swal.fire(
                            'Removed!',
                            response,
                            'success'
                        );
                        fetchLawFirmClients(); // Refresh the client list
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });
    });


    function fetchLawFirmClients(){
        $.ajax({
            url: "base/fetchClientsInitialData",
            type: "GET",
            success: function(response) {
                $("#allClients").html(response);
            },
        });
        fetchLawFirmCorporateClients();
        fetchLawFirmIndividualClients();
    }
    fetchLawFirmClients();

    function fetchLawFirmCorporateClients(){
        $.ajax({
            url: "base/fetchCorporateClientsInitialData",
            type: "GET",
            success: function(response) {
                $("#displayCorporate").html(response);
            },
        });
    }
    function fetchLawFirmIndividualClients(){
        $.ajax({
            url: "base/fetchIndividualClientsInitialData",
            type: "GET",
            success: function(response) {
                $("#displayIndividual").html(response);
            },
        });
    }
// }else{
//     sweetError("Please check your connection");

// }



