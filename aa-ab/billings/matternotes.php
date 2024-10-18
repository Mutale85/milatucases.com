<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../assets/" data-template="vertical-menu-template-starter">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName']?>'s Time Entries</title>
	<?php include '../addon_header.php'; ?>
	<style>
		 table {
      width: 100%;
      border-collapse: collapse;
      font-family: Arial, sans-serif;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }

    .filter-container {
      margin-bottom: 10px;
    }

    .filter-container input {
      width: 100%;
      padding: 6px 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    @media (max-width: 767px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      th {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }

      td {
        border: none;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 50%;
      }

      td:before {
        position: absolute;
        top: 6px;
        left: 6px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
      }
			
			td:nth-of-type(1):before { content: "Client"; }
      td:nth-of-type(2):before { content: "Date"; }
      td:nth-of-type(3):before { content: "Description"; }
      td:nth-of-type(4):before { content: "Matter"; }
      td:nth-of-type(5):before { content: "Client"; }
      td:nth-of-type(6):before { content: "Time"; }
      td:nth-of-type(7):before { content: "Rate"; }
      td:nth-of-type(8):before { content: "Cost"; }
      td:nth-of-type(9):before { content: "Staff"; }
      td:nth-of-type(10):before { content: "Status"; }
      td:nth-of-type(11):before { content: "Action"; }
    }
	</style>
</head>
<body>
	<div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<?php include '../addon_side_nav.php'; ?>
        	<div class="layout-page">
          		<?php include '../addon_top_nav.php'; ?>
          		<div class="content-wrapper">
          			<div class="container-xxl flex-grow-1 container-p-y">
          				<div class="row">
          					<div class="col-md-12">
          						<div class="card">
          							<div class="card-header d-flex justify-content-between align-items-center">
												    <h5 class="card-title text-primary">Time Entries</h5>
												    <button class="btn btn-primary btn-sm" id="newTimerBtn">New Time Entry</button>
												</div>
          							<div class="card-body">
          								<div class="table table-responsive">
          									<div class="filter-container">
													    <input type="text" id="filter-input" placeholder="Search table...">
													  </div>
													  <table id="data-table">
													    <thead>
													      <tr>
													      	<th>Client</th>
													        <th>Description</th>
													        <th>Matter</th>
													        <th>Time</th>
													        <th>Rate</th>
													        <th>Cost</th>
													        <th>Staff</th>
													        <th>Status</th>
													        <th>Action</th>
													      </tr>
													    </thead>
													    <tbody id="timeData">
													      <!-- Populate the table rows with data from the MySQL table -->
													    </tbody>
													  </table>
													  <!-- <button type="submit" class="btn btn-primary mt-2" id="invoiceSelectedBtn" disabled>Invoice Selected Client's Time</button> -->
													  <?php include '../inv/invoiceModalForm.php' ?>
          								</div>
          							</div>
          						</div>
          					</div>
          				</div>
          			</div>
          			<?php include '../addon_footer.php';?>

          			<div class="content-backdrop fade"></div>
          		</div>
          	</div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer_links.php';?>
    <script>
    	
    		fetchTimeData()
    		function fetchTimeData(){
				  $.ajax({
				    url: 'cases/fetchMatterTimeEntries',
				    method: 'POST',
				    // dataType: 'json',
				    success: function(data) {
				      
				      $("#timeData").html(data);

				    },
				    error: function(xhr, status, error) {
				      console.error('Error fetching data:', error);
				    }
				  });
				}
				fetchTimeData();
			

        // Filter table based on search input
        const filterInput = document.getElementById('filter-input');
        const dataTable = document.getElementById('data-table');
        const tableRows = dataTable.getElementsByTagName('tr');

        filterInput.addEventListener('input', () => {
          const filterText = filterInput.value.toLowerCase();

          for (let i = 1; i < tableRows.length; i++) {
            const row = tableRows[i];
            const cells = row.getElementsByTagName('td');
            let rowVisible = false;

            for (let j = 0; j < cells.length; j++) {
              if (cells[j].textContent.toLowerCase().includes(filterText)) {
                rowVisible = true;
                break;
              }
            }

            row.style.display = rowVisible ? '' : 'none';
          }
        });
      
      	$("#newTimerBtn").click(function(){
      		$("#timeEntryModal").modal("show");
      	})
      
      	// Get the relevant form elements
				const hoursInput = document.getElementById('hours');
				const minutesInput = document.getElementById('minutes');
				const hourlyRateInput = document.getElementById('hourlyRate');
				const costInput = document.getElementById('cost');

				// Add event listeners to the hours and minutes inputs
				hoursInput.addEventListener('input', calculateCost2);
				minutesInput.addEventListener('input', calculateCost2);
				hourlyRateInput.addEventListener('input', calculateCost2);

				// Function to calculate the cost
				function calculateCost2() {
				  const hours = parseFloat(hoursInput.value) || 0;
				  const minutes = parseFloat(minutesInput.value) || 0;
				  const hourlyRate = parseFloat(hourlyRateInput.value) || 0;

				  const totalMinutes = hours * 60 + minutes;
				  const cost = (totalMinutes / 60) * hourlyRate;

				  costInput.value = cost.toFixed(2);
				}

				/*=========the invoice buttons=============*/
						
		</script>
</body>
</html>