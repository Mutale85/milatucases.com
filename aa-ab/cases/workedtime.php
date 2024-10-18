<?php 
  include "../../includes/db.php";
  include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LawFirm Worked Time</title>
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
                          <h5 class="card-title">Recorded Billable Hours</h5>
                        </div>
                        
                        <div class="card-body">
                      
                      <div class="table table-responsive">
                        <table class="table table-striped" id="allTables">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Case No</th>
                                    <th>Total Time</th>
                                    <th>Hourly Rate (ZMW)</th>
                                    <th>Amount (ZMW)</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                  $query = $connect->prepare("
                                  SELECT 
                                      clientId,
                                      case_id,
                                      caseNo,
                                      SUM(total_amount) AS total,
                                      SUM(time_spent) AS total_time,
                                      hourly_rate
                                     
                                  FROM timer_logs
                                  WHERE lawFirmId = ?
                                  GROUP BY clientId, case_id, caseNo, hourly_rate
                              ");
                              $query->execute([$lawFirmId]);
                              $check = $query->rowCount();

                              if ($check > 0): 
                                  $feeNotes = $query->fetchAll(PDO::FETCH_ASSOC);
                                  foreach ($feeNotes as $feeNote): 
                                      $caseId = $feeNote['case_id'];
                                      $clientId = fetchCLientIdCaseId($caseId);
                                      $clientNames = getClientNameById($clientId, $lawFirmId);
                                      $caseNo = $feeNote['caseNo'];
                                      $totalBillableTime = getTotalBillableTime($lawFirmId);
                                                    $totalHours = $totalBillableTime['hours'];
                                                    $totalMinutes = $totalBillableTime['minutes'];
                                                    $time = "<small> {$totalHours} HRS : {$totalMinutes} MIN</small>";
                                      if(userHasAccessToCase($userId, $caseId, $lawFirmId)):
                                         
                                          $amount = TotalFeeNoteAmountByCaseId($feeNote['case_id']);
                                          ?>    
                                          <tr>
                                              <td><a href="crm/?clientId=<?php echo encrypt($clientId)?>"><?php echo htmlspecialchars(html_entity_decode($clientNames)); ?></a></td>
                                              <td><?php echo htmlspecialchars($caseNo); ?></td>
                                              <td><?php echo $time ?></td>
                                              <td><?php echo htmlspecialchars($feeNote['hourly_rate']); ?></td>
                                              <td><?php echo number_format($feeNote['total'], 2); ?></td>
                                              <td><a href="cases/feenote?caseId=<?php echo $caseId?>&caseNo=<?php echo base64_encode($caseNo)?>=&clientId=<?php echo encrypt($clientId)?>">View Feenote</a>
                                              </td>
                                          </tr>
                                      <?php else: ?>
                                          <tr>
                                              <td colspan="5">Not Authorized</td>
                                          </tr>
                                      <?php endif; ?>
                                  <?php endforeach; ?>
                              <?php else: ?>
                                  <tr>
                                      <td colspan="5">No fee notes found.</td>
                                  </tr>
                              <?php endif; ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th colspan="5">Totals</th>
                                
                                <th><?php echo number_format(TotalFeeNoteAmountByLawfirmId($lawFirmId), 2)?></th>
                              </tr>
                            </tfoot>
                        </table>
                    </div>
                  </div>

                        <div class="card-footer">
                          <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="documentModalLabel">Documents</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="showDocuments"></div>
                                </div>
                            </div>
                        </div>
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