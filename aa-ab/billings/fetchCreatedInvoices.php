<?php
require_once '../../includes/db.php'; 
if(isset($_POST['getInvoices'])){
    $lawFirmId = $_SESSION['parent_id'];
    fetchCreatedInvoice($lawFirmId);
}
?>