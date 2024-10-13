<?php 
	include "../../includes/db.php";
	if(isset($_POST['clientId'])){
		$clientId = base64_decode($_POST['clientId']);
		$lawFirmId = $_SESSION['parent_id'];
		$query = $connect->prepare("
            SELECT * 
            FROM `cases` 
            WHERE `lawFirmId` = ? AND clientId = ?
        ");
        $query->execute([$lawFirmId, $clientId]);
        
        if($query->rowCount() > 0){?>
        	<div class="table table-responsive">
				<table class="table table-borderless" id="allTables">
					<thead>
						<tr>
							<th>Case No</th>
							<th>Title</th>
							<th>Case Status</th>
							<th>Case Date</th>
							<th>Lawyers</th>
						</tr>
					</thead>
					<tbody>
        <?php
        	$cases = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach ($cases as $case) {
	        	$tpin = htmlspecialchars($case['client_tpin']);
	        	$clientId = $case['clientId'];
	        	$caseNo = $case['caseNo'];
	        	$lawFirmId = $case['lawFirmId'];
	        	$caseTitle = $case['caseTitle'];
	        	$caseId = $case['id'];
	        	$status = $case['caseStatus'];
	        	$date = $case['caseDate'];
	        	if (userHasAccessToCase($_SESSION['user_id'], $caseId, $_SESSION['parent_id'])) {?>
		            <tr>
			            <td><a href="cases/cases-details?caseId=<?php echo base64_encode($caseId)?>&caseNo=<?php echo base64_encode($caseNo)?>&clientId=<?php echo base64_encode($clientId)?>"><?php echo $caseNo?></a>
			            </td>
			            <td><?php echo decrypt($caseTitle) ?></td>
			            
			            <td><?php echo htmlspecialchars($status)?> </td>
			            <td><?php echo date("D d M, Y", strtotime($date)) ?></td>
			            <td><small><?php echo fetchCaseLayersAsFullNames($caseId)?></small></td>
		            </tr>
		    <?php
				}
	        }
	      ?>
		      	</tbody>
		  	</table>
		</div>
	    <?php  
	    }
	}
?>