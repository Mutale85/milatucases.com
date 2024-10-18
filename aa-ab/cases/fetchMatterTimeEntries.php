<?php 
include '../../includes/db.php';
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $lawFirmId = $_SESSION['parent_id'];
    $userId = $_SESSION['user_id'];     
    $isSuperAdmin = ($_SESSION['user_role'] == 'superAdmin');

    if($isSuperAdmin){
        $stmt = $connect->prepare("SELECT * FROM `time_entries` WHERE lawFirmId = ? AND clientId != '' AND billableStatus = 'billable' ");
        $stmt->execute([$lawFirmId]);
    } else {
        $stmt = $connect->prepare("SELECT * FROM `time_entries` WHERE lawFirmId = ? AND userId = ? AND clientId != '' AND billableStatus = 'billable' ");
        $stmt->execute([$lawFirmId, $userId]);
    }
    
    $timeEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($timeEntries)) {
        // No time entries found
        echo '<tr><td colspan="9" class="text-center">No billable time entries found.</td></tr>';
    } else {
        foreach($timeEntries as $row){
            $staffName = fetchLawFirmMemberNames($row['userId']);
            $matterNo = ($row['caseId'] == 'Non-Matter-Related') ? "Non-Matter-Related" : fetchCaseNumber($row['caseId'], $lawFirmId);
            $clientNames = ($row['caseId'] == 'Non-Matter-Related') ? "---" : clientIdByCaseId($row['caseId']);
            $billableStatus = $row['billableStatus'];
            $hours = ($row['hours'] > 1) ? $row['hours']." hrs " : $row['hours']." hr ";
            $bill_status = ($billableStatus == 'nonBillable') ? 'none' : 'billable';
            $clientId = ($row['clientId'] != "") ? $row['clientId'] : "";
?>
        <tr>
            <td><?php echo $clientNames; ?></td>
            <td><?php echo decrypt($row['description']); ?></td>
            <td><?php echo $matterNo ?></td>
            <td><?php echo $hours . '' . $row['minutes'] . ' Mins '; ?></td>
            <td><?php echo $row['hourlyRate']; ?></td>
            <td><?php echo $row['cost']; ?></td>
            <td><?php echo $staffName ?></td>
            <td><a href="cases/track-timer?clientId=<?php echo $row['clientId']?>&caseId=<?php echo $row['caseId']?>" class="btn btn-outline-dark btn-sm">View Fee Note</a></td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item editTimerLog" href="#" data-id="<?php echo $row['id']; ?>">Edit</a>
                        <a class="dropdown-item deleteTimerLog" href="#" onclick="deleteTimeEntry(<?php echo $row['id']; ?>)">Delete</a>
                    </div>
                </div>
            </td>
        </tr>
<?php
        }
    }
}
?>