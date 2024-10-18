<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="table table-responsive">
            <table class="table table-borderless">
                <tr>
                   <th><span id="greeting"></span> <?php echo $_SESSION['names']?></th>
                   <td id="time" align="right"></td>
                   
                </tr>
            </table>
        </div>

        <div class="col-md-12">
            <a href="cases/addNewCase" class="btn btn-dark btn-sm">
                <i class="bi bi-archive"></i> + Add Matter
            </a>

            <a href="cc/addNewClient" class="btn btn-primary btn-sm">
                <i class="bi bi-person-bounding-box"></i> + Add Client
            </a>
        </div>
        <div class="col-md-12">
            <div class="dashboard">
                <a href="cases/all" class="cards new-appointment">
                  <h3>MATTERS</h3>
                  <p><?php echo fetchTotalCases($lawFirmId)?></p>
                </a>
                <a href="cc/corporate" class="cards pending-order">
                  <h3>CORPORATE CLIENTS</h3>
                  <p><?php echo fetchTotalCorporateClient($lawFirmId)?></p>
                </a>
                <a href="cc/individual" class="cards successful-appointment">
                  <h3>INDIVIDUAL CLIENTS</h3>
                  <p><?php echo fetchTotalIndividualClients($lawFirmId)?></p>
                </a>
                
                <a href="settings/users" class="cards total-lawyer">
                  <h3>TEAM MEMBERS</h3>
                  <p><?php echo countUsersByFirmId($lawFirmId)?></p>
                </a>
                <a href="calendar/events-company" class="cards earnings-monthly">
                  <h3>UPCOMING EVENTS</h3>
                  <p><?php echo countUpcomingEvents($lawFirmId)?></p>
                </a>
                <a href="cases/all-time-entries" class="cards earnings-total">
                    <?php 
                    $totalBillableTime = getTotalBillableTime($lawFirmId);
                    $totalHours = $totalBillableTime['hours'];
                    $totalMinutes = $totalBillableTime['minutes'];
                    $time = "<small> {$totalHours} HRS : {$totalMinutes} MIN</small>";
                ?>
                  <h3>BILLABLE TIME</h3>
                  <p><?php echo $time?></p>
                </a>
                <a href="ai-features/" class="cards earnings-total">
                    
                  <h3>A.I</h3>
                  <p>Everything A.I</p>
                </a>
            </div>
        </div>
    </div>
    <h5 class="text-center mb-4">Income and Expense Analysis</h5>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Overview</h5>
                    <div id="monthlyChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profit/Loss</h5>
                    <div id="profitLossChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    function getMonthlyTotals($table, $lawFirmId) {
        global $connect;
        
        $dateColumn = ($table === 'tableIncome') ? 'income_date' : 'date_added';
        
        $sql = "SELECT YEAR($dateColumn) as year, MONTH($dateColumn) as month, SUM(amount) as total
                FROM $table
                WHERE lawFirmId = :lawFirmId AND YEAR($dateColumn) = YEAR(CURDATE())
                GROUP BY YEAR($dateColumn), MONTH($dateColumn)
                ORDER BY YEAR($dateColumn), MONTH($dateColumn)";
        
        $stmt = $connect->prepare($sql);
        $stmt->execute(['lawFirmId' => $lawFirmId]);
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[$row['month']] = $row['total'];
        }
        
        return $results;
    }

    // Get data
    $lawFirmId = $_SESSION['parent_id'];
    $income = getMonthlyTotals('tableIncome', $lawFirmId);
    $expenses = getMonthlyTotals('tableExpenses', $lawFirmId);

    // Calculate profit/loss
    $profitLoss = [];
    for ($month = 1; $month <= 12; $month++) {
        $incomeAmount = isset($income[$month]) ? $income[$month] : 0;
        $expenseAmount = isset($expenses[$month]) ? $expenses[$month] : 0;
        $profitLoss[$month] = $incomeAmount - $expenseAmount;
    }

    // Prepare data for charts
    $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $incomeData = $expenseData = $profitLossData = [];
    for ($i = 0; $i < 12; $i++) {
        $incomeData[] = isset($income[$i+1]) ? $income[$i+1] : 0;
        $expenseData[] = isset($expenses[$i+1]) ? $expenses[$i+1] : 0;
        $profitLossData[] = isset($profitLoss[$i+1]) ? $profitLoss[$i+1] : 0;
    }

    // Convert to JSON for use in JavaScript
    $incomeJson = json_encode($incomeData);
    $expenseJson = json_encode($expenseData);
    $profitLossJson = json_encode($profitLossData);
    $monthsJson = json_encode($months);
?>

<script>
        // Monthly Overview Chart
    var monthlyOptions = {
        series: [{
            name: 'Income',
            data: <?php echo $incomeJson; ?>
        }, {
            name: 'Expenses',
            data: <?php echo $expenseJson; ?>
        }],
        chart: {
            type: 'line',
            height: 350,
        },
        colors: ['#00E396', '#FF4560'], // Green for Income, Red for Expenses
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        xaxis: {
            categories: <?php echo $monthsJson; ?>,
        },
        yaxis: {
            title: {
                text: 'Amount'
            },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
        },
        markers: {
            size: 6,
            hover: {
                size: 8
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                    if(typeof y !== "undefined") {
                        return  y.toFixed(0) + " ZMW";
                    }
                    return y;
                }
            }
        }
    };

    var monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyOptions);
    monthlyChart.render();

    // Preprocess data for donut chart
var totalProfit = 0;
var totalLoss = 0;
<?php echo $profitLossJson; ?>.forEach(function(value) {
    if (value > 0) {
        totalProfit += value;
    } else {
        totalLoss += Math.abs(value);
    }
});

var profitLossOptions = {
    series: [totalProfit, totalLoss],
    chart: {
        type: 'donut',
        height: 350
    },
    labels: ['Profit', 'Loss'],
    colors: ['#00E396', '#FF4560'], // Green for Profit, Red for Loss
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '22px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 600,
                        color: undefined,
                        offsetY: -10,
                    },
                    value: {
                        show: true,
                        fontSize: '16px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 400,
                        color: undefined,
                        offsetY: 16,
                        formatter: function (val) {
                            return val.toFixed(2);
                        }
                    },
                    total: {
                        show: true,
                        showAlways: false,
                        label: 'Total',
                        fontSize: '22px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                        fontWeight: 600,
                        color: '#373d3f',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toFixed(2);
                        }
                    }
                }
            }
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var profitLossChart = new ApexCharts(document.querySelector("#profitLossChart"), profitLossOptions);
profitLossChart.render();
</script>
