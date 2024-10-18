<?php
include '../../includes/db.php';
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use setasign\Fpdi\Fpdi;

$lawFirmId = $_SESSION['parent_id'];

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
        return [
            'error' => 'Database error: ' . htmlspecialchars($e->getMessage())
        ];
    }
}

function fetchLawFirmLogo($lawFirmId) {
    global $connect;
    $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ?");
    $stmt->execute([$lawFirmId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['logo'] : '../settings/uploads/sampleLogo.png';
}

function createPDF($caseId, $lawFirmId, $clientId) {
    global $connect;
    
    $companyInfo = fetchCompanyInfo($lawFirmId);
    $logo = fetchLawFirmLogo($lawFirmId);

    $pdf = new FPDF();
    $pdf->AddPage();

    // Header with logo
    if (file_exists($logo)) {
        $pdf->Image($logo, 10, 10, 30);
    }
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 10, $companyInfo['company_name'], 0, 1, 'R');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $companyInfo['address'], 0, 1, 'R');
    $pdf->Cell(0, 5, $companyInfo['postal_code'], 0, 1, 'R');

    $pdf->Ln(10);

    // Matter Consolidation
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Matter Consolidation', 0, 1, 'C');
    $pdf->Ln(5);

    // Fetch case details
    $query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND clientId = ?");
    $query->execute([$caseId, $clientId]);
    $caseDetails = $query->fetch(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Cause ID: {$caseDetails['causeId']} | Matter / File No: {$caseDetails['caseNo']}", 0, 1);
    $description = decrypt($caseDetails['caseTitle']);
    $decoded_string = htmlspecialchars(html_entity_decode($description, ENT_QUOTES, 'UTF-8'));

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Title', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, $decoded_string, 0, 'L');
    $pdf->Cell(0, 10, "Dated: " . date("D d, M, Y H:i A", strtotime($caseDetails['created_at'])), 0, 1);

    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Description', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, html_entity_decode(decrypt($caseDetails['caseDescription'])), 0, 'L');

    $pdf->Ln(5);

    // Progress Work
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Progress Work', 0, 1);

    $queryMilestones = $connect->prepare("SELECT * FROM case_milestones WHERE caseId = ?");
    $queryMilestones->execute([$caseId]);
    foreach($queryMilestones->fetchAll() as $row) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Title: " . html_entity_decode(decrypt($row['milestoneTitle'])), 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, html_entity_decode(decrypt($row['milestoneDescription'])), 0, 'L');
        $pdf->Cell(0, 10, "Added By: " . fetchLawFirmUserName($row['userId'], $lawFirmId), 0, 1);
        $pdf->Cell(0, 10, date("D d M, Y", strtotime($row['created_at'])) . " - " . time_ago_check($row['created_at']), 0, 1);
        $pdf->Ln(5);
    }

    // Attached Documents
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Attached Documents', 0, 1);

    $queryFiles = $connect->prepare("SELECT * FROM caseDocuments WHERE caseId = ?");
    $queryFiles->execute([$caseId]);
    $files = $queryFiles->fetchAll();

    if (count($files) > 0) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Document', 1);
        $pdf->Cell(60, 10, 'Added By', 1);
        $pdf->Cell(50, 10, 'Date', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        foreach($files as $row) {
            $pdf->Cell(80, 10, html_entity_decode($row['documentName']), 1);
            $pdf->Cell(60, 10, fetchLawFirmUserName($row['userId'], $lawFirmId), 1);
            $pdf->Cell(50, 10, date("d M, Y", strtotime($row['date_added'])), 1);
            $pdf->Ln();
        }
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No documents attached', 0, 1);
    }

    $pdf->Ln(5);

    // Matter Status
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Status', 0, 1);

    $queryStatus = $connect->prepare("SELECT DISTINCT(case_status), userId, date_added FROM case_status WHERE caseId = ? ORDER BY date_added ASC");
    $queryStatus->execute([$caseId]);
    foreach($queryStatus->fetchAll() as $row) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, html_entity_decode($row['case_status']) . " (" . date("D d, M, Y H:i A", strtotime($row['date_added'])) . ")", 0, 1);
    }

    $pdfFilename = 'matter_' . $caseId . '.pdf';
    $pdf->Output($pdfFilename, 'F');

    return $pdfFilename;
}

function sendEmail($pdfFilename, $clientEmail) {
    $company = fetchCompanyName();
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mutamuls@gmail.com';
        $mail->Password = 'mdbm npox ftcj ougf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('mutamuls@gmail.com', 'Legal Matter Management');
        $mail->addAddress($clientEmail);

        $mail->addAttachment($pdfFilename);

        $lawFirmName = $_SESSION['lawFirmName'];

        $mail->isHTML(true);
        $mail->Subject = "Consolidated Matter Status from $lawFirmName";
        
        $mail->Body = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { width: 80%; margin: 0 auto; }
                .footer { margin-top: 20px; font-size: 12px; color: #888; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Dear '.$clientEmail.',</h2>
                <p>We hope this message finds you well.</p>
                <p>Please find attached the consolidated matter status document for your review. If you have any questions or need further clarification, please don\'t hesitate to reach out to us.</p>
                <p>Thank you for your trust in our services.</p>
                <p>Best regards,</p>
                
                <div class="footer">
                    '.$company.'
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
        echo "The consolidated matter status has been successfully created and sent to $clientEmail.";
        
        // Optionally delete the file after sending
        unlink($pdfFilename);
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Main logic
if (isset($_POST['caseId']) && isset($_POST['clientEmail']) && isset($_POST['clientId'])) {
    $caseId = $_POST['caseId'];
    $clientEmail = $_POST['clientEmail'];
    $clientId = $_POST['clientId'];

    $pdfFilename = createPDF($caseId, $lawFirmId, $clientId);
    sendEmail($pdfFilename, $clientEmail);
}
?>