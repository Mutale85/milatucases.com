<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="./" class="app-brand-link">
      <span class="app-brand-logo demo"><img src="../sampleLogo.png" style="width:50px; height: 50px; border-radius: 50%;"></span>
      <span class="app-brand-text demo menu-text fw-bolder ms-2"><small>Milatucases</small></span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bi bi-chevron-double-left bx-sm align-middle"></i>
    </a>
  </div>
  <div class="menu-inner-shadow"></div>
  <?php if($_SESSION['user_role'] == 'superAdmin'): ?>
    <ul class="menu-inner py-1 ps ps--active-y">
      <li class="menu-item active">
        <a href="./" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="settings/firm" class="menu-link">
          <i class="bi bi-gear-wide menu-icon tf-icons"></i> 
          <div data-i18n="Add Client">Customize</div>
        </a>
      </li>
      <!-- Layouts -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">CLIENTS</span></li>
      
      <li class="menu-item">
        <a href="cc/addNewClient" class="menu-link">
          <i class="bi bi-person-plus menu-icon tf-icons"></i> 
          <div data-i18n="Add Client">Add Client</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/corporate" class="menu-link">
          <i class="bi bi-briefcase menu-icon tf-icons"></i> 
          <div data-i18n="Corporate">Corporate</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/individual" class="menu-link">
          <i class="bi bi-person menu-icon tf-icons"></i> 
          <div data-i18n="Individual">Individual</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/all-clients" class="menu-link">
          <i class="bi bi-people menu-icon tf-icons"></i> 
          <div data-i18n="All Client">All Clients</div>
        </a>
      </li>
      <li class="menu-header small text-uppercase"><span class="menu-header-text">MATTERS</span></li> 
      <li class="menu-item" id="casesBtn" data-intro="Click here to manage your legal cases" data-step="4">
        <a href="cases/addNewCase" class="menu-link">
          <i class="menu-icon tf-icons bi bi-bag-plus"></i> <div data-i18n="Add Case">Add Matter</div>
        </a>
      </li>
      <li class="menu-item" id="casesBtn" data-intro="Click here to manage your legal cases" data-step="4">
        <a href="cases/all" class="menu-link">
          <i class="menu-icon tf-icons bi bi-card-list"></i> <div data-i18n="Case List"> Matter List</div>
        </a>
      </li>
      <li class="menu-header small text-uppercase"><span class="menu-header-text">WORKS</span></li>
      
      <!-- Calendar -->
      <li class="menu-item">
        <a
          href="cases/all-time-entries"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-alarm"></i>
          <div data-i18n="Profile">Time Entries</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="billings/bill"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-receipt-cutoff"></i>
          <div data-i18n="Profile">Invoices</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="billings/matternotes" class="menu-link">
          <i class="menu-icon tf-icons bi bi-list-stars"></i>
          <div>Fees Note</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="javascript:void(0);" id="lawyers" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-bookmark-check"></i>
          <div data-i18n="Layouts">Lawyers/Advocates</div>
        </a>
        <?php 
          $userId = $_SESSION['user_id'];
          $userRole = $_SESSION['user_role'];
          $isSuperAdmin = ($userRole === 'superAdmin');

          if ($isSuperAdmin) {
            $query = $connect->prepare("SELECT id, names FROM lawFirms WHERE parentId = ? AND (job = 'Lawyer' OR job = 'Advocate')");
            $query->execute([$lawFirmId]);
          } else {
            $query = $connect->prepare("SELECT id, names FROM lawFirms WHERE id = ? AND parentId = ? AND (job = 'Lawyer' OR job = 'Advocate')");
            $query->execute([$userId, $lawFirmId]);
          }
          $result = $query->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <ul class="menu-sub">
          <?php if (empty($result)): ?>
            <li class="menu-item">
              <a href="#" class="menu-link">
                <div data-i18n="No Lawyers">No Lawyers/Advocates Found</div>
              </a>
            </li>
          <?php else: ?>
            <?php foreach($result as $row): ?>
              <li class="menu-item">
                <a href="team/member?teamId=<?php echo base64_encode($row['id'])?>" class="menu-link">
                  <div data-i18n="Corporate"><?php echo $row['names']?></div>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </li>

      <li class="menu-item">
        <a href="javascript:void(0);" id="calendarA" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-calendar2-check"></i>
          <div data-i18n="Layouts">Calendar</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="calendar/events-company" class="menu-link">
              <div data-i18n="Corporate">Company</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="calendar/event-personal" class="menu-link">
              <div data-i18n="Without navbar">Personal</div>
            </a>
          </li>
        </ul>
      </li>      
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-file-earmark-text"></i>
          <div data-i18n="Layouts">Documents</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="docs/ai-docs" class="menu-link">
              <div data-i18n="Without navbar">AI Generated Docs</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="docs/library" class="menu-link">
              <div data-i18n="Without navbar">Manage Documents</div>
            </a>
          </li>
        </ul>
      </li>
          
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Finances</span>
      </li>
      <li class="menu-item">
        <a href="finance/income" class="menu-link">
          <i class="menu-icon tf-icons bi bi-coin"></i>
          <div data-i18n="Account">Income</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="finance/expenses" class="menu-link">
          <i class="menu-icon tf-icons bi bi-wallet"></i>
          <div data-i18n="Notifications">Expenses</div>
        </a>
      </li>

      <li class="menu-item">
        <a href="finance/petty-cash" class="menu-link">
          <i class="menu-icon tf-icons bi bi-crosshair"></i>
          <div data-i18n="Petty Cash">Petty Cash</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="finance/margins" class="menu-link">
          <i class="menu-icon tf-icons bi bi-bar-chart"></i>
          <div data-i18n="Account">Margins</div>
        </a>
      </li>

      <!-- Misc -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
      
      <li class="menu-item">
        <a
          href="settings/users"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-people"></i>
          <div data-i18n="Profile">Users </div>
        </a>
      </li>
      <li class="menu-item">
        <a href="https://wa.me/260976330092"
          target="_blank"
          class="menu-link">
          <i class="menu-icon tf-icons bx bx-support"></i>
          <div data-i18n="Support">Support</div>
        </a>
      </li>
      
      <li class="menu-item">
        <a
          href="../signout"
          class="menu-link">
          <i class="menu-icon tf-icons bx bx-power-off"></i>
          <div data-i18n="Log out">Log out</div>
        </a>
      </li>
    </ul>
  <?php endif;?>

  <!-- Legal Admin (All Legal Matters, Not allowed on Finances) -->
  <?php if($_SESSION['user_role'] == 'Legal Admin'): ?>
    <ul class="menu-inner py-1 ps ps--active-y">
      <li class="menu-item active">
        <a href="./" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="settings/firm" class="menu-link">
          <i class="bi bi-gear-wide menu-icon tf-icons"></i> 
          <div data-i18n="Add Client">Customize</div>
        </a>
      </li>
      <!-- Layouts -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">CLIENTS</span></li>
      
      <li class="menu-item">
        <a href="cc/addNewClient" class="menu-link">
          <i class="bi bi-person-plus menu-icon tf-icons"></i> 
          <div data-i18n="Add Client">Add Client</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/corporate" class="menu-link">
          <i class="bi bi-briefcase menu-icon tf-icons"></i> 
          <div data-i18n="Corporate">Corporate</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/individual" class="menu-link">
          <i class="bi bi-person menu-icon tf-icons"></i> 
          <div data-i18n="Individual">Individual</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/all-clients" class="menu-link">
          <i class="bi bi-people menu-icon tf-icons"></i> 
          <div data-i18n="All Client">All Clients</div>
        </a>
      </li>
      <li class="menu-header small text-uppercase"><span class="menu-header-text">MATTERS</span></li> 
      <li class="menu-item" id="casesBtn" data-intro="Click here to manage your legal cases" data-step="4">
        <a href="cases/addNewCase" class="menu-link">
          <i class="menu-icon tf-icons bi bi-bag-plus"></i> <div data-i18n="Add Case">Add Matter</div>
        </a>
      </li>
      <li class="menu-item" id="casesBtn" data-intro="Click here to manage your legal cases" data-step="4">
        <a href="cases/all" class="menu-link">
          <i class="menu-icon tf-icons bi bi-card-list"></i> <div data-i18n="Case List"> Matter List</div>
        </a>
      </li>
      <li class="menu-header small text-uppercase"><span class="menu-header-text">WORKS</span></li>
      
      <!-- Calendar -->
      <li class="menu-item">
        <a
          href="cases/all-time-entries"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-alarm"></i>
          <div data-i18n="Profile">Time Entries</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="billings/bill"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-receipt-cutoff"></i>
          <div data-i18n="Profile">Invoices</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="billings/matternotes" class="menu-link">
          <i class="menu-icon tf-icons bi bi-list-stars"></i>
          <div>Fees Note</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="javascript:void(0);" id="lawyers" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-bookmark-check"></i>
          <div data-i18n="Layouts">Lawyers/Advocates</div>
        </a>
        <?php 
          $userId = $_SESSION['user_id'];
          $userRole = $_SESSION['user_role'];
          $isSuperAdmin = ($userRole === 'superAdmin');

          if ($isSuperAdmin) {
            $query = $connect->prepare("SELECT id, names FROM lawFirms WHERE parentId = ? AND (job = 'Lawyer' OR job = 'Advocate')");
            $query->execute([$lawFirmId]);
          } else {
            $query = $connect->prepare("SELECT id, names FROM lawFirms WHERE id = ? AND parentId = ? AND (job = 'Lawyer' OR job = 'Advocate')");
            $query->execute([$userId, $lawFirmId]);
          }
          $result = $query->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <ul class="menu-sub">
          <?php if (empty($result)): ?>
            <li class="menu-item">
              <a href="#" class="menu-link">
                <div data-i18n="No Lawyers">No Lawyers/Advocates Found</div>
              </a>
            </li>
          <?php else: ?>
            <?php foreach($result as $row): ?>
              <li class="menu-item">
                <a href="team/member?teamId=<?php echo base64_encode($row['id'])?>" class="menu-link">
                  <div data-i18n="Corporate"><?php echo $row['names']?></div>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </li>

      <li class="menu-item">
        <a href="javascript:void(0);" id="calendarA" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-calendar2-check"></i>
          <div data-i18n="Layouts">Calendar</div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item">
            <a href="calendar/events-company" class="menu-link">
              <div data-i18n="Corporate">Company</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="calendar/event-personal" class="menu-link">
              <div data-i18n="Without navbar">Personal</div>
            </a>
          </li>
        </ul>
      </li>
      
      
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-file-earmark-text"></i>
          <div data-i18n="Layouts">Documents</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="docs/library" class="menu-link">
              <div data-i18n="Without navbar">Manage Documents</div>
            </a>
          </li>
        </ul>
      </li>
      <!-- Misc -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
      
      <li class="menu-item">
        <a
          href="settings/users"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-people"></i>
          <div data-i18n="Profile">Users </div>
        </a>
      </li>
      <li class="menu-item">
        <a href="https://wa.me/260976330092"
          target="_blank"
          class="menu-link">
          <i class="menu-icon tf-icons bx bx-support"></i>
          <div data-i18n="Support">Support</div>
        </a>
      </li>
      <li class="menu-item">
        <a
          href="../signout"
          class="menu-link">
          <i class="menu-icon tf-icons bx bx-power-off"></i>
          <div data-i18n="Log out">Log out</div>
        </a>
      </li>
    </ul>
  <?php endif;?>

<!-- Financial Officer (All Finance Matters, Not allowed on Legal Matters) -->
  <?php if($_SESSION['user_role'] == 'Financial Officer'): ?>
    <ul class="menu-inner py-1 ps ps--active-y">
      <li class="menu-item active">
        <a href="./" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="settings/firm" class="menu-link">
          <i class="bi bi-gear-wide menu-icon tf-icons"></i> 
          <div data-i18n="Add Client">Customize</div>
        </a>
      </li>
      <!-- Layouts -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">CLIENTS</span></li>
      
      <li class="menu-item">
        <a href="cc/addNewClient" class="menu-link">
          <i class="bi bi-person-plus menu-icon tf-icons"></i> 
          <div data-i18n="Add Client">Add Client</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/corporate" class="menu-link">
          <i class="bi bi-briefcase menu-icon tf-icons"></i> 
          <div data-i18n="Corporate">Corporate</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/individual" class="menu-link">
          <i class="bi bi-person menu-icon tf-icons"></i> 
          <div data-i18n="Individual">Individual</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="cc/all-clients" class="menu-link">
          <i class="bi bi-people menu-icon tf-icons"></i> 
          <div data-i18n="All Client">All Clients</div>
        </a>
      </li>
      <!-- Finances -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">FINANCES</span></li>
      <li class="menu-item">
        <a href="finance/income" class="menu-link">
          <i class="menu-icon tf-icons bi bi-coin"></i>
          <div data-i18n="Account">Income</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="finance/expenses" class="menu-link">
          <i class="menu-icon tf-icons bi bi-wallet"></i>
          <div data-i18n="Notifications">Expenses</div>
        </a>
      </li>

      <li class="menu-item">
        <a href="finance/petty-cash" class="menu-link">
          <i class="menu-icon tf-icons bi bi-crosshair"></i>
          <div data-i18n="Petty Cash">Petty Cash</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="finance/margins" class="menu-link">
          <i class="menu-icon tf-icons bi bi-bar-chart"></i>
          <div data-i18n="Account">Margins</div>
        </a>
      </li>

      <li class="menu-header small text-uppercase"><span class="menu-header-text">WORKS</span></li>
      
      <!-- Calendar -->
      <li class="menu-item">
        <a
          href="cases/all-time-entries"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-alarm"></i>
          <div data-i18n="Profile">Time Entries</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="billings/bill"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-receipt-cutoff"></i>
          <div data-i18n="Profile">Invoices</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="billings/matternotes" class="menu-link">
          <i class="menu-icon tf-icons bi bi-list-stars"></i>
          <div>Fees Note</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="javascript:void(0);" id="calendarA" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bi bi-calendar2-check"></i>
          <div data-i18n="Layouts">Calendar</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="calendar/events-company" class="menu-link">
              <div data-i18n="Corporate">Company</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="calendar/event-personal" class="menu-link">
              <div data-i18n="Without navbar">Personal</div>
            </a>
          </li>
        </ul>
      </li>
      <!-- Misc -->
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
      
      <li class="menu-item">
        <a href="settings/users"
          class="menu-link">
          <i class="menu-icon tf-icons bi bi-people"></i>
          <div data-i18n="Profile">Users </div>
        </a>
      </li>
      <li class="menu-item">
        <a href="https://wa.me/260976330092"
          target="_blank"
          class="menu-link">
          <i class="menu-icon tf-icons bx bx-support"></i>
          <div data-i18n="Support">Support</div>
        </a>
      </li>
      <li class="menu-item">
        <a
          href="../signout"
          class="menu-link">
          <i class="menu-icon tf-icons bx bx-power-off"></i>
          <div data-i18n="Log out">Log out</div>
        </a>
      </li>
    </ul>
  <?php endif;?>

</aside>