<?php
include '../../includes/db.php';
require_once('../../tcpdf/tcpdf.php');
include '../../vendor/autoload.php';
$lawFirmId = $_SESSION['parent_id'];
$author = $_SESSION['lawFirmName'];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function fetchCompanyInfo($lawFirmId) {
    global $connect;
    try {
        $sql = "SELECT * FROM company_info WHERE lawFirmId = ?";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$lawFirmId]);
        $companyInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$companyInfo) {
            $companyInfo = [
                'company_name' => 'Default Company Ltd.',
                'address' => '1234 Default Address',
                'postal_code' => '000-000-0000'
            ];
        } else {
            foreach ($companyInfo as $key => $value) {
                $companyInfo[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $companyInfo;
    } catch (PDOException $e) {
        error_log("Database error in fetchCompanyInfo: " . $e->getMessage());
        return [
            'error' => 'Database error: ' . htmlspecialchars($e->getMessage())
        ];
    }
}

function fetchLawFirmLogo($lawFirmId) {
    global $connect;
    $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ?");
    $stmt->execute([$lawFirmId]);
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $logoFile = '../settings/'.$row['logo'];
        $fileExtension = pathinfo($logoFile, PATHINFO_EXTENSION);
        return $logoFile;
    }else{
        return '../settings/uploads/sampleLogo.png';
    }
}


class MYPDF extends TCPDF {
    private $isFirstPage = true;

    // Page header
    public function Header() {
        if ($this->isFirstPage) {
            $companyInfo = fetchCompanyInfo($_SESSION['parent_id']);
            $coy = $companyInfo['company_name'];
            $address = htmlspecialchars(html_entity_decode($companyInfo['address'])) . ", " . $companyInfo['postal_code'];
            $phone = $companyInfo['telephone'];
            $email = $companyInfo['email'];

            // Add padding at the top
            $this->SetY(15);

            // Logo
            $image_file = fetchLawFirmLogo($_SESSION['parent_id']);
            $fileExtension = pathinfo($image_file, PATHINFO_EXTENSION);
            switch ($fileExtension) {
                case 'jpg':
                case 'jpeg':
                    $this->Image($image_file, 10, 15, 50, '', 'JPG', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
                case 'png':
                    $this->Image($image_file, 10, 15, 50, '', 'PNG', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
                case 'gif':
                    $this->Image($image_file, 10, 15, 50, '', 'GIF', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
                default:
                    $this->Image('../settings/uploads/sampleLogo.png', 10, 15, 50, '', 'PNG', '', 'T', false, 250, '', false, false, 0, false, false, false);
                    break;
            }

            // Company info
            $this->SetX(70);
            $this->SetFont('helvetica', 'B', 18);
            $this->SetTextColor(51, 51, 51); // Dark grey
            $this->Cell(0, 10, $coy, 0, 1, 'R', 0, '', 0, false, 'M', 'M');

            $this->SetFont('helvetica', '', 12);
            $this->SetTextColor(51, 51, 51); // Dark grey
            $this->SetX(70);
            $this->MultiCell(0, 8, $address, 0, 'R', 0, 1, '', '', true);

            $this->SetX(70);
            $this->MultiCell(0, 8, "Phone: $phone", 0, 'R', 0, 1, '', '', true);

            $this->SetX(70);
            $this->MultiCell(0, 8, "Email: $email", 0, 'R', 0, 1, '', '', true);
        }
        $this->isFirstPage = false;
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}



$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($author);
$pdf->SetTitle('Matter Consolidation');
$pdf->SetSubject('Case Details');
$pdf->SetKeywords('PDF, case, matter');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

function createPDF($caseId, $lawFirmId, $clientId, &$pdf) {
    global $connect;

    $pdf->setAutoPageBreak(TRUE, 10);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();

    
    // Fetch case details
    $query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND clientId = ?");
    $query->execute([$caseId, $clientId]);
    $caseDetails = $query->fetch(PDO::FETCH_ASSOC);
    $matterNo = $caseDetails['caseNo'];
    $pdf->Ln(35); // Move down slightly from the top of the page
    $pdf->SetLineWidth(0.5); // Set the line width
    $pdf->Line(10, $pdf->GetY(), $pdf->GetPageWidth() - 10, $pdf->GetY()); // Draw the line
    $pdf->Ln(25); // Add space below the line

    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, "Matter No: $matterNo Report", 0, 1, 'C');
    $pdf->Ln(15); // Add space below the title


    $pdf->SetFont('helvetica', '', 12);
    if($caseDetails['causeId'] != null || $caseDetails['causeId'] != ""){
        $html1 = "<h4>Cause ID: {$caseDetails['causeId']} | Matter / File No: {$caseDetails['caseNo']}</h4>"; 
    }else{
        $html1 = "<h4> Matter / File No: {$caseDetails['caseNo']}</h4>";
    }

    $caseTitle = html_entity_decode(decrypt($caseDetails['caseTitle']));
    $html2 = "<h3>Matter Title</h3><br>" . $caseTitle;
    $pdf->Ln(3);
    $description = html_entity_decode(decrypt($caseDetails['caseDescription']));
    $html3 = "<h3>Matter Description</h3><br>" . $description;

    $pdf->writeHTMLCell(0, 0, '', '', $html1 . $html2 . $html3, 0, 1, 0, true, '', true);
    $pdf->Ln(5);
    // Progress Work
    
    $queryMilestones = $connect->prepare("SELECT * FROM case_milestones WHERE caseId = ?");
    $queryMilestones->execute([$caseId]);
    if($queryMilestones->rowCount() > 0){
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 12);
        $html = '<h4>Progress Work</h4>';
        $pdf->SetFont('helvetica', '', 12);
        while ($row = $queryMilestones->fetch(PDO::FETCH_ASSOC)) {

            $dates = date("D d M, Y H:i A", strtotime($row['created_at']));
            $html .= "<h4>Title: " . html_entity_decode(decrypt($row['milestoneTitle'])) . "</h4>";
            $html .= "<p>" . html_entity_decode(decrypt($row['milestoneDescription'])) . "</p> <br><span><i>Added By: " . fetchLawFirmUserName($row['userId'], $lawFirmId) . " $dates </i></span>";
            $pdf->Ln(5);
        }
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    }

    

    $queryFiles = $connect->prepare("SELECT * FROM caseDocuments WHERE caseId = ?");
    $queryFiles->execute([$caseId]);
    $files = $queryFiles->fetchAll();

    if (count($files) > 0) {
        // Attached Documents
        $pdf->AddPage();
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $html = '<h4>Attached Documents</h4>';
        $html .= '<table border="0.5" cellspacing="1" cellpadding="4">
                    <tr>
                        <th>Document</th>
                        <th>Added By</th>
                        <th>Date</th>
                    </tr>';
        $pdf->SetFont('helvetica', '', 12);
        foreach ($files as $row) {
            $documentName = $row['documentName'];
            $addedBy = fetchLawFirmUserName($row['userId'], $lawFirmId);
            $dateAdded = date("D d M, Y", strtotime($row['date_added'])) . ", <small>(" . time_ago_check($row['date_added']).")</small>";
            $html .= '
                <tr>
                    <td>' . $documentName . '</td>
                    <td>' . $addedBy . '</td>
                    <td>' . $dateAdded . '</td>
                </tr>
            ';
        }
        $html .= '</table>';
    } else {
        $html .= '<h5>No documents attached</h5>';
    }
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // Matter Status
    $pdf->AddPage();
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $html = '<h4>Matter Status</h4>';

    $queryStatus = $connect->prepare("SELECT DISTINCT(case_status), userId, date_added FROM case_status WHERE caseId = ? ORDER BY date_added ASC");
    $queryStatus->execute([$caseId]);
    $pdf->SetFont('helvetica', '', 12);
    while ($row = $queryStatus->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['case_status'];
        $statusDate = date("D d, M, Y H:i A", strtotime($row['date_added']));
        $html .= "$status ($statusDate) ->";
    }

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    $pdfFilename = __DIR__ . '/matter_' . $caseId . '.pdf';
    $pdf->Output($pdfFilename, 'F');

    if (!file_exists($pdfFilename)) {
        error_log("Failed to create PDF file: $pdfFilename");
        return false;
    }

    return $pdfFilename;
}

function sendEmailWithAttachment($pdfFilename, $email, $caseNo) {
    $company = fetchCompanyName();
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'support@milatucases.com';
        $mail->Password = 'Javeria##2019'; // Consider using environment variables for credentials
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        // Recipients
        $mail->setFrom('support@milatucases.com', "Matter Number: $caseNo");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Matter Report';

        $mail->Body    = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .header { font-size: 18px; font-weight: bold; color: #333; }
                    .content { font-size: 14px; color: #555; }
                    .footer { font-size: 12px; color: #777; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class="header">Dear Valued Recipient,</div>
                <div class="content">
                    <p>Please find the attached report for Matter / File Number: '.$caseNo.' </p>
                    <p>Should you have any questions, feel free to reach out to us. The Firm Details are in the attached PDF</p>
                </div>
                <div class="footer">
                    <p>Best regards,<br>
                    '.$company.'<br>
                    <small>'.date("D d M, Y H:i A").'</small><br>
                    </p>
                </div>
            </body>
            </html>
        ';

        $mail->addAttachment($pdfFilename);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Main execution
if (isset($_POST['caseId']) && isset($_POST['clientId']) && isset($_POST['clientEmail'])) {
    $caseId = $_POST['caseId'];
    $clientId = $_POST['clientId'];
    $email = $_POST['clientEmail'];
    $caseNo = fetchCaseNoById($caseId);

    $pdfFilename = createPDF($caseId, $lawFirmId, $clientId, $pdf);
    if ($pdfFilename) {
        if (sendEmailWithAttachment($pdfFilename, $email, $caseNo)) {
            echo "Email has been sent successfully.";
        } else {
            echo "Failed to send email.";
        }

        // Cleanup
        if (file_exists($pdfFilename)) {
            if (!unlink($pdfFilename)) {
                error_log("Failed to delete temporary file: $pdfFilename");
            }
        }
    } else {
        echo "Failed to create PDF.";
    }
} else {
    echo "Missing parameters.";
}