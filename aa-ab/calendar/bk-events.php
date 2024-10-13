<?php include('../../includes/db.php')?>
<?php require('../assets/base.php')?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../addon_header.php';?>
</head>
<body>
    <div class="container-scroller">
        <?php include '../addon_side_nav.php';?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../addon_top_nav.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper pb-0">
                    <div class="page-header flex-wrap">
                        <h3 class="mb-0"> Hi, Mutale! <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block" id="greeting"></span></h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title text-primary">Calendar</h4>
                                </div>
                               
                                <div class="card-body">
                                    <?php include("car.php")?>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Events</h4>
                            <div id="events"></div>
                        </div>
                    </div>
                </div>  
            </div>
            <?php include '../addon_footer_link.php';?>
        </div>
    </div>
    <?php include('../addon_footer.php')?>
    <script src="assets/custom/events.js"></script>
</body>
</html>
