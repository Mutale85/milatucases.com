<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Profit / Loss Sections</title>
	<?php include '../addon_header.php'; ?>
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
          							<div class="card-header">
          								<h5 class="card-title">Business Margins</h5>
          							</div>
          							<div class="card-body">
          								<div class="row">
          									<div class="col-md-6">
	          									<div id="chart-line"></div>
	          								</div>
	          								<div class="col-md-6">
	    										<div id="chart-bar"></div>
	    									</div>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'finance/fetchBusinessMargins',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var optionsLine = {
                        chart: {
                            type: 'line',
                            height: 350,
                            zoom: {
                                enabled: false
                            }
                        },
                        series: [{
                            name: 'Income',
                            data: data.monthlyIncomes
                        }, {
                            name: 'Expenses',
                            data: data.monthlyExpenses.map(function(val) { return -val; })
                        }],
                        xaxis: {
                            categories: data.months
                        }
                    };

                    var optionsBar = {
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        series: [{
                            name: 'Incomes',
                            data: data.monthlyIncomes
                        }, {
                            name: 'Expenses',
                            data: data.monthlyExpenses
                        }],
                        xaxis: {
                            categories: data.months,
                            title: {
                                text: 'Month'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Amount'
                            }
                        }
                    };

                    var chartLine = new ApexCharts(document.querySelector("#chart-line"), optionsLine);
                    chartLine.render();

                    var chartBar = new ApexCharts(document.querySelector("#chart-bar"), optionsBar);
                    chartBar.render();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
    </script>
</body>
</html>