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

    <!-- Layouts -->
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Admin</span></li>
  
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
    
    <li class="menu-item">
      <a
        href="docs/library"
        class="menu-link">
        <i class="menu-icon tf-icons bi bi-folder"></i>
        <div data-i18n="Profile">Documents </div>
      </a>
    </li>
        
    <!-- Misc -->
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
    
    <li class="menu-item">
      <a
        href="users"
        class="menu-link">
        <i class="menu-icon tf-icons bi bi-people"></i>
        <div data-i18n="Profile">Users </div>
      </a>
    </li>
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