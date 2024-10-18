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
			// $clientId = decryptData($_GET['cc']);
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
					    			<li class="list-group-item"><a href="./">Home</a> / <a href="cc/corporate">Corporate</a> / <?php echo $clientName?></li>
					    		</ul>
					    	</div>
					        <div class="col">
					            <div class="card">
					                <div class="card-header d-flex justify-content-between align-items-center">
					                	<h5 class="card-title">KYC for <?php echo $clientName?></h5>
															                    
					                	<button id="downloadPdf" class="btn btn-dark btn-sm"><i class="bi bi-file-pdf"></i> Download PDF</button>
					                </div>
					                <div class="card-body" id="printableDiv">
									    <?php 
									        $sql = "SELECT * FROM `corporatePart1` WHERE `clientId` = :clientId";
									        $stmt = $connect->prepare($sql);
									        $stmt->execute(['clientId' => $clientId]);
									        $data = $stmt->fetch();

									        if($stmt->rowCount() > 0){
									    ?>

									    <div class="card mb-4 kycData mt-4">
									        <div class="card-header border-bottom">
									            <h5 class="card-title">ACCOUNT OWNER(S) HOLDER(S) - BUSINESS ENTITY</h5>
									        </div>
									        <div class="card-body p-4">
									            <!-- <table class="table table-borderless">
									                <tbody>
									                    <tr>
									                        <th>Business Name</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['client_name']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Date of Incorporation</th>
									                        <td align="right"><?php echo date("D d M, Y", strtotime($data['date_of_incorporation'])); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Place of Incorporation</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['place_of_incorporation']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Business Type</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['business_type']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Tax Identification Number (TPIN)</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['tax_identification_number']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Registered Office Address</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['registered_office_address']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Mailing Address</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['mailing_address']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Contact Person</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['contact_person']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Contact Number</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['contact_number']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Email</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['email']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Name and address of Auditors</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['auditors']); ?></td>
									                    </tr>
									                    <tr>
									                        <th>Financial year end</th>
									                        <td align="right"><?php echo htmlspecialchars_decode($data['financial_year_end']); ?></td>
									                    </tr>
									                </tbody>
									            </table> -->
									            <table class="table table-borderless">
												    <tbody>
												        <tr>
												            <th>Business Name</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['client_name'])); ?></td>
												        </tr>
												        <tr>
												            <th>Date of Incorporation</th>
												            <td align="right"><?php echo date("D d M, Y", strtotime($data['date_of_incorporation'])); ?></td>
												        </tr>
												        <tr>
												            <th>Place of Incorporation</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['place_of_incorporation'])); ?></td>
												        </tr>
												        <tr>
												            <th>Business Type</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['business_type'])); ?></td>
												        </tr>
												        <tr>
												            <th>Tax Identification Number (TPIN)</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['tax_identification_number'])); ?></td>
												        </tr>
												        <tr>
												            <th>Registered Office Address</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['registered_office_address'])); ?></td>
												        </tr>
												        <tr>
												            <th>Mailing Address</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['mailing_address'])); ?></td>
												        </tr>
												        <tr>
												            <th>Contact Person</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['contact_person'])); ?></td>
												        </tr>
												        <tr>
												            <th>Contact Number</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['contact_number'])); ?></td>
												        </tr>
												        <tr>
												            <th>Email</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['email'])); ?></td>
												        </tr>
												        <tr>
												            <th>Name and address of Auditors</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['auditors'])); ?></td>
												        </tr>
												        <tr>
												            <th>Financial year end</th>
												            <td align="right"><?php echo htmlspecialchars_decode(decrypt($data['financial_year_end'])); ?></td>
												        </tr>
												    </tbody>
												</table>
									        </div>
									    </div>

									    <div class="card mb-4 kycData">
										    <div class="card-header border-bottom">
										        <table class="table table-borderless">
										            <tr>
										                <th><h5 class="mb-4">DETAILS OF DIRECTORS/TRUSTEES/SETTLEMENT/BENEFICIARY</h5></th>
										            </tr>
										        </table>
										    </div>
										    <div class="card-body p-4">
										        <div class="form-group table-responsive mb-3">
										            <table id="directors-table" class="table">
										                <thead>
										                    <tr>
										                        <th>Full Name</th>
										                        <th>Gender</th>
										                        <th>M Status</th>
										                        <th>Nationality</th>
										                        <th>Occupation</th>
										                        <th>ID Type & No.</th>
										                        <th>Date & Place of Issue</th>
										                        <th>R. Address</th>
										                        <th>Contact (mobile/email)</th>
										                    </tr>
										                </thead>
										                <tbody>
										                    <?php 
										                        $sql = $connect->prepare("SELECT * FROM `corporatePart2` WHERE `clientId` = :clientId");
										                        $sql->execute(['clientId' => $clientId]);
										                        while ($row = $sql->fetch()) {
										                            echo '<tr>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['full_name'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['gender'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['marital_status'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['nationality'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['occupation'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['identity_type_and_no'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['date_place_of_issue'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['residential_address'])) . '</td>';
										                            echo '<td>' . htmlspecialchars_decode(decrypt($row['contact_details'])) . '</td>';
										                            echo '</tr>';
										                        }
										                    ?>
										                </tbody>
										            </table>
										        </div>
										    </div>
										</div>

										<div class="card mb-4 kycData">
										    <div class="card-header border-bottom">
										        <h5 class="card-title">DETAILS OF PERSON WITH AUTHORITY TO CONDUCT TRANSACTION ON BEHALF OF BUSINESS ENTITY/TRUST</h5>
										    </div>
										    <div class="card-body p-4">
										        <?php 
										            $sql = "SELECT * FROM `corporatePart3` WHERE `clientId` = :clientId";
										            $stmt = $connect->prepare($sql);
										            $stmt->execute(['clientId' => $clientId]);
										            while ($row = $stmt->fetch()) {
										        ?>
										        <div class="form-group table-responsive mb-3">
										            <table class="table">
										                <tr>
										                    <th>Name</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['name'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Marital Status</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['marital_status'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Date of Birth</th>
										                    <td align="right"><?php echo date("D d M, Y", strtotime($row['date_of_birth'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Gender</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['sex'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Profession</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['profession'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Occupation</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['occupation'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Nationality</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['nationality'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Identity Type</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['identity_type'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Identification Number</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['identification_number'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Date of Issue</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['date_of_issue'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Place of Issue</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['place_of_issue'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Identification Issued By</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['identification_issued_by'])); ?></td>
										                </tr>
										                <tr>
										                    <th>TPN No.</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['TPN_no'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Contact Details</th>
										                    <td align="right"><?php echo htmlspecialchars_decode(decrypt($row['contact_details'])); ?></td>
										                </tr>
										            </table>
										        </div>
										        <?php 
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
										            $sql = "SELECT * FROM `corporatePart4` WHERE `clientId` = :clientId";
										            $stmt = $connect->prepare($sql);
										            $stmt->execute(['clientId' => $clientId]); // Assuming you want to fetch the data with businessId = 1
										            $data = $stmt->fetch();
										        ?>
										        
										        <table class="table">
										            <tbody>
										                <tr>
										                    <th>Politically Exposed Foreign Person</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['politically_exposed_foreign_person'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Potentially Exposed to Money Laundering</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['potentially_exposed_to_money_laundering'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Potentially Exposed to Any Terrorist Act</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['potentially_exposed_to_any_terrorist_act'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Criminal Activity</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['criminal_activity'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Terrorist Association</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['terrorist_association'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Terrorist Dealings</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['terrorist_dealings'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Representative's Name</th>
										                    <td align="right"><?php echo ucfirst(htmlspecialchars_decode($data['representative_name'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Signature</th>
										                    <?php 
										                    	if($data['signature'] == ""){
										                    		$signed = "--";
										                    	}else{
										                    		$signed = '<img src="'.htmlspecialchars_decode($data['signature']).'" alt="Signature" width="90">';
										                    	}
										                    ?>
										                    <td align="right"><?php echo $signed?></td>
										                </tr>
										                <tr>
										                    <th>Date</th>
										                    <td align="right"><?php echo date("D d M,Y", strtotime($data['date'])); ?></td>
										                </tr>
										                <tr>
										                    <th>Compliance Officer's Name</th>
										                    <td align="right"><?php echo htmlspecialchars_decode($data['compliance_officer_name']); ?></td>
										                </tr>
										            </tbody>
										        </table>
										    </div>
										</div>

										<?php }else{
												echo "Client has not completed the KYC";
											}
										?>
									</div>
									<div class="card-footer">
										<button type="button" class="btn btn-dark btn-sm" onclick="printDiv('printableDiv')">
					                    	<i class="bi bi-printer"></i> Print KYC
					                	</button>
										<!-- Modal for adding a new Case -->
										
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