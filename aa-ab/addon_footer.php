<footer class="content-footer footer bg-footer-theme">
  
  
</footer>
<?php 
  $lawFirmId = $_SESSION['parent_id'];
  $query = $connect->prepare("SELECT * FROM `cases` WHERE `lawFirmId` = ?");
  $query->execute([$lawFirmId]);
  $cases = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="modal fade" id="timeEntryModal" tabindex="-1" aria-labelledby="timeEntryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="timeEntryModalLabel">New Time Entry</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="timeEntryForm">
            <div class="modal-body">
                  <div class="row mb-3">
                      <div class="col-md-12" id="matterSelect">
                          <label for="selectCase" class="form-label mb-2">Select Matter</label>
                          <select id="selectCase" name="caseId" class="form-select" required>
                              <option value="">Choose</option>
                              <option value="Non-Matter-Related">Non Matter Related</option>
                              <?php 
                                foreach ($cases as $row) {
                                  $caseId = $row['id'];
                                  $caseTitle = $row['caseTitle'];
                                  if (userHasAccessToCase($_SESSION['user_id'], $caseId, $lawFirmId)) {
                                      echo '<option value="'.$caseId.'">'. html_entity_decode(decrypt($caseTitle)).'</option>';
                                  }
                                }
                              ?>
                              <!-- Select Matter that user has access to -->

                          </select>
                          <input type="hidden" name="userId" value="<?php echo $_SESSION['user_id']?>">
                          <input type="hidden" name="timerId" id="timerId">
                      </div>
                  </div>
                 
                  <div class="row mb-3">
                      <div class="col-md-6">
                          <label for="date" class="form-label mb-2">Date</label>
                          <input type="date" class="form-control" name="dateCreated" id="date" value="<?php echo date("Y-m-d")?>" required>
                      </div>
                      <div class="col-md-6">
                          <label for="time" class="form-label mb-2">Time</label>
                          <input type="time" class="form-control" name="timeCreated" id="time" value="<?php echo date("H:i A")?>" required>
                      </div>
                  </div>
                  <div class="row mb-3">
                      <div class="col-md-6">
                          <label for="hours" class="form-label mb-2">Hours</label>
                          <input type="number" class="form-control" name="hours" id="hours" min="0" step="any" required>
                      </div>
                      <div class="col-md-6">
                          <label for="minutes" class="form-label mb-2">Minutes</label>
                          <input type="number" class="form-control" name="minutes" id="minutes" min="0" max="59" step="1" required>
                      </div>
                  </div>
                  <div class="row mb-3">
                      <div class="col-md-4">
                        <label for="selectCurrency" class="form-label mb-2">Select Currency</label>
                        <select id="selectCurrency" name="currency" class="form-select" required>
                            <option value="">Choose...</option>
                            <option value="ZMW" selected>Zambia (ZMW)</option>
                            <option value="USD">US Dollar (USD)</option>
                            <option value="GBP">British Pound (GBP)</option>
                            <option value="ZAR">South African Rand (ZAR)</option>
                            <option value="EUR">Euro (EUR)</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                          <label for="hourlyRate" class="form-label mb-2">Hourly Rate </label>
                          <input type="number" class="form-control" name="hourlyRate" id="hourlyRate" min="0" step="0.01" value="0" required>
                      </div>
                      <div class="col-md-4">
                          <label for="cost" class="form-label mb-2">Cost </label>
                          <input type="number" class="form-control" name="cost" id="cost" min="0" step="0.01" required value="0">
                      </div>
                  </div>
                  
                  <div class="mb-3">
                      <label for="description" class="form-label mb-2">Description</label>
                      <textarea class="form-control" name="description" id="description" rows="3" required placeholder="Enter tasks done in the recorded period"></textarea>
                  </div>
                  <div class="row mb-3">
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="billableStatus" id="nonBillable" value="nonBillable" required>
                      <label class="form-check-label" for="nonBillable">Non-Billable</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="billableStatus" id="billable" value="billable" required>
                      <label class="form-check-label" for="billable">Billable</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" id="saveTimeEntry">Save Time Entry</button>
              </div>
          </form>
      </div>
  </div>
</div>

<!-- Timers Modal -->
<div class="modal fade" id="timersModal" tabindex="-1" aria-labelledby="timersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timersModalLabel">Recorded Timers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="timersList" class="list-group mb-3">
                    <!-- Timer list items will be dynamically added here -->
                </ul>
                <button id="addNewTimerBtn" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add New Timer</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var now = new Date();
    document.getElementById('date').value = now.toISOString().split('T')[0];
    document.getElementById('time').value = now.toTimeString().slice(0,5);
});
</script>
