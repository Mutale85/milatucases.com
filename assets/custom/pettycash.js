
$(document).ready(function() {
  	$('#pettyCashForm').submit(function(event) {
    	event.preventDefault(); 
    	var formData = $(this).serialize();
    	$.ajax({
      		url: 'finance/submitPettyCash',
      		type: 'POST',
      		data: formData,
      		beforeSend:function(){
      			$("#submitBtn").prop("disabled", true).html("Processing...");
      		},
      		success: function(response) {
          		$('#pettyCashForm')[0].reset();
        		alert(response);
        		fetchData();
        		$("#submitBtn").prop("disabled", false).html("Submit Data");
      		}
    	});
  	});
})

function fetchData() {
  var petty_cash = 'petty_cash';
  $.ajax({
    url: 'finance/fetchPettyCash',
    type: 'POST',
    data: {petty_cash:petty_cash},
    success: function(data) {
      $('#petty_cash').html(data);
    }
  });
}
fetchData();