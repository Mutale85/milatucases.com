<?php 
	include '../../includes/db.php';
	$church_id = $_SESSION['parent_id'];
	$stmt = $connect->prepare("SELECT SUM(amount) as total FROM church_offering WHERE churchId = ?");
    $stmt->execute([$church_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];
    echo '	
		<tr>
			<th>Total</th>
			<td></td>
			<th>'.$total.'</th>
		</tr>
    	';
?>