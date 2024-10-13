<?php 
	include '../../includes/db.php';
	$lawFirmId = $_SESSION['parent_id'];
	$stmt = $connect->prepare("SELECT SUM(amount) as total FROM tableIncome WHERE lawFirmId = ?");
    $stmt->execute([$lawFirmId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = number_format($row['total'], 2);
    echo '	
		<tr>
			<th>Total</th>
			<td></td>
			<th>'.$total.'</th>
			<td></td>
		</tr>
    	';
?>