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
                            <div class="col-md-12 mb-4">
                                <button type="button" id="transactionBtn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transactionModal">
                                    <i class="bi bi-wallet"></i> Add Transaction
                                </button>
                            </div>
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Petty Cash</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table table-responsive">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Description</th>
                                                        <th>Cash Out</th>
                                                        <th>Cash In</th>
                                                        <th>Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="petty_cash">
                                                    <?php 
                                                        echo pettyCash($_SESSION['parent_id']);
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="transactionModalLabel">Add Transaction</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="pettyCashForm" method="POST">
                                        <div class="form-group mb-3">
                                            <label class="mb-1" for="date">Date:</label>
                                            <input type="date" class="form-control" id="transaction_date" name="date" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-1" for="description">Description:</label>
                                            <input type="text" class="form-control" id="description" name="description" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-1" for="amount">Amount:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">ZMW</span>
                                                <input type="number" step="any" class="form-control" id="amount" name="amount" required>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-1" for="transaction_type">Transaction Type:</label>
                                            <select class="form-control" id="transaction_type" name="transaction_type" required>
                                                <option value="">Select</option>
                                                <option value="Cash In">Cash In</option>
                                                <option value="Cash Out">Cash Out</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-1" for="payment_mode">Transaction Mode:</label>
                                            <select class="form-control" id="payment_mode" name="payment_mode" required>
                                                <option value="">Select </option>
                                                <option value="Mobile Money">Mobile Money</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cash">Cash</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label><input type="checkbox" name="correct" id="correct" value="correct" required> Transaction is true</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
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
    <script type="text/javascript" src="../assets/custom/pettycash.js"></script>

    
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
