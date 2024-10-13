<?php 
    include "../includes/db.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_SESSION['lawFirmName']?>'s Dashboard</title>
    <?php include 'addon_header.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/5.1.0/introjs.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/5.1.0/intro.min.js"></script>
    <?php
      $lawFirmId = $_SESSION['parent_id'];

      $query = $connect->prepare("SELECT currency, SUM(amount) as total_income, date_added FROM tableIncome WHERE lawFirmId = ?");
      $query->execute([$lawFirmId]);
      $incomeData = $query->fetch(PDO::FETCH_ASSOC);
      $currency = $incomeData['currency'];
      $totalIncome = $incomeData['total_income'];

      // Fetch data from the tableExpenses table
      $stmt = $connect->prepare("SELECT currency, SUM(amount) as total_expenses, date_added FROM tableExpenses WHERE lawFirmId = ?");
      $stmt->execute([$lawFirmId]);
      $expensesData = $stmt->fetch(PDO::FETCH_ASSOC);
      $currency = $expensesData['currency'];
      $totalExpenses = $expensesData['total_expenses'];

      // Calculate the net profit
      
      $netProfit = $totalIncome - $totalExpenses;

      // Calculate the profit percentage
      $profitPercentage = 0;
      if ($totalIncome > 0) {
          $profitPercentage = ($netProfit / $totalIncome) * 100;
      }

      if ($profitPercentage >= 0) {
          $profitClass = "text-success";
          $profitIcon = "bx bx-up-arrow-alt";
      } else {
          $profitClass = "text-danger";
          $profitIcon = "bx bx-down-arrow-alt";
      }

      $showGuide = isset($_SESSION['first_login']) && $_SESSION['first_login'];

    ?>
    <style>
        .highlight {
            box-shadow: 0 0 10px 5px rgba(255, 165, 0, 0.5);
            transition: box-shadow 0.3s ease-in-out;
        }

    </style>
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'addon_side_nav.php'; ?>
            <div class="layout-page">
                <?php include 'addon_top_nav.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row">
                        <h5 class="card-title"><small><span id="greeting"></span></small> <?php echo $_SESSION['names']?></h5>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><a href="cases/all"><i class="bi bi-window-stack"></i> Cases - <?php echo fetchTotalCases($lawFirmId)?></a></h5> 
                                </div>
                                <div class="card-body">
                                    <?php echo getRecentCases($lawFirmId);?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Clients</h5>
                                </div>
                                <div class="card-body">
                                    <p><a href="cc/corporate"><i class="bi bi-briefcase"></i> Corporate: <?php echo fetchTotalCorporateClient($lawFirmId)?></a> | <a href="cc/individual"><i class="bi bi-people"></i> Individual: <?php echo fetchTotalIndividualClients($lawFirmId)?></a></p>
                                    <?php echo getRecentClients($lawFirmId)?>
                                </div>
                                <div class="card-footer" id="element3">
                                  <a href="cc/addNewClient" class="btn btn-dark btn-sm mb-2" id="addCorporateClientBtn" data-intro="Use this button to add new  clients" data-step="2"><i class="bi bi-person-bounding-box"></i> Add  Client</a>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><a href="users"> <i class="bi bi-people"></i> Users </a></h5>
                                </div>
                                <div class="card-body">
                                    <h3><?php echo countUsersByFirmId()?></h3>
                                    <a href="users" class="btn btn-dark btn-sm"><i class="bi bi-person"></i> Add Member</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                          <?php
                            $data = getTimeLogsData($lawFirmId);
                            $totalTime = getTotalBillableTime($lawFirmId);
                          ?>
                          <div class="card">
                            <div class="row row-bordered g-0">
                              <div class="col-md-8">
                                <h5 class="card-header m-0 me-2 pb-3">Billable Time</h5>
                                <div id="totalHoursChart" class="px-2"></div>
                              </div>
                              <div class="col-md-4">
                                <div class="card-body">
                                  <div class="text-center">
                                  <div class="text-center fw-semibold pt-3 mb-2">Total Time: </div>
                                    <?php 
                                        $totalBillableTime = getTotalBillableTime($lawFirmId);
                                        $totalHours = $totalBillableTime['hours'];
                                        $totalMinutes = $totalBillableTime['minutes'];
                                        
                                        echo "<p> {$totalHours} hours and {$totalMinutes} minutes</p>";
                                    ?>
                                    <p><a href="cases/workedtime"> <i class="bi bi-clock-history"></i> View More </a></p>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    
                    </div>
                    <?php include 'addon_footer.php';?>

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
 
    <?php include 'addon_footer_links.php';?>
    <script>
        function updateGreeting() {
            const now = new Date();
            const hours = now.getHours();
            let greeting;

            if (hours < 12) {
                greeting = 'Morning!';
            } else if (hours < 18) {
                greeting = 'Afternoon!';
            } else {
                greeting = 'Evening!';
            }

            document.getElementById('greeting').innerText = greeting;
            // document.getElementById('time').innerText = now.toLocaleTimeString();
        }

        setInterval(updateGreeting, 1000);
        updateGreeting();

        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                chart: {
                    type: 'bar'
                },
                series: [{
                    name: 'Time Spent (Hours)',
                    data: <?php echo json_encode(array_values($data)); ?>
                }],
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                yaxis: {
                    title: {
                        text: 'Hours'
                    }
                },
                title: {
                    text: 'Monthly Billable Time',
                    align: 'left'
                }
            };

            var chart = new ApexCharts(document.querySelector("#totalHoursChart"), options);
            chart.render();
        
        });
        
        
        $(document).ready(function() {
            let showGuide = <?php echo isset($_SESSION['first_login']) && $_SESSION['first_login'] ? 'true' : 'false'; ?>;
            
            if (showGuide) {
                var intro = introJs();
                
                intro.setOptions({
                    steps: [
                        {
                            intro: "Welcome to Milatucase! Let's take a quick tour of the main features."
                        },
                        {
                            element: document.querySelector('#customizeBtn'),
                            intro: "Click here to personalize your firm's logo and address for invoices and fee notes."
                        },
                        {
                            element: document.querySelector('a[href="cc/addNewClient"]'),
                            intro: "Add new clients here, both corporate and individual."
                        },
                        {
                            element: document.querySelector('a[href="cc/corporate"]'),
                            intro: "Manage your corporate clients here."
                        },
                        {
                            element: document.querySelector('a[href="cc/individual"]'),
                            intro: "Manage your individual clients here."
                        },
                        {
                            element: document.querySelector('a[href="cases/addNewCase"]'),
                            intro: "Add new legal cases here."
                        },
                        {
                            element: document.querySelector('a[href="cases/all"]'),
                            intro: "View and manage all your cases here."
                        },
                        {
                            element: document.querySelector('a[href="calendar/events"]'),
                            intro: "Manage your calendar and schedule events here."
                        },
                        {
                            element: document.querySelector('a[href="inv/invoices"]'),
                            intro: "Create new invoices here."
                        },
                        {
                            element: document.querySelector('a[href="inv/invoice-list"]'),
                            intro: "View a list of all your invoices here."
                        },
                        {
                            element: document.querySelector('a[href="cases/workflow"]'),
                            intro: "Start timing your work on cases here."
                        },
                        {
                            element: document.querySelector('a[href="cases/workedtime"]'),
                            intro: "View and manage your logged work time here."
                        },
                        
                        {
                            element: document.querySelector('a[href="docs/library"]'),
                            intro: "Access your document library here."
                        },
                        {
                            element: document.querySelector('a[href="docs/feenotes"]'),
                            intro: "Generate and view PDF fee notes here."
                        },
                        {
                            element: document.querySelector('a[href="users"]'),
                            intro: "Manage users and permissions here."
                        },
                        {
                            element: document.querySelector('a[href=""][target="_blank"]'),
                            intro: "Access support resources here."
                        },
                        {
                            intro: "You're all set! Enjoy using Milatucase."
                        }
                    ],
                    exitOnOverlayClick: false,
                    exitOnEsc: false,
                    showStepNumbers: false
                });

                intro.onbeforeexit(function() {
                    $.post('unset_first_login.php');
                });

                intro.start();
            }
        });
    </script>

</body>
</html>