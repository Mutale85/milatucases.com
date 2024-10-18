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
                <i class="bi bi-archive"></i> + Add Matter
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
                
                <a href="calendar/events-company" class="cards earnings-monthly">
                  <h3>EVENTS</h3>
                  <p><?php echo countUpcomingEvents($lawFirmId)?></p>
                </a>
                
            </div>
        </div>
        
    </div>
</div>