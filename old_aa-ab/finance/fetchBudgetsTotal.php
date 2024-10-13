<?php 
	include '../../includes/db.php';
	$church_id = $_SESSION['parent_id'];
	$stmt = $connect->prepare("SELECT SUM(amount) as total FROM church_budgets WHERE church_id = ?");
    $stmt->execute([$church_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];
    echo '	
		<tr>
			<th>Total</th>
			<td></td>
			<td></td>
			<th>'.number_format($total, 2).'</th>
			<td></td>
		</tr>
    	';
?>