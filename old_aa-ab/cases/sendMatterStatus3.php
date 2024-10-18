<?php

include '../../includes/db.php';
require_once('../../tcpdf/tcpdf.php');
include '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure this session variable is set
if (!isset($_SESSION['parent_id'])) {
    die("Error: Session not set properly.");
}

$lawFirmId = $_SESSION['parent_id'];

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Matter Consolidation');
$pdf->SetSubject('Case Details');
$pdf->SetKeywords('PDF, case, matter');

function cleanHtml($content) {
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $content = strip_tags($content, '<br><p>'); // This will keep <br> and <p> tags
    $content = str_replace(['<p>', '</p>'], ["\n", "\n"], $content); // Replace <p> tags with newlines
    $content = str_replace('<br>', "\n", $content); // Replace <br> with newlines
    $content = trim($content);
    return $content;
}

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
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['logo'] : '../settings/uploads/sampleLogo.png';
}

function createPDF($caseId, $lawFirmId, $clientId, &$pdf) {
    global $connect;

    $companyInfo = fetchCompanyInfo($lawFirmId);
    $logo = fetchLawFirmLogo($lawFirmId);

    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->setAutoPageBreak(TRUE, 10);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();

    // Header with logo
    if (file_exists($logo)) {
        $pdf->Image($logo, 10, 10, 20);
    }
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 15, $companyInfo['company_name'], 0, 1, 'R');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, $companyInfo['address'], 0, 1, 'R');
    $pdf->Cell(0, 5, $companyInfo['postal_code'], 0, 1, 'R');

    $pdf->Ln(10);

    // Matter Consolidation
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Consolidation', 0, 1, 'C');
    $pdf->Ln(5);

    // Fetch case details
    $query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND clientId = ?");
    $query->execute([$caseId, $clientId]);
    $caseDetails = $query->fetch(PDO::FETCH_ASSOC);

    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, "Cause ID: {$caseDetails['causeId']} | Matter / File No: {$caseDetails['caseNo']}", 0, 1);
    
    $description = cleanHtml(decrypt($caseDetails['caseTitle']));

    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Title', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, $description, 0, 'L');

    $pdf->Cell(0, 10, "Dated: " . date("D d, M, Y H:i A", strtotime($caseDetails['created_at'])), 0, 1);

    $pdf->Ln(5);

    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Description', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, cleanHtml(decrypt($caseDetails['caseDescription'])), 0, 'L');

    $pdf->Ln(5);

    // Progress Work
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Progress Work', 0, 1);

    $queryMilestones = $connect->prepare("SELECT * FROM case_milestones WHERE caseId = ?");
    $queryMilestones->execute([$caseId]);
    while ($row = $queryMilestones->fetch(PDO::FETCH_ASSOC)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, "Title: " . cleanHtml(decrypt($row['milestoneTitle'])), 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(0, 10, cleanHtml(decrypt($row['milestoneDescription'])), 0, 'L');
        $pdf->Cell(0, 10, "Added By: " . fetchLawFirmUserName($row['userId'], $lawFirmId), 0, 1);
        $pdf->Cell(0, 10, date("D d M, Y", strtotime($row['created_at'])) . " - " . time_ago_check($row['created_at']), 0, 1);
        $pdf->Ln(5);
    }

    // Attached Documents
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Attached Documents', 0, 1);

    $queryFiles = $connect->prepare("SELECT * FROM caseDocuments WHERE caseId = ?");
    $queryFiles->execute([$caseId]);
    $files = $queryFiles->fetchAll();

    if (count($files) > 0) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(80, 10, 'Document', 1);
        $pdf->Cell(60, 10, 'Added By', 1);
        $pdf->Cell(50, 10, 'Date', 1);
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 12);
        foreach ($files as $row) {
            $pdf->Cell(80, 10, $row['documentName'], 1);
            $pdf->Cell(60, 10, fetchLawFirmUserName($row['userId'], $lawFirmId), 1);
            $pdf->Cell(50, 10, date("d M, Y", strtotime($row['date_added'])), 1);
            $pdf->Ln();
        }
    } else {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'No documents attached', 0, 1);
    }

    $pdf->Ln(5);

    // Matter Status
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Matter Status', 0, 1);

    $queryStatus = $connect->prepare("SELECT DISTINCT(case_status), userId, date_added FROM case_status WHERE caseId = ? ORDER BY date_added ASC");
    $queryStatus->execute([$caseId]);
    while ($row = $queryStatus->fetch(PDO::FETCH_ASSOC)) {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $row['case_status'] . " (" . date("D d, M, Y H:i A", strtotime($row['date_added'])) . ")", 0, 1);
    }

    $pdfFilename = __DIR__ . '/matter_' . $caseId . '.pdf';
    $pdf->Output($pdfFilename, 'F');

    if (!file_exists($pdfFilename)) {
        error_log("Failed to create PDF file: $pdfFilename");
        return false;
    }

    return $pdfFilename;
}

function sendEmailWithAttachment($pdfFilename, $email) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mutamuls@gmail.com';
        $mail->Password = 'mdbm npox ftcj ougf'; // Consider using environment variables for credentials
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        // Recipients
        $mail->setFrom('mutamuls@gmail.com', 'Matter Status');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your PDF Document';
        $mail->Body    = 'Please find attached the PDF document you requested.';
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

    $pdfFilename = createPDF($caseId, $lawFirmId, $clientId, $pdf);
    if ($pdfFilename) {
        if (sendEmailWithAttachment($pdfFilename, $email)) {
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