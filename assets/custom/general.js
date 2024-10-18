document.getElementById('church_logo').onchange = function (event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('logo_preview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
};

$(document).ready(function() {
    $('#churchForm').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'settings/updateChurch', // Update this to your actual PHP file for processing
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend:function(){
                $("#submit-btn").prop("disabled", true).html("Processing...");
            },
            success: function(response) {
                alert(response); // Handle response from server
                // Optionally refresh or update part of your page here
                $("#submit-btn").prop("disabled", false).html("Submit Details");
                displayChurchInfo()
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
                alert('An error occurred. Please try again.');
                $("#submit-btn").prop("disabled", false).html("Submit Details");

            }
        });
    });
});


function displayChurchInfo() {
    var church_uid = 'church_uid';
    $.ajax({
        url: 'settings/getChurchInfo', // Update this to your actual PHP file for fetching church info
        method: 'POST',
        data: { church_uid: church_uid },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#church-name').text(response.data.churchName);
                $('#church-email').text(response.data.email);
                $('#church-congregants').text(response.data.congregants);
                $('#church-address').text(response.data.church_address);
                $('#church-pastor').text(response.data.pastor_minister);

                if (response.data.church_logo) {
                    $('#church-logo').attr('src', 'uploads/' + response.data.church_logo).show();
                } else {
                    $('#church-logo').hide();
                }

                $('#church-info-card').show();
                $("#addAminModal").modal("hide");
            } else {
                // alert('Failed to fetch church information.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + error);
            alert('An error occurred. Please try again.');
        }
    });
}

displayChurchInfo();


$(document).ready(function() {
    $('#adminBtn').on('click', function() {
        var churchId = $(this).data('id');
        
        $.ajax({
            url: 'settings/fetchSelectedChurch',
            type: 'POST',
            data: { id: churchId },
            dataType: 'json',
            success: function(response) {
                // $('#church_name').val(response.churchName);
                $('#congregants').val(response.congregants);
                $('#church_address').val(response.church_address);
                $('#pastor_minister').val(response.pastor_minister);
                $('#church_uid').val(response.churchUID);
                $('#church_id').val(response.id);
                if (response.church_logo) {
                    $('#logo_preview').attr('src', 'uploads/'+response.church_logo).show();
                } else {
                    $('#logo_preview').hide();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    $('#church_logo').on('change', function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#logo_preview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(this.files[0]);
    });
});

