<?php 
    include "../../includes/db.php";
    include '../base/base.php';

    if(isset($_GET['teamId'])){
        $teamId = base64_decode($_GET['teamId']);
    }

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="../../assets/" data-template="vertical-menu-template-starter">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_SESSION['lawFirmName']?></title>
    <?php include '../addon_header.php'; ?>
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
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><?php echo html_entity_decode(fetchLawFirmUserName($teamId, $lawFirmId) ); ?>'s Dashboard </h5>
                                </div>
                                <div class="card-body">
                                    
                                    <div class="mb-4">
                                        <h6></h6>
                                        <p>Total Milestones: <?php echo countTotalMilestones($teamId) ?> | Total Time Entries: <?php echo countTotalTimeEntries($teamId) ?></p>
                                        <table class="table" id="allTables">
                                            <thead>
                                                <tr>
                                                    <th>Case Title</th>
                                                    <th>Milestones</th>
                                                    <th>Time Entries</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $cases = fetchMembersCases($teamId);
                                                if ($cases && count($cases) > 0) {
                                                    foreach ($cases as $row): ?>
                                                        <tr>
                                                            <td><?php echo html_entity_decode(decrypt($row['caseTitle'])); ?></td>
                                                            <td><?php echo countMilestones($row['id'], $teamId); ?></td>
                                                            <td><?php echo countTimeEntries($row['id'], $teamId); ?></td>
                                                        </tr>
                                                    <?php 
                                                    endforeach; 
                                                } else {
                                                    echo "<tr><td colspan='3'>No cases found.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
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
</body>
</html>