<?php
	include '../../includes/db.php'; 
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$lawFirmId = $_SESSION['parent_id'];
		$today = $_POST['today'];
		$query = $connect->prepare("SELECT * FROM events WHERE start_date = ? AND lawFirmId = ?");
		$query->execute([$today, $lawFirmId]);
		if($query->rowCount() > 0){
			foreach($query->fetchAll() as $row){
				extract($row);
			?>
				<div class="event">
					<div class="alert alert-info">
						<?php echo $title?><br>
						<?php echo html_entity_decode($description) ?><br>
						<em class="text-dark"><i class="bi bi-clock-history"></i> <?php echo date("H:i A", strtotime($start_time))?> - <?php echo date("H:i A", strtotime($end_time))?></em>
					</div>
				</div>
			<?php }
		}else{
			echo '<p class="alert alert-danger">No events today</p>';
		}
	}
?>
