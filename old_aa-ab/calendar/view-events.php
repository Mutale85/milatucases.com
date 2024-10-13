<?php include('../../includes/db.php')?>
<?php require('../base/base.php')?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include '../addon_header.php'?>
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
                            
                            <div class="col-md-12 col-lg-12 mb-3">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Added Events</h5>
                                    </div>
                                    <div class="card-body">                                        
                                        <div class="table table-responsive">
                                            <table class="table" id="allTables">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Details</th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th>Attendees</th>
                                                        <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Secretary'):?>
                                                        <th>Status</th>
                                                        <th>Checklist</th>
                                                    <?php endif;?>
                                                    </tr>
                                                </thead>
                                                <tbody id='calendarEvents'>
                                                    <?php echo fetchEventDetails();?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="modal fade" id="attendeesModal" tabindex="-1" aria-labelledby="attendeesModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="attendeesModalLabel">Event Attendees</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="attendeesDiv" class="border-bottom pb-3"></div>
                                                        <div id="sendMessageForm" style="display:none;">
                                                            <form id="messageForm">
                                                                <div class="mb-3">
                                                                    <label class="mb-2 mt-3" for="message">Message:</label>
                                                                    <textarea class="form-control" id="message" name="message" rows="2" required></textarea>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary" id="messageBtn">Send Message</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addItemModalLabel">Add Event Checklist Item</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="checklist-form">
                                                        <input type="hidden" name="event_id" id="event-id" value="">
                                                        <div class="form-group">
                                                            <label for="new-item">New Item</label>
                                                            <input type="text" class="form-control" id="new-item" name="item" required>
                                                        </div>
                                                    </form>

                                                    <div id="checkListDiv"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button id="print-btn" onclick="printContent('print-content');" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</button>
                                                    <button type="button" class="btn btn-primary" id="add-item-btn">Add Item</button>
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
    <script type="text/javascript" src="../assets/custom/eventDetails.js"></script>
</body>
</html>