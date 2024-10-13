function fetchHeadsOfDepartmentToSMS(){
    var hods = "fetchHoDs";
    $.ajax({
        url: 'contacts/fetchSMSHoD', // Your PHP script to process the event
        type: 'POST',
        data: {hods: hods},
        success: function(response) {
            $("#hodsTable").html(response);
            
        }
    });
}

fetchHeadsOfDepartmentToSMS();

$(document).ready(function () {
    $(document).on('click', '#selectAll', function () {
        $('.selectHod').prop('checked', this.checked);
    });

    $(document).on('change', '.selectHod', function () {
        var totalCheckboxes = $('.selectHod').length;
        var totalChecked = $('.selectHod:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === totalChecked);
    });


    $('#messageForm').submit(function (e) {
        e.preventDefault();
        
        // Check if the internet is connected
        if (!navigator.onLine) {
            alert('Please check your internet connection and try again.');
            return; // Stop the function from proceeding
        }
        
        var selectedHods = [];
        $('.selectHod:checked').each(function () {
            selectedHods.push({phone: $(this).data('phone'), email: $(this).data('email')});
        });
        
        // Check if at least one checkbox has been checked
        if (selectedHods.length === 0) {
            alert('Please select at least one head of department.');
            return; // Stop the function from proceeding
        }
        
        var message = $('#message').val();

        $.ajax({
            url: 'contacts/sendMessageEmail', // Your PHP script to process the event
            type: 'POST',
            data: {
                message: message,
                to: JSON.stringify(selectedHods) // Convert the array to a JSON string
            },
            beforeSend: function() {
                // Optionally, you can add code here to show a loading spinner or message
            },
            success: function(response) {
                alert(response);
                // Optionally, hide the loading spinner or message here
            }
        });
    });

});





document.getElementById('message').addEventListener('input', function () {
    this.style.height = 'auto'; // Reset height to allow for shrinking
    this.style.height = this.scrollHeight + 'px'; // Set height based on content
});

