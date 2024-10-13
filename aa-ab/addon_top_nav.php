<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!--
    <div id="connection-status">
      <span id="connection-message">No internet connection</span>
    </div>
     Search 
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
      
      <li class="nav-item me-4">
          <div class="d-flex align-items-center">
              <span id="timerClock" class="nav-link me-1">00:00:00</span>
              <button id="startBtn" class="btn btn-primary btn-sm me-2"><i class="bi bi-play-fill"></i></button>
              <button id="pauseBtn" class="btn btn-warning btn-sm me-2" style="display:none;"><i class="bi bi-pause-fill"></i></button>
              <button id="stopBtn" class="btn btn-danger btn-sm me-2" style="display:none;"><i class="bi bi-stop-fill"></i></button>
              <button id="timersBtn" class="btn btn-success btn-sm"><i class="bi bi-plus"></i></button>
          </div>
      </li>
      <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="bx bx-sun"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
          <li>
            <a class="dropdown-item active" href="javascript:void(0);" data-theme="light">
              <span><i class="bx bx-sun me-3"></i>Light</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
              <span><i class="bx bx-moon me-3"></i>Dark</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
              <span><i class="bx bx-desktop me-3"></i>System</span>
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
            <a class="dropdown-item" href="settings/firm">
              <i class="bi bi-gear-wide me-2"></i>
              <span class="align-middle">Firm Settings</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="settings/users">
              <i class="bx bx-user me-2"></i>
              <span class="align-middle">Users</span>
            </a>
          </li>
          <!-- <li>
            <a class="dropdown-item" href="settings/firm">
              <i class="bi bi-sliders2 me-2"></i>
              <span class="align-middle">Customise</span>
            </a>
          </li> -->
          <li>
            <a class="dropdown-item" href="settings/my-profile">
              <i class="bi bi-person-bounding-box me-2"></i>
              <span class="align-middle">Profile</span>
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