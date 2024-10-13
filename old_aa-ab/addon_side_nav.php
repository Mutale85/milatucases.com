<?php 
  if($_SESSION['userJob'] === 'Advocate' || $_SESSION['userJob'] === 'Lawyer' ){
    // show legal dashboard
    include 'addon_nav_legal.php';

  }else if($_SESSION['userJob'] === 'Secretary'){
    include 'addon_nav_admin.php';

  }else if($_SESSION['userJob'] === 'Financial Officer' ) {
    include 'addon_nav_finances.php';
  }
?>