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
            <a href="inv/createNewInvoice" class="btn btn-dark btn-sm">
                <i class="bi bi-receipt-cutoff"></i> + Create Invoice
            </a>

            <a href="finance/income" class="btn btn-primary btn-sm">
                <i class="bi bi-money"></i> + Add Income
            </a>
        </div>
        <div class="col-md-12">
            <div class="dashboard">
                <a href="inv/invoice-list" class="cards new-appointment">
                  <h3>Generated Invoices</h3>
                  <span><?php echo fetchTotalInvoices($lawFirmId)?></span>
                </a>
                <a href="finance/income" class="cards pending-order">
                  <h3>Posted Income</h3>
                  <span><?php echo fetchTotalIncome($lawFirmId)?></span>
                </a>
                <a href="finance/expenses" class="cards successful-appointment">
                  <h3>Posted Expenses</h3>
                  <span><?php echo fetchTotalExpenses($lawFirmId)?></span>
                </a>
                
                <!-- <a href="users" class="cards total-lawyer">
                  <h3>TEAM MEMBERS</h3>
                  <p><?php echo countUsersByFirmId($lawFirmId)?></p>
                </a> -->
                <a href="calendar/events-company" class="cards earnings-monthly">
                  <h3>EVENTS</h3>
                  <span><?php echo countUpcomingEvents($lawFirmId)?></span>
                </a>
                <!-- <a href="cases/workflow" class="cards earnings-total">
                    <?php 
                    $totalBillableTime = getTotalBillableTime($lawFirmId);
                    $totalHours = $totalBillableTime['hours'];
                    $totalMinutes = $totalBillableTime['minutes'];
                    $time = "<small> {$totalHours} HRS : {$totalMinutes} MIN</small>";
                ?>
                  <h3>BILLABLE TIME</h3>
                  <p><?php echo $time?></p>
                </a> -->
            </div>
        </div>
        
    </div>
</div>