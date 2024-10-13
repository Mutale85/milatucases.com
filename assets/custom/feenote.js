$(document).ready(function() {
    // Handle edit button click
    $('.editLog').on('click', function() {
        const row = $(this).closest('tr');
        const id = $(this).data('id');
        const description = row.find('td:nth-child(2)').text();
        const hourlyRate = row.find('td:nth-child(4)').text().replace(/,/g, '');

        // Populate the modal form with data
        $('#logId').val(id);
        $('#taskDescription').val(description);
        $('#hourlyRate').val(hourlyRate);

        // Show the modal
        $('#timerModal').modal('show');
    });

    // Handle form submission
    $('#timerForm').on('submit', function(event) {
        event.preventDefault();

        const logId = $('#logId').val();
        const taskDescription = $('#taskDescription').val();
        const hourlyRate = $('#hourlyRate').val();

        $.ajax({
            url: 'cases/updateTimerLog',
            method: 'POST',
            data: {
                logId: logId,
                taskDescription: taskDescription,
                hourlyRate: hourlyRate
            },
            success: function(response) {
                if (response.success) {
                    // Update the table row with new values
                    const row = $(`tr[data-id='${logId}']`);
                    row.find('td:nth-child(2)').text(taskDescription);
                    row.find('td:nth-child(4)').text(parseFloat(hourlyRate).toFixed(2));
                    row.find('td:nth-child(5)').text(parseFloat(response.totalCharge).toFixed(2));

                    // Recalculate the total amount
                    let totalAmount = 0;
                    $('tbody tr').each(function() {
                        const totalCharge = parseFloat($(this).find('td:nth-child(5)').text().replace(/,/g, ''));
                        totalAmount += totalCharge;
                    });
                    $('tfoot tr td:nth-child(2)').text(totalAmount.toFixed(2));
                    location.reload();
                    // Close the modal
                    $('#timerModal').modal('hide');
                } else {
                    alert('Update failed');
                }
            },
            dataType: 'json'
        });
    });


    $('.deleteLog').on('click', function() {
        const logId = $(this).data('id');
        const row = $(this).closest('tr');

        if (confirm('Are you sure you want to delete this log?')) {
            $.ajax({
                url: 'cases/deleteTimerLog',
                method: 'POST',
                data: {
                    logId: logId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the table row
                        row.remove();

                        // Recalculate the total amount
                        let totalAmount = 0;
                        $('tbody tr').each(function() {
                            const totalCharge = parseFloat($(this).find('td:nth-child(5)').text().replace(/,/g, ''));
                            totalAmount += totalCharge;
                        });
                        $('tfoot tr td:nth-child(2)').text(totalAmount.toFixed(2));
                    } else {
                        sweetError('Delete failed');
                    }
                },
                dataType: 'json'
            });
        }
    });

    $('#generatePdf').on('click', function() {
        var clientId = $(this).data('client-id');
        var caseId = $(this).data('case-id');
        $.ajax({
            url: 'cases/generateFeeNotePdf',
            method: 'POST',
            data:{clientId:clientId, caseId:caseId},
            beforeSend:function(){
                $('#generatePdf').prop("disabled", true).html("Processing...");
            },
            success: function(response) {
                if (response.success) {
                    sweetSuccess("PFD Created and Saved in Your Library");
                    window.open("cases/" + response.pdfUrl, '_blank');

                } else {
                    sweetError('Failed to generate PDF');
                }
                $('#generatePdf').prop("disabled", false).html('<i class="bi bi-file-pdf"></i> Generate PDF');
            },
            dataType: 'json'
        });
    });

    $(document).on('click', '.send-email-btn', function() {
        var clientId = $(this).data('client-id');
        var caseId = $(this).data('case-id');
        var clientEmail = $(this).data('client-email');
        $('#emailInput').val(clientEmail);
        $('#clientIdInput').val(clientId);
        $('#caseIdInput').val(caseId);
        $('#emailModal').modal('show');
    });

    $('#sendEmailBtn').on('click', function() {
        var email = $('#emailInput').val();
        var clientId = $('#clientIdInput').val();
        var caseId = $('#caseIdInput').val();

        if (!email) {
            sweetError('Please enter an email address.');
            return;
        }

        $.ajax({
            url: 'cases/sendFeeNoteEmail',
            method: 'POST',
            data: {
                clientId: clientId,
                caseId: caseId,
                clientEmail: email
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

});