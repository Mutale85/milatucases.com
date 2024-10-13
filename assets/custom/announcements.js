$(function(){
	$('#announcementForm').on('submit', function(e) {
	    e.preventDefault();

	    $.ajax({
	        url: 'comms/createAnnouncement',
	        type: 'POST',
	        data: $(this).serialize(),
	        success: function(response) {
	            const result = JSON.parse(response);
	            if (result.status === 'success') {
	                alert(result.message);
	                // location.reload();
	            } else {
	                alert(result.message);
	            }
	            fetchAnnouncements();
	        },
	        error: function() {
	            alert('Error in adding announcement.');
	        }
	    });
	});

})

function fetchAnnouncements() {
    $.ajax({
        url: 'comms/fetchAnnouncements',
        type: 'GET',
        success: function(data) {
            $('#announcementsTable').html(data);
        },
        error: function() {
            alert('Error fetching announcements.');
        }
    });
}
fetchAnnouncements();

$(document).on('click', '.editAnnouncement', function() {
    const id = $(this).data('id');

    $.ajax({
        url: 'comms/fetchSelectedAnnouncement',
        type: 'POST',
        data: { id: id },
        success: function(response) {
            const announcement = JSON.parse(response);
            if (announcement) {
                $('#announcementId').val(announcement.id);
                $('#title').val(announcement.title);
                $('#description').val(announcement.description);
                $('#date').val(announcement.date_time);
                $('#announcementModal').modal('show');
                $('#submitBtn').text('Update Announcement');
            } else {
                alert('Failed to fetch announcement details.');
            }
        },
        error: function() {
            alert('Error fetching announcement details.');
        }
    });
});

$(document).on('click', '.deleteAnnouncement', function() {
    const announcementId = $(this).data('id');
    if(confirm("You wish to delete the announcement")){

	    $.ajax({
	        url: 'comms/deleteAnnouncement',
	        type: 'POST',
	        data: { announcementId: announcementId },
	        success: function(response) {
	            const result = JSON.parse(response);
	            if (result.status === 'success') {
	                alert(result.message);
	                // location.reload();
	                fetchAnnouncements()
	            } else {
	                alert(result.message);
	            }
	        },
	        error: function() {
	            alert('Error in deleting announcement.');
	        }
	    });
	}else{
		return false;
	}
});
