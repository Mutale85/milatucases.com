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
<!-- Icons. Uncomment required icon fonts -->
<link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

<!-- Core CSS -->
<link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
<link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
<link rel="stylesheet" href="../assets/css/demo.css" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

<link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../assets/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="../assets/css/buttons.dataTables.min.css">

<link rel="stylesheet" href="../assets/css/jquery-ui.css">
<!-- Summer Note -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css" />



<!-- Page CSS -->

<!-- Helpers -->
<script src="../assets/vendor/js/helpers.js"></script>

<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="../assets/js/config.js"></script>
<!-- Telephone -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
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

    input[type="text"] {
        width: calc(100%);
        padding: 10px;
        margin-right: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
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
</style>


<?php
    if(!empty($_SESSION['country']) && $_SESSION['country'] === 'Zambia'){
        header("location:../aa-ww");
    }
    
    if(empty($_SESSION['lawFirm_Account'])){
        header("location:../signout");
    }
    $lawFirmId = $_SESSION['parent_id'];
    $userId = $_SESSION['user_id'];
?>