<?php
    include '../../includes/db.php';
    $church_id = $_SESSION['parent_id'];
    echo pettyCash($church_id);
    

?>
