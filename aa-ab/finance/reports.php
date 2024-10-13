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
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <h4 class="card-title">Groups Table</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Date</th>
                                                        <?php if($_SESSION['user_role'] == 'superAdmin'):?>
                                                            <th>Actions</th>
                                                        <?php endif;?>
                                                    </tr>
                                                </thead>
                                                <tbody id="groupTable">
                                                    <?php echo fetchChurchGroups($_SESSION['parent_id'])?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" id="groupModalBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#groupModal">
                                          Add Group Form
                                        </button>

                                        <!-- Large Modal -->
                                        <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel1">Groups Form</h5>
                                                    <button
                                                      type="button"
                                                      class="btn-close"
                                                      data-bs-dismiss="modal"
                                                      aria-label="Close"
                                                    ></button>
                                                </div>
                                                 <form id="groupForm" method="POST">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="form-group col-md-12 mb-3">
                                                                <label class="mb-2" for="group_name">Group Name</label>
                                                                <input type="text" class="form-control" id="group_name" name="group_name" required>
                                                                <input type="hidden" class="form-control" id="church_id" name="church_id" value="<?php echo $_SESSION['parent_id']; ?>">
                                                                <input type="hidden" name="visitor_id" id="visitor_id" value="">
                                                            </div>
                                                            <div class="form-group col-md-12 mb-3">
                                                                <label class="mb-2" for="group_description">Group Description</label>
                                                                <textarea class="form-control" id="group_description" name="group_description" required></textarea>
                                                            </div>
                                                            <div class="form-group col-md-12 mb-3">
                                                                <label class="mb-2">Select Members</label>
                                                                <div class="checkbox-container">
                                                                    <?php
                                                                    $members = fetchChurchMembersForGroups($_SESSION['parent_id']);
                                                                    foreach ($members as $member) {
                                                                        echo '<div class="form-check">';
                                                                        echo '<input class="form-check-input" type="checkbox" name="member_ids[]" value="' . $member['id'] . '" id="member_' . $member['id'] . '">';
                                                                        echo '<label class="form-check-label" for="member_' . $member['id'] . '">';
                                                                        echo $member['firstname'] . ' ' . $member['lastname'];
                                                                        echo '</label>';
                                                                        echo '</div>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary" id="submitBtn">Create Group</button>
                                                    </div>
                                                </form>

                                            </div>
                                          </div>
                                        </div>
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
    <script type="text/javascript" src="../assets/custom/group.js"></script>
    
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
