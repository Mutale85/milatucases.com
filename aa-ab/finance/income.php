<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Incomes Table</title>
	<?php include '../addon_header.php'; ?>
	<?php 
		$userCurrency = getUserCurrency();
		$currency = $userCurrency['currency'];
		if (isset($userCurrency['rate'])) {
			$rate = $userCurrency['rate'];
		    // echo "<br>Exchange rate (USD to " . $userCurrency['currency'] . "): " . $userCurrency['rate'];
		}
	?>
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
                            <div class="col-md-12 mb-5">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <h4 class="card-title">Income Table</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] === 'Financial Officer'){?>
                                                        <th>Action</th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody id="incomesTable">
                                                    <?php 
                                                    	echo fetchLawFirmIncome($_SESSION['parent_id']); 
                                                    	
                                                    ?>
                                                </tbody>
                                                <tfoot id="incomesTableTotal">
                                                    
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] === 'Financial Officer'):?>
                                        <button type="button" id="incomeModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#incomeModal">
                                            <i class="bi bi-wallet2"></i> Add Income Form
                                        </button>
                                        <?php endif;?>
                                        <!-- Large Modal -->
                                        <div class="modal fade" id="incomeModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel1">Income Form</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form id="incomeForm" method="POST">
                                                        <div class="modal-body">
                                                            
                                                            <div class="form-group mb-3">
                                                                <label class="mb-2" for="description">Income Description</label>
                                                                <input type="text" class="form-control" id="description" name="description" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="mb-2" for="amount">Amount</label>
                                                                <div class="input-group">
                                                                	<input type="text" name="currency" id="currency" value="<?php echo $currency;?>" class="form-control" readonly>
	                                                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
	                                                                <input type="hidden" class="form-control" id="income_id" name="income_id">
	                                                            </div>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="mb-2" for="date">Income Date</label>
                                                                <input type="date" class="form-control" id="date" name="date" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" required>
                                                                    <label class="form-check-label" for="confirmation">
                                                                        I confirm that this income record is correct
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary" id="submitBtn">Post Income</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Income Bar</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="incomesBarChart"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Income Line</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="incomesLineChart"></div>
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
    <script type="text/javascript" src="../assets/custom/income.js"></script>
</body>
</html>