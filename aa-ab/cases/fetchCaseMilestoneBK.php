<?php 
	include "../../includes/db.php";

	if ($_POST['caseId']) {
	    $caseId = decrypt($_POST['caseId']);
	    
	    $query = $connect->prepare("SELECT * FROM case_milestones WHERE caseId = ? AND lawFirmId = ? ORDER BY id DESC ");
	    $query->execute([$caseId, $_SESSION['parent_id']]);
	    if($query->rowCount() > 0){
            $milestones = $query->fetchAll();
		    foreach ($milestones as $index => $row) {
		        $addedBy = fetchLawFirmMemberNames($row['userId']);
		        
		        $date = date("D d M Y", strtotime($row['created_at']));
		        
		        $editButton = '';
		        if ($row['userId'] == $_SESSION['user_id']) {
		            $editButton = '<button class="btn btn-primary btn-sm editMilestoneBtn" data-id="' . $row['id'] . '">Edit</button>';
		        }

                // Determine the alignment for the timeline
                $alignment = ($index % 2 === 0) ? 'left' : 'right';

		        echo '
		            <div class="containerTime ' . $alignment . '">
		                <div class="content">
		                    <h5 class="text-primary">' . htmlspecialchars(decrypt($row['milestoneTitle']), ENT_QUOTES, 'UTF-8') . '</h5>
		                    <p>' . htmlspecialchars_decode(decrypt($row['milestoneDescription']), ENT_QUOTES) . '</p>
		                    <p><i class="bi bi-person"></i>' . htmlspecialchars($addedBy, ENT_QUOTES, 'UTF-8') . ' -  ' . $date . ' <em>( <i class="bi bi-clock"></i> ' . time_ago_check($row['created_at']) . ')</em> </p>
		                    ' . $editButton . '
		                </div>
		            </div>
		        ';
		    }
		}else{
			echo "No milestone added yet";
		}
	}
?>
