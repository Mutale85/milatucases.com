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
                                        <h4 class="card-title">Tithes Table</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Member</th>
                                                        <th>Added By</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="titheTable">
                                                    <?php echo fetchChurchTithes($_SESSION['parent_id'])?>
                                                </tbody>
                                                <tfoot id="titheTableTotal">
                                                    
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Financial Officer'){?>
                                            <button type="button" id="tithesModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tithesModal">
                                              Add Tithe Form
                                            </button>
                                        <?php }?>

                                        <!-- Large Modal -->
                                        <div class="modal fade" id="tithesModal" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel1">Tithe Form</h5>
                                                    <button
                                                      type="button"
                                                      class="btn-close"
                                                      data-bs-dismiss="modal"
                                                      aria-label="Close"
                                                    ></button>
                                                </div>
                                                <form id="tithesForm" method="POST">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="form-group col-md-12 mb-3">
                                                                <label class="mb-2" for="amount">Amount</label>
                                                                <input type="number" class="form-control" id="amount" name="amount" required>
                                                                <input type="hidden" class="form-control" id="church_id" name="church_id" value="<?php echo $_SESSION['parent_id']; ?>">
                                                                <input type="hidden" class="form-control" id="added_by" name="added_by" value="<?php echo $_SESSION['user_id']; ?>">
                                                            </div>
                                                            <div class="form-group col-md-12 mb-3">
                                                                <label class="mb-2">Paid By</label>
                                                                <select class="form-control" name="member_id" id="member_id">
                                                                    <option value="">Select Member</option>
                                                                    <?php 
                                                                        $sql = $connect->prepare("SELECT * FROM church_members WHERE church_id = ?");
                                                                        $sql->execute([$_SESSION['parent_id']]);
                                                                        foreach($sql->fetchAll() as $row){
                                                                            echo '<option value="'.$row['id'].'">'.$row['firstname'].' '.$row['lastname'].'</option>';
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-12 mb-3">
                                                                <label class="mb-2" for="donation_date">Tithe Date</label>
                                                                <input type="date" class="form-control" id="donation_date" name="donation_date" required>
                                                            </div>
                                                            <div class="form-group col-md-12 mb-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" required>
                                                                    <label class="form-check-label" for="confirmation">
                                                                        I confirm that the tithe being added is true
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary" id="submitBtn">Add Tithe</button>
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
                                        <h4 class="card-title">Tithe Bar</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="titheBarChart"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Tithe Line</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="titheLineChart"></div>
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
    <script type="text/javascript" src="../assets/custom/tithe.js"></script>
    
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
