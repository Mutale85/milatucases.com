<?php 
    
    if(empty($_SESSION['lawFirm_Account']) && empty($_SESSION['parent_id'])){
        header("Location:../signout");
        exit;
    }
?>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
<title><?php echo $_SESSION['lawFirmName']?>'s - Milatucases Account</title>
<meta name="description" content="" />
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="../sampleLogo.png" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
<link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
<link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
<link rel="stylesheet" href="../assets/css/demo.css" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

<link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../assets/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="../assets/css/buttons.dataTables.min.css">

<!-- New Data Tables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.8.0/css/searchBuilder.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.3/css/dataTables.dateTime.min.css">

<!-- <link rel="stylesheet" href="../assets/css/jquery-ui.css"> -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">

<!-- Summer Note -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">

<!-- Page CSS -->

<!-- Helpers -->
<script src="../assets/vendor/js/helpers.js"></script>
<script src="../assets/js/config.js"></script>
<!-- Telephone -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- Toasr -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css"/>

<style>
    body {
      color: #000;
      padding-bottom: 60px
    }
    table th {
        text-transform: unset !important;
    }
    table td {
        text-transform: unset !important;
    }
    a:link, a:active {
      text-decoration: none;
    }
    #timer-container {
        margin-bottom: 20px;
    }
    .timer {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .timer-description {
        flex: 1;
    }
    .timer-controls {
        margin-left: 10px;
    }
    .timer-controls button {
        margin-left: 5px;
    }
    .sticky-footer {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 100%;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 10px;
        z-index: 1000;
    }
    .timer-display {
        font-weight: bold;
    }
    .action-buttons {
        display: none;
    }
    .action-buttons.show {
        display: block;
    }
    .bg-menu-theme .ps__thumb-y, .bg-menu-theme .ps__rail-y.ps--clicking > .ps__thumb-y {
        background: #ff3e1d !important;
    }

    .menu .ps__thumb-y, .menu .ps__rail-y {
        width: 0.5rem !important;
    }
</style>

<?php
  
    $lawFirmId = $_SESSION['parent_id'];
    $userId = $_SESSION['user_id'];

    if(!empty($_SESSION['profile_locked'])){
        if($_SESSION['profile_locked'] == true){
            // header("location:billings/subscription");
        }
    }
    $country = $_SESSION['country'];
    $currency = userMainCurrency($country);
    $country = userMainCountry();

?>