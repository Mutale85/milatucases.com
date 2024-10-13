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

	<ul class="menu-inner py-1 ps ps--active-y">
  		<!-- Dashboard -->
    <li class="menu-item active">
      <a href="./" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>
    <!-- Calendar -->

    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bi bi-calendar"></i>
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
        <!-- <li class="menu-item">
          <a href="calendar/time-me?userId=<?php echo $userId?>" class="menu-link">
            <div data-i18n="Without navbar">Time Entries</div>
          </a>
        </li> -->
      </ul>
    </li>
    
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Finances</span>
    </li>
    <li class="menu-item">
      <a href="inv/invoices" class="menu-link">
        <i class="menu-icon tf-icons bx bx-receipt"></i>
        <div>New Invoice</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="inv/invoice-list" class="menu-link">
        <i class="menu-icon tf-icons bi bi-list-stars"></i>
        <div>Invoice List</div>
      </a>
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
      <a href="finance/disbursements" class="menu-link">
        <i class="menu-icon tf-icons bi bi-wallet2"></i>
        <div data-i18n="Notifications">Disbursements</div>
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
        href=""
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
</aside>