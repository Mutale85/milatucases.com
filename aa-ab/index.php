<?php 
	include "../includes/db.php";
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../assets/" data-template="vertical-menu-template-starter" data-style="dark">
  <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_SESSION['lawFirmName']?>'s Dashboard</title>
	 <?php include 'addon_header.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/5.1.0/introjs.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/5.1.0/intro.min.js"></script>
    <?php
      // $lawFirmId = $_SESSION['parent_id'];

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
         * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            grid-gap: 20px;
            padding: 20px;
        }

        .cards {
          background-color: #ffff;
          border-radius: 4px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
          padding: 20px;
          text-align: center;
          text-decoration: none;
          color: inherit;
          border-left: 4px solid transparent;
          transition: border-color 0.3s, transform 0.3s;
        }

        .cards:hover {
          transform: translateY(-5px);
        }

        .cards h3 {
          font-size: 18px;
          margin-bottom: 10px;
        }

        .cards p {
          font-size: 24px;
          font-weight: bold;
        }

        /* Left border colors */
        .cards.new-appointment {
          border-color: #3f51b5;
        }
        .cards.pending-order {
          border-color: #00bcd4;
        }
        .cards.successful-appointment {
          border-color: #4caf50;
        }
        .cards.total-client {
          border-color: #ff9800;
        }
        .cards.total-lawyer {
          border-color: #e91e63;
        }
        .cards.earnings-monthly {
          border-color: #673ab7;
        }
        .cards.earnings-total {
          border-color: #009688;
        }
        .cards.total-subscriber {
          border-color: #f44336;
        }

        @media (max-width: 768px) {
          .dashboard {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          }

          .card h3 {
            font-size: 16px;
          }

          .card p {
            font-size: 20px;
          }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                document.getElementById('time').innerText = now.toLocaleTimeString();
            }
            setInterval(updateGreeting, 1000);
            updateGreeting();
        });

        // $(document).ready(function(){
        //     let showGuide = <?php echo isset($_SESSION['first_login']) && $_SESSION['first_login'] ? 'true' : 'false'; ?>;
            
        //     if (showGuide) {
        //         var intro = introJs();
                
        //         // Initialize intro.js
        //         var intro = introJs();

        //         // Configure the steps for the tour
        //         intro.setOptions({
        //             steps: [
        //                 {
        //                     intro: "Welcome to Milatucase! Let's take a quick tour of the main features."
        //                 },
        //                 {
        //                     element: 'a[href="settings/firm"]',
        //                     intro: "Click here to customize your firm's logo and address for your pdf documents (Very Important)."
        //                 },
        //                 {
        //                     element: 'a[href="cc/addNewClient"]',
        //                     intro: "Add new clients here, both corporate and individual."
        //                 },
        //                 {
        //                     element: 'a[href="cc/all-clients"]',
        //                     intro: "Manage all your clients here."
        //                 },
        //                 {
        //                     element: 'a[href="cases/addNewCase"]',
        //                     intro: "Add new legal matters here."
        //                 },
        //                 {
        //                     element: 'a[href="cases/all"]',
        //                     intro: "View and manage all your matters here."
        //                 },
        //                 {
        //                     element: 'a[href="cases/all-time-entries"]',
        //                     intro: "View and manage your time entries here."
        //                 },
        //                 {
        //                     element: 'a[href="billings/bill"]',
        //                     intro: "Create bills for your clients here."
        //                 },
        //                 {
        //                     element: 'a[href="billings/matternotes"]',
        //                     intro: "Manage your fee notes here."
        //                 },
                        
        //                 {
        //                     element: 'a[href="settings/users"]',
        //                     intro: "Manage users and permissions here."
        //                 },
        //                 {
        //                     element: 'a[href=""][target="_blank"]',
        //                     intro: "Access support resources here."
        //                 },
        //                 {
        //                     intro: "You're all set! Enjoy using Milatucase."
        //                 }
        //             ],
        //             exitOnOverlayClick: false,
        //             exitOnEsc: false,
        //             showStepNumbers: false
        //         });

        //         // Handle tour exit
        //         intro.onbeforeexit(function() {
        //             // Send a POST request to mark the first login as complete
        //             fetch('unset_first_login', {
        //                 method: 'POST',
        //                 credentials: 'same-origin', // Include cookies in the request
        //                 headers: {
        //                     'Content-Type': 'application/x-www-form-urlencoded',
        //                 },
        //             });
        //         });

        //         // Start the tour
        //         intro.start();
        //     }
        // })   

    </script>
</head>
<body>
	<div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<?php include 'addon_side_nav.php'; ?>
        	<div class="layout-page">
          		<?php include 'addon_top_nav.php'; ?>
          		<div class="content-wrapper">
                    <?php
                      include 'incs_body_legal.php';
                      /* 
                        if($_SESSION['user_role'] == 'superAdmin'){
              			       include 'incs_body_legal.php';
                        }else if ($_SESSION['user_role'] == 'Secretary') {
                            include 'incs_body_admin.php';
                        }else if($_SESSION['user_role'] === 'Financial Officer'){
                            include 'incs_body_finance.php';
                        }
                      */
                    ?>
                    <?php  
          			       include 'addon_footer.php';
                    ?>

          			<div class="content-backdrop fade"></div>
          		</div>
          	</div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include 'addon_footer_links.php';?>
</body>
</html>