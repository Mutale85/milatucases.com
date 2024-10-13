<?php 
include '../../includes/db.php';
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $lawFirmId = $_SESSION['parent_id'];
    fetchTimeEntries($lawFirmId);
}
?>