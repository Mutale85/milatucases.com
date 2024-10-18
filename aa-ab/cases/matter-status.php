<div class="row">
  	<div class="col-md-12" id="Consolidation">
		<?php 
			$query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND clientId = ? ");
			$query->execute([$caseId, $clientId]);
			$row = $query->fetch(PDO::FETCH_ASSOC);
			$causeId = $row['causeId'];
			$description = html_entity_decode(decrypt($row['caseDescription']));
			$title = decrypt($row['caseTitle']);
			if($causeId !== NULL){
				$cause = "Cause ID: $causeId |";
			}else{
				$cause = null;
			}
			$createdAt = date("D d, M, Y H:i A", strtotime($row['created_at']));
		?>
  		<div class="card">
  			<div class="card-header">
  				<h5 class="card-title"><?php echo $cause ?> <?php echo "Matter / File No: $caseNo |"; ?> </h5>
  			</div>
  			<div class="card-body">
  				<?php echo " <strong>Matter Title</strong><br> $title <br>Dated: $createdAt"?>
  				<div class="border border-top mt-3 mb-3"></div>
  				<?php echo " <h4 class='text-dark'>Matter Description</h4><br> $description "?>
  				<div class="border border-top mt-3 mb-3"></div>
  				<h5 class="text-primary">Matter Milestones</h5>
  				<?php 
  					$queryMilestones = $connect->prepare("SELECT * FROM case_milestones WHERE caseId = ?  ORDER BY created_at DESC ");
  					$queryMilestones->execute([$caseId]);
  					if($queryMilestones->rowCount() > 0){
  						foreach($queryMilestones->fetchAll() as $row){?>

  							<div class="card-body">
  								<h5>Title: <?php echo html_entity_decode(decrypt($row['milestoneTitle']))?></h5>
  								<p><?php echo html_entity_decode(decrypt($row['milestoneDescription']))?></p>
  								<p><em>Added By: <?php echo fetchLawFirmUserName($userId, $lawFirmId)?></em></p>
  								<em><i class="bi bi-calendar"></i> <?php echo date("D d M, Y", strtotime($row['created_at'])) ?> - <i class="bi bi-clock-history"></i> <?php echo time_ago_check($row['created_at']) ?></em>
  							</div>
  				<?php	}
  					}else{	
  						echo "";
  					}
  				?>

  				<div class="mt-3">
      				<h5 class="text-dark">Matter Status</h5>
      				<?php 
						$queryStatus = $connect->prepare("SELECT DISTINCT(case_status), userId, date_added FROM case_status WHERE caseId = ? ORDER BY date_added ASC");
						$queryStatus->execute([$caseId]);
						if($queryStatus->rowCount() > 0){
						    $prevStatus = null;
						    $rows = $queryStatus->fetchAll();
						    foreach($rows as $row){
						      	$userId = $row['userId'];
						      	$caseStatus = $row['case_status'];
						     	$dateAdded =  date("D d, M, Y H:i A", strtotime($row['date_added']));
						?>
						       <?php echo "<strong>$caseStatus</strong> <i>($dateAdded)</i> <i class='bi bi-arrow-right'></i>" ?>
						            
						<?php
						       
						    }
						} else {
						    echo "";
						}
					?>
				</div>
  			</div>
  			<div class="card-footer">
  				<button class="btn btn-primary btn-sm" data-case-id="<?php echo $caseId?>" data-client-id="<?php echo $clientId?>"  id="generatePdf"><i class="bi bi-file-earmark-pdf"></i> Generate PDF</button>

  				<button class="btn btn-dark btn-sm" data-caseid="<?php echo $caseId?>" id="sendEmail"><i class="bi bi-envelope"></i> Send Email</button>
  			</div>
  		</div>
  	</div>
</div>


<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title" id="emailModalLabel">Send Matter Status</h5>
	        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
	        	</button>
	      	</div>
	      	<form id="sendEmailForm">
	      		<div class="modal-body">
	        		<div class="mb-3">
		            	<label for="recipient-name" class="col-form-label">Select Milestone Period:</label>
		            	<div class="input-group">
		            		<input type="date" name="from" id="from" class="form-control" onchange="setEndDate()" required>
		            		<input type="date" name="end" id="end" class="form-control" required>
		            	</div>
		          	</div>
	          		<div class="form-group">
	            		<label for="emailInput">Email address</label>
	            		<input type="email" class="form-control" name="email" id="emailInput" required>
	          		</div>
	          		<input type="hidden" id="caseId" name="caseId" value="<?php echo $caseId?>">
	          		<input type="hidden" id="clientId" name="clientId" value="<?php echo $clientId?>">
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
		        	<button type="submit" class="btn btn-primary" id="sendEmailBtn">Send</button>
		      	</div>
		    </form>
	    </div>
	</div>
</div>
<!-- End of email modal -->

<!-- Generate PDF Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title" id="pdfModalLabel">Which Period</h5>
	        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      	</div>
	      	<form method="POST" id="createPdfForm">
	      		<div class="modal-body">
		          	<div class="mb-3">
		            	<label for="recipient-name" class="col-form-label">Select Milestone Period:</label>
		            	<div class="input-group">
		            		<input type="date" name="from" id="fromdate" class="form-control" onchange="setEndDate2()" required>
		            		<input type="date" name="end" id="todate" class="form-control" required>
		            	</div>
		          	</div>
		          	<div class="mb-3">
		          		<input type="hidden" name="caseId" value="<?php echo $caseId?>">
			        	<input type="hidden" name="clientId" value="<?php echo $clientId?>">
		          	</div>
			     </div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
	        		<button type="submit" class="btn btn-primary" id="submitPdfBtn">Create PDF</button>
	      		</div>
		    </form>
	    </div>
	</div>
</div>
<!-- End of generate pdf modal -->