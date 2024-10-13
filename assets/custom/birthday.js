$(document).ready(function() {
    $('#birthdayForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'process/submit_birthday',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Data saved successfully');
                    $('#birthdayForm')[0].reset();
                    loadBirthdayData();
                }
            },
            error: function() {
                alert('Error processing request');
            }
        });
    });
});


function loadBirthdayData() {
    $.ajax({
        url: 'process/fetch_birthdays',
        type: 'GET',
        success: function(data) {
            $('#birthdayTableBody').html(data);
        },
        error: function() {
            alert('Error loading data');
        }
    });
}
loadBirthdayData();

$(document).on('click', '.editBirthday-btn', function() {
    var id = $(this).data('id');
    // AJAX call to get the data for the selected row
    $.ajax({
        url: 'process/get_birthday',
        type: 'POST',
        data: { id: id },
        dataType:'Json',
        success: function(data) {
            $('#item_id').val(data.id);
            $('#title').val(data.title);
            $('#names').val(data.names);
            $('#date').val(data.date);
            $('#task_status').val(data.task_status);
            $('#birthdayBtn').click();
        }
    });
});

$(document).on('click', '.deleteBirthday-btn', function() {
    var id = $(this).data('id');
    if (confirm("Are you sure you want to delete this record?")) {
        $.ajax({
            url: 'process/delete_birthday',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                
                alert(response);
                loadBirthdayData();
            }
                
        });
    }
});

$(document).on('change', '.statusCheckbox', function() {
    var eventId = $(this).data('eventid');
    var isChecked = $(this).prop('checked');
    var table = $(this).data('table');
    if (!confirm('Are you sure you want to change the status?')) {
        $(this).prop('checked', false);
        return;
    }

    $.ajax({
        url: 'base/updateStatus',
        method: 'POST',
        data: { eventId: eventId, isChecked: 1, table:table },
        success: function(response) {
            // Handle the response if needed
            alert(response);
            loadBirthdayData();
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

// ===== Checklist ===========
$(document).on('click', '.checkListModal', function (e) {
    $('#new-item').focus();
    e.preventDefault();
    $("#addItemModal").modal('show');
    $('#birthday-id').val($(this).data('id'));
    updateChecklistDisplay($(this).data('id'));
});

$('#add-item-btn').click(function() {
    var item = $('#new-item').val();
    var birthday_id = $('#birthday-id').val();

    $('#add-item-btn').html('Processing...');

    $.post('process/birthdayCreateChecklist', {item: item, birthday_id: birthday_id}, function(data) {
        if (data.success) {
            $('#add-item-btn').html('Add Item');
            updateChecklistDisplay(birthday_id);
        } else {
            $('#add-item-btn').html('Add Item');

            updateChecklistDisplay(birthday_id);
        }
    });
});

function updateChecklistDisplay(birthday_id) {
    $.post('process/getBirthdayChecklist', {birthday_id: birthday_id}, function(data) {
        $('#checkListDiv').html(data);
        $("#checklist-form")[0].reset();
    });
}


$(document).on('change', '.checklist-item', function() {
    var id = $(this).attr('id').replace('check_', '');
    var birthday_id = $(this).data("birthday_id");
    var checked = $(this).is(':checked') ? 1 : 0;

    if (confirm("Are you sure you want to update the status?")) {
        $.post('process/birthdayCheckListUpdate', {id: id, checked: checked}, function(data) {
            // Handle response
            if (!data) {
                alert('Failed to update status. Please try again.');
            } else {
                alert(data);
                updateChecklistDisplay(birthday_id);
            }
        });
    } else {
        // If the user cancels the confirmation, prevent the checkbox from being checked
        $(this).prop('checked', !checked);
    }
});


$(document).on('click', '.delete-item', function() {
    var id = $(this).data('id');
    var birthday_id = $(this).data("birthday_id");


    if (confirm("Are you sure you want to delete this item?")) {
        $.post('process/deleteBirthdayCheckListItem', {id: id}, function(data) {
            // Handle response
            if (data) {
                
                updateChecklistDisplay(birthday_id);
            } else {
                alert('Failed to delete item. Please try again.');
            }
        });
        updateChecklistDisplay(birthday_id);
    }
});

function printContent(el) {
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();

    // Remove the last th and td (Delete column)
    printcontent.find('th:last-child, td:last-child').remove();

    $('body').empty().html(printcontent);

    // Store current scroll position
    var scrollPos = $(window).scrollTop();

    setTimeout(function() {
        window.print();
        setTimeout(function() {
            if ($(window).scrollTop() === scrollPos) {
                location.reload();
            } else {
                $('body').html(restorepage);
            }
        }, 500); 
    }, 100);

    window.onafterprint = function() {
        $('body').html(restorepage);
    };
}


