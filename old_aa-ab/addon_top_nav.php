<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search 
    <form method="get" action="">
      <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
          <i class="bx bx-search fs-4 lh-0"></i>
          <input
            type="text"
            class="form-control border-0 shadow-none"
            placeholder="Search..."
            aria-label="Search..."
            name="search"
          />
        </div>
      </div>
    </form>
    Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User chev-->
      <li class="nav-item me-4" id="customizeBtn" data-intro="Click here to personalize your firm's logo and address for invoices and fee notes" data-step="1">
        <a href="settings/firm" class="customize"><i class="bi bi-gear-wide"></i> Customise</a>
      </li>
      <li class="nav-item navbar-dropdown dropdown-user dropdown me-4">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          Cases <i class="bi bi-sliders2"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          
          <li>
            <a class="dropdown-item" href="cases/all">
              <i class="bi bi-briefcase me-2"></i>
              <span class="align-middle">My Cases</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="cc/corporate">
              <i class="bi bi-building me-2"></i>
              <span class="align-middle">Corporate</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="cc/individual">
              <i class="bi bi-person-bounding-box me-2"></i>
              <span class="align-middle">Individual</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="cases/all-clients-start-timer">
              <i class="bi bi-alarm me-2"></i>
              <span class="align-middle">Start Time</span>
            </a>
          </li>
          
        </ul>
      </li>
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="<?php echo get_gravatar($_SESSION['email']) ?>" alt class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="<?php echo get_gravatar($_SESSION['email']) ?>" alt class="w-px-40 h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?php echo $_SESSION['names']?></span>
                  <small class="text-muted"><?php echo $_SESSION['user_role']?></small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="users">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">Users</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="settings/firm">
              <i class="bi bi-sliders2 me-2"></i>
              <span class="align-middle">Settings</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="billings/subscription">
              <span class="d-flex align-items-center align-middle">
                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                <span class="flex-grow-1 align-middle">Billing</span>
                <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
              </span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="../signout">
              <i class="bx bx-power-off me-2"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>
</nav>