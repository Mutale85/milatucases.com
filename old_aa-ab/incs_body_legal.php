<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="table table-responsive">
            <table class="table table-borderless">
                <tr>
                   <th><span id="greeting"></span> <?php echo $_SESSION['names']?></th>
                   <td id="time" align="right"></td>
                </tr>
            </table>
        </div>

        <div class="col-md-12">
            <a href="cases/addNewCase" class="btn btn-dark btn-sm">
                <i class="bi bi-archive"></i> + Add Case
            </a>

            <a href="cc/addNewClient" class="btn btn-primary btn-sm">
                <i class="bi bi-person-bounding-box"></i> + Add Client
            </a>
        </div>
        <div class="col-md-12">
            <div class="dashboard">
                <a href="cases/all" class="cards new-appointment">
                  <h3>MATTERS</h3>
                  <p><?php echo fetchTotalCases($lawFirmId)?></p>
                </a>
                <a href="cc/corporate" class="cards pending-order">
                  <h3>CORPORATE CLIENTS</h3>
                  <p><?php echo fetchTotalCorporateClient($lawFirmId)?></p>
                </a>
                <a href="cc/individual" class="cards successful-appointment">
                  <h3>INDIVIDUAL CLIENTS</h3>
                  <p><?php echo fetchTotalIndividualClients($lawFirmId)?></p>
                </a>
                
                <a href="users" class="cards total-lawyer">
                  <h3>TEAM MEMBERS</h3>
                  <p><?php echo countUsersByFirmId($lawFirmId)?></p>
                </a>
                <a href="calendar/events-company" class="cards earnings-monthly">
                  <h3>UPCOMING EVENTS</h3>
                  <p><?php echo countUpcomingEvents($lawFirmId)?></p>
                </a>
                <a href="cases/workflow" class="cards earnings-total">
                    <?php 
                    $totalBillableTime = getTotalBillableTime($lawFirmId);
                    $totalHours = $totalBillableTime['hours'];
                    $totalMinutes = $totalBillableTime['minutes'];
                    $time = "<small> {$totalHours} HRS : {$totalMinutes} MIN</small>";
                ?>
                  <h3>BILLABLE TIME</h3>
                  <p><?php echo $time?></p>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Recent Activities <small>Last 5 Days</small>
                </div>
                <div class="card-body">
                    <?php
                    $lawFirmData = fetchLawFirmData($lawFirmId);

                    // Display cases
                    if (empty($lawFirmData['cases'])): ?>
                        
                    <?php else: ?>
                        <p class="mt-2">Added Cases</p>
                        <div class="list-group">
                            <?php foreach ($lawFirmData['cases'] as $case): ?>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($case['caseTitle']); ?></h5>
                                        <small><?php echo date('M d, Y', strtotime($case['caseDate'])); ?></small>
                                    </div>
                                    <small>Case No: <?php echo htmlspecialchars($case['caseNo']); ?></small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display clients -->
                    <?php if (empty($lawFirmData['clients'])): ?>
                        
                    <?php else: ?>
                        <p class="mt-2">Added Clients</p>
                        <div class="list-group">
                            <?php foreach ($lawFirmData['clients'] as $client): ?>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo html_entity_decode(decrypt($client['client_names'])); ?></h5>
                                        <small>Client Type: <?php echo htmlspecialchars($client['client_type']); ?> <?php echo date('M d, Y', strtotime($client['created_at']));?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display files -->
                    <?php if (empty($lawFirmData['files'])): ?>
                        
                    <?php else: ?>
                        <div class="list-group">
                            <p class="mt-2">Added File</p>
                            <?php foreach ($lawFirmData['files'] as $file): ?>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($file['file_name']); ?></h5>
                                        <small><?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?></small>
                                    </div>
                                    <p class="mb-1">Uploaded by: <?php echo htmlspecialchars($file['uploaded_by']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display events -->
                    <?php if (empty($lawFirmData['events'])): ?>
                        
                    <?php else: ?>
                        <p class="mt-2"> Added Events</p>
                        <div class="list-group">
                            <?php foreach ($lawFirmData['events'] as $event): ?>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                                        <small><?php echo date('M d, Y', strtotime($event['start_date'])); ?> <?php echo date('H:i A', strtotime($event['start_time'])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($event['description']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Company Hours Graph
                </div>
            </div>
        </div>
    </div>
</div>