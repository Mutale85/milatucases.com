<?php include('../../includes/db.php')?>
<?php require('../base/base.php')?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include '../addon_header.php'?>
        
        <style>
            @media print {
                .print-content th:last-child,
                .print-content td:last-child {
                    display: none;
                }
            }
        </style>

    </head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include '../addon_side_nav.php';?>        
            <div class="layout-page">
                <?php include '../addon_top_nav.php';?>
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
                                                        <th>Category</th>
                                                        <th>Description</th>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Financial Officer'){?>
                                                        <th>Action</th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody id="incomesTable">
                                                    <?php echo fetchChurchIncome($_SESSION['parent_id']) ?>
                                                </tbody>
                                                <tfoot id="incomesTableTotal">
                                                    
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Financial Officer'):?>
                                        <button type="button" id="incomeModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#incomeModal">
                                            Add Income Form
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
                                                                <label class="mb-2" for="budget_id">Income Category</label>
                                                                <select class="form-control" id="budget_id" name="budget_id" required>
                                                                    <!-- Populate this with budget categories from the database -->
                                                                </select>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="mb-2" for="description">Description</label>
                                                                <input type="text" class="form-control" id="description" name="description">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="mb-2" for="amount">Amount</label>
                                                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                                                <input type="hidden" class="form-control" id="income_id" name="income_id">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="mb-2" for="date">Date</label>
                                                                <input type="date" class="form-control" id="date" name="date" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" required>
                                                                    <label class="form-check-label" for="confirmation">
                                                                        Confirm this income is correct
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary" id="submitBtn">Record Income</button>
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
                    <?php include '../addon_footer_link.php'; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer.php'; ?>
    <script type="text/javascript" src="../assets/custom/income.js"></script>

    
    <script>
        
        $(document).ready(function() {
            // Populate the category dropdown
            $.ajax({
                type: 'POST',
                url: 'finances/fetch_categories', // Change this to the actual URL that fetches categories
                data: { church_id: '<?php echo $_SESSION['parent_id']; ?>' }, // Send the church ID as data
                success: function(response) {
                    $('#budget_id').html(response);
                }
            });
        });
    </script>
</body>
</html>
