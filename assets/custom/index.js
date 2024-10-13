
  // Example chart setup
  document.addEventListener('DOMContentLoaded', function() {
        fetch('base/membershipGrowthData')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('membershipGrowthChart').getContext('2d');
                const membershipGrowthChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Febr', 'March', 'April', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Membership Growth',
                            data: Object.values(data),
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching membership growth data:', error));
    });


    $(document).ready(function() {
      $('#attendanceForm').submit(function(event) {
          event.preventDefault();

          var attendanceDate = $('#attendanceDate').val();
          var attendanceNumber = $('#attendanceNumber').val();
          var churchId = $('#churchId').val();
          var userId = $('#userId').val();

          $.ajax({
              url: 'base/addAttendance',
              type: 'POST',
              data: {
                  attendance_date: attendanceDate,
                  attendance: attendanceNumber,
                  churchId: churchId,
                  userId: userId
              },
              beforeSend:function() {
                $("#submitAtt").prop('disabled', true).html("Processing...");
              },
              success: function(response) {
                  alert(response);
                  $('#attendanceForm')[0].reset();
                  // Hide the modal
                  $('#addAttendanceModal').modal('hide');
                  fetchAttendance();
                   $("#submitAtt").prop('disabled', false).html("Submit");
              },
              error: function(xhr, status, error) {
                  console.error(xhr.responseText);
              }
          });
      });
  });

  function fetchAttendance(){
    var fetchAttendance = 'fetchAttendance';
    $.ajax({
        url: 'base/fetchAttendance',
        type: 'POST',
        data: {fetchAttendance:fetchAttendance},
        success: function(response) {
            
            $('#addAttendance').html(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
  }
  fetchAttendance();

  function upcomingEvents(){
    var upcomingEvents = 'upcomingEvents';
    $.ajax({
        url: 'base/upcomingEvents',
        type: 'POST',
        data: {upcomingEvents:upcomingEvents},
        success: function(response) {
            
            $('#upcomingEvents').html(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
  }
  upcomingEvents();


  function newMembersThisMonth(){
    var newMembersThisMonth = 'newMembersThisMonth';
    $.ajax({
        url: 'base/newMembersThisMonth',
        type: 'POST',
        data: {newMembersThisMonth:newMembersThisMonth},
        success: function(response) {
            
            $('#newMembersThisMonth').html(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
  }
  newMembersThisMonth();

  function totalMembers(){
    var totalMembers = 'totalMembers';
    $.ajax({
        url: 'base/totalMembers',
        type: 'POST',
        data: {totalMembers:totalMembers},
        success: function(response) {
            
            $('#totalMembers').html(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
  }
  totalMembers();

  document.addEventListener('DOMContentLoaded', function() {
    fetch('base/incomeTrendsData')
        .then(response => response.json())
        .then(data => {
            const ctx2 = document.getElementById('donationTrendsChart').getContext('2d');
            const donationTrendsChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'March', 'April', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Tithes & Offering',
                        data: Object.values(data),
                        backgroundColor: 'rgba(75, 192, 192, 0.4)', // Brighter background color
                        borderColor: 'rgba(75, 192, 192, 1)', // Brighter border color
                        borderWidth: 1 // Adjust border width if needed
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching donation trends data:', error));
});


