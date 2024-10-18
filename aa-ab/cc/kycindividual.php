<?php 
	include "../../includes/db.php";
	include '../base/base.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Corporate Clients</title>
	<?php include '../addon_header.php'; 
		if(isset($_GET['cc'])){
			$clientId = base64_decode($_GET['cc']);
			$clientName = lawFirmClientNameById($clientId, $lawFirmId);
		}
	?>
</head>
<body>
	<div class="layout-wrapper layout-content-navbar">
      	<div class="layout-container">
        	<?php include '../addon_side_nav.php'; ?>
        	<div class="layout-page">
          		<?php include '../addon_top_nav.php'; ?>
          		<div class="content-wrapper">
          			<div class="container-xxl flex-grow-1 container-p-y">
					    <div class="row">
					    	<div class="col-md-12">
					    		<ul class="list-group list-group-flush mb-3">
					    			<li class="list-group-item"><a href="./">Home</a> / <a href="cc/individual">Individual</a> / <?php echo $clientName?></li>
					    		</ul>
					    	</div>
					        <div class="col">
					            <div class="card">
					                <div class="card-header d-flex justify-content-between align-items-center">
					                    
					                	<button id="downloadPdf" class="btn btn-dark btn-sm"> <i class="bi bi-file-pdf"></i> Download PDF</button>
					                </div>
					                <div class="card-body" id="kycData">
									    
										<div class="card mb-4 kycData">
										    <div class="card-header border-bottom">
										        <h5 class="card-title">ACCOUNT OWNER(S) HOLDER(S) - INDIVIDUAL</h5>
										    </div>
										    <div class="card-body p-4">
											    <?php 
											        $sql = $connect->prepare("SELECT * FROM `individualPart1` WHERE `lawFirmId` = ? AND `clientId` = ?");
											        $sql->execute([$lawFirmId, $clientId]);
											        
											       	if($sql->rowCount() > 0){
											        	while ($row = $sql->fetch()) {
											       	
											    ?>
											    <div class="form-group table-responsive mb-3">
											        <table class="table">
											            <tr>
											                <td>Name</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['client_name'])); ?></td>
											            </tr>
											            <tr>
											                <td>Marital Status</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['marital_status'])); ?></td>
											            </tr>
											            <tr>
											                <td>Date of Birth</td>
											                <td align="right"><?php echo date("D d M, Y", strtotime($row['date_of_birth'])); ?></td>
											            </tr>
											            <tr>
											                <td>Gender</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['sex'])); ?></td>
											            </tr>
											            <tr>
											                <td>Profession</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['profession'])); ?></td>
											            </tr>
											            <tr>
											                <td>Occupation</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['occupation'])); ?></td>
											            </tr>
											            <tr>
											                <td>Nationality</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['nationality'])); ?></td>
											            </tr>
											            <tr>
											                <td>Identity Type</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['identity_type'])); ?></td>
											            </tr>
											            <tr>
											                <td>Identification Number</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['identification_number'])); ?></td>
											            </tr>
											            <tr>
											                <td>Date of Issue</td>
											                <td align="right"><?php echo htmlspecialchars_decode($row['date_of_issue']); ?></td>
											            </tr>
											            <tr>
											                <td>Place of Issue</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['place_of_issue'])); ?></td>
											            </tr>
											            <tr>
											                <td>Identification Issued By</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['identification_issued_by'])); ?></td>
											            </tr>
											            <tr>
											                <td>TPN No.</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['tpn_no'])); ?></td>
											            </tr>
											            <tr>
											                <td>Physical Address</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['residential_address'])); ?></td>
											            </tr>
											            <tr>
											                <td>Postal Address</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['postal_address'])); ?></td>
											            </tr>
											            <tr>
											                <td>Contact Details</td>
											                <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['contact_details'])); ?></td>
											            </tr>
											        </table>
											    </div>
											    <?php 
											        }
											    }else{
											    	echo "Client has not yet filled in the KYC form.";
											    }
											    ?>
											</div>
										</div>

										<div class="card mb-4 kycData">
										    <div class="card-header border-bottom">
										        <h5 class="card-title">ANTI-MONEY LAUNDERING/TERRORISM/CRIMINAL ACTIVITY QUESTIONNAIRE</h5>
										    </div>
										    <div class="card-body p-4">
										        <?php 
										            $sql = "SELECT * FROM `individualPart2` WHERE lawFirmId = ? AND `clientId` = ?";
													$stmt = $connect->prepare($sql);
													$stmt->execute([$lawFirmId, $clientId]);
													if($stmt->rowCount() > 0){
													    $data = $stmt->fetch(PDO::FETCH_ASSOC);
													    if($data){
													        if(isset($data['signature'])){
													            $signature = $data['signature'];
													        } else {
													            // Handle the case where 'signature' key does not exist
													            $signature = null; // Or set a default value
													        }
													    }
													

										        ?>
										        
										        <table class="table">
										            <tbody>
										                <tr>
										                    <td>Politically Exposed Foreign Person</td>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['politically_exposed_foreign_person'])); ?></td>
										                </tr>
										                <tr>
										                    <td>Potentially Exposed to Money Laundering</td>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['potentially_exposed_to_money_laundering'])); ?></td>
										                </tr>
										                <tr>
										                    <td>Potentially Exposed to Any Terrorist Act</td>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['potentially_exposed_to_any_terrorist_act'])); ?></td>
										                </tr>
										                <tr>
										                    <td>Criminal Activity</td>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['criminal_activity'])); ?></td>
										                </tr>
										                <tr>
										                    <td>Terrorist Association</td>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['terrorist_association'])); ?></td>
										                </tr>
										                <tr>
										                    <td>Terrorist Dealings</td>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['terrorist_dealings'])); ?></td>
										                </tr>
										                
										                <tr>
										                    <td>Signature</td>

										                    <?php 
										                    	if($signature == ""){
										                    		$signed = "--";
										                    	}else{
										                    		$signed = '<img src="'.htmlspecialchars_decode($signature).'" alt="Signature" width="90">';
										                    	}
										                    ?>
										                    <td align="right"><?php echo $signed?></td>
										                </tr>
										                <tr>
										                    <td>Date</td>
										                    <td align="right"><?php echo date("D d M,Y", strtotime($data['signature_date'])); ?></td>
										                </tr>
										                <tr>
										                    <td>Full Names</td>
										                    <td align="right"><?php echo htmlspecialchars_decode($data['signature_names']); ?></td>
										                </tr>
										            </tbody>
										        </table>
										    	<?php 
										    		}else{
										    			echo "Client has not yet filled in Part 2 the KYC form.";
										    		}

										    	?>
										    </div>
										</div>
									</div>
									<div class="card-footer">
										<button type="button" class="btn btn-dark btn-sm" onclick="printDiv('printableDiv')">
					                    	<i class="bi bi-printer"></i> Print KYC
					                	</button>
									</div>
					            </div>
					        </div>
					    </div>
					</div>
          			<?php include '../addon_footer.php';?>

          			<div class="content-backdrop fade"></div>
          		</div>
          	</div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <?php include '../addon_footer_links.php';?>
    <!-- Include jsPDF library -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="../dist/controls/clientDetails.js"></script>	
</body>
</html>