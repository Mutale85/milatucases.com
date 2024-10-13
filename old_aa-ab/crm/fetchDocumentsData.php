<?php 
	include "../../includes/db.php";
	if(isset($_POST['clientId'])){
		$clientId = base64_decode($_POST['clientId']);
		$lawFirmId = $_SESSION['parent_id'];
		$sql = $connect->prepare("SELECT * FROM `cases` WHERE `lawFirmId` = ? AND `clientId` = ? ");
		$sql->execute([$lawFirmId, $clientId]);
		try {
		    $sql = $connect->prepare("SELECT * FROM `cases` WHERE `lawFirmId` = ? AND `clientId` = ?");
		    $sql->execute([$lawFirmId, $clientId]);

		    if ($sql->rowCount() > 0) {
		        echo '<div class="list-group">'; 
		        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $row) {
		            extract($row);
		            $folderName = htmlspecialchars($caseNo, ENT_QUOTES, 'UTF-8'); // Prevent XSS attacks
		            $docs = countDocumentsByCaseAndLawFirm($id);
		            if (userHasAccessToCase($userId, $id, $lawFirmId)) {
		                echo '<a href="#" data-case-id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" data-case-no="' . htmlspecialchars($caseNo, ENT_QUOTES, 'UTF-8') . '" class="list-group-item list-group-item-action displayDocument">
		                    <i class="bi bi-folder"></i> ' . $folderName . ' - files (' . htmlspecialchars($docs, ENT_QUOTES, 'UTF-8') . ') - View Files</a>';
		            }
		        }
		        echo '</div>';
		    } else {
		        echo "No case files found";
		    }
		} catch (PDOException $e) {
		    echo "Error: " . $e->getMessage();
		}

				
	}
?>