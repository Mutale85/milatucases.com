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
                                        <h4 class="card-title">Budgets Table</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th>Year</th>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Financial Officer'):?>
                                                        <th>Action</th>
                                                        <?php endif?>
                                                    </tr>
                                                </thead>
                                                <tbody id="budgetTable">
                                                    <?php echo fetchChurchBudgets($_SESSION['parent_id'])?>
                                                </tbody>
                                                <tfoot id="budgetTableTotal">
                                                    
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Financial Officer'){?>
                                        <button type="button" id="budgetsModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#budgetsModal">
                                          Add Budget Form
                                        </button>
                                        <?php }?>
                                        <!-- Large Modal -->
                                        <div class="modal fade" id="budgetsModal" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel1">Budget Form</h5>
                                                    <button
                                                      type="button"
                                                      class="btn-close"
                                                      data-bs-dismiss="modal"
                                                      aria-label="Close"
                                                    ></button>
                                                </div>
                                                <form id="budgetForm" method="POST">
                                                    <div class="modal-body">
                                                        <div class="form-group mb-3">
                                                            <label class="mb-2" for="category">Category</label>
                                                            <select class="form-control" id="category" name="category" required>
                                                                <option value="">Select Category</option>
                                                                <option value="General Fund">General Fund</option>
                                                                <option value="Salaries and Wages">Salaries and Wages</option>
                                                                <option value="Utilities">Utilities</option>
                                                                <option value="Maintenance">Maintenance</option>
                                                                <option value="Events">Events</option>
                                                                <option value="Missions">Missions</option>
                                                                <option value="Education Programs">Education Programs</option>
                                                                <option value="Outreach Programs">Outreach Programs</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="mb-2" for="amount">Amount</label>
                                                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                                            <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['user_id']?>">
                                                            <input type="hidden" name="budget_id" id="budget_id">
                                                            <input type="hidden" name="church_id" id="church_id" value="<?php echo $_SESSION['parent_id']?>">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label class="mb-2" for="year">Year</label>
                                                            <input type="number" class="form-control" id="year" name="year" required value="<?php echo date("Y")?>">
                                                        </div>
                                                        <div class="form-group col-md-12 mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" required>
                                                                <label class="form-check-label" for="confirmation">
                                                                    I confirm that the budget being added is true
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary" id="submitBtn">Create Budget</button>
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
                                        <h4 class="card-title">Categories</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="categoriesBarChart"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Percentages</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="categoriesDonutChart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include '../addon_footer_link.php';?>
                    <div class="content-backdrop fade"></div>
                </div>

            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer.php';?>
    <script type="text/javascript" src="../assets/custom/budget.js"></script>
    
    <script>
        
        function printContent(el) {
            var restorepage = $('body').html();
            var printcontent = $('#' + el).clone();

            // Remove the last th and td (Delete column)
            printcontent.find('th:last-child, td:last-child').remove();

            $('body').empty().html(printcontent);

            // Store current scroll position
            var scrollPos = $(window).scrollTop();

            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    if ($(window).scrollTop() === scrollPos) {
                        location.reload();
                    } else {
                        $('body').html(restorepage);
                    }
                }, 500); 
            }, 100);

            window.onafterprint = function() {
                $('body').html(restorepage);
            };
        }
    </script>
</body>
</html>
