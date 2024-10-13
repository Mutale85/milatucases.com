<?php
include '../../includes/db.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use setasign\Fpdi\Fpdi;

$lawFirmId = $_SESSION['parent_id'];

function fetchClientInfoByForPdf($clientId) {
    global $connect, $lawFirmId;
    
    try {
        $stmt = $connect->prepare("
            SELECT 
                client_type,
                business_name,
                client_names,
                client_email
            FROM lawFirmClients
            WHERE id = ? AND lawFirmId = ?
        ");
        $stmt->execute([$clientId, $lawFirmId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            if ($data['client_type'] === 'Corporate') {
                return [
                    'name' => $data['business_name'],
                    'email' => decrypt($data['client_email'])
                ];
            } else {
                return [
                    'name' => decrypt($data['client_names']),
                    'email' => decrypt($data['client_email'])
                ];
            }
        } else {
            return ['error' => 'No data found for the provided ID.'];
        }
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function createCompanyHeader($pdf, $lawFirmId) {
    global $connect;
    
    $stmt = $connect->prepare("SELECT * FROM `company_info` WHERE `lawFirmId` = ?");
    $stmt->execute([$lawFirmId]);
    $companyInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$companyInfo) {
        // Use default information if not found
        $companyInfo = [
            'company_name' => 'Default Company Ltd.',
            'address' => '1234 Default Address',
            'postal_code' => '000-000-0000',
            'telephone' => '123-456-7890',
            'email' => 'info@defaultcompany.com',
            'logo' => 'settings/uploads/sampleLogo.png'
        ];
    }

    // Set up the header
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 10, html_entity_decode($companyInfo['company_name']), 0, 1, 'R');
    $pdf->SetFont('Arial', '', 10);

    // Add logo if available
    $logoPath = '../settings/' . $companyInfo['logo'];
    if (!file_exists($logoPath)) {
        $logoPath = 'settings/uploads/sampleLogo.png';
    }
    $pdf->Image($logoPath, 10, 10, 30);

    // Add contact details
    $pdf->SetXY(50, 20);
    $pdf->MultiCell(0, 5, html_entity_decode($companyInfo['address']) . "\n" . 
                         html_entity_decode($companyInfo['postal_code']) . "\n" .
                         "Tel: " . $companyInfo['telephone'] . "\n" .
                         "Email: " . $companyInfo['email'], 0, 'R');

    // Add a line under the header
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Line(10, 50, 200, 50);

    // Reset position for the rest of the document
    $pdf->SetXY(10, 60);

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $emailAddress = $_POST['emailAddress'];
    $disbursementId = $_POST['disbursementId'];
    $clientId = $_POST['clientId'];
    
    $lawFirmId = $_SESSION['parent_id'];



    function sendEmailWithAttachment($clientEmail, $pdfFilename, $clientId, $lawFirmId) {
        $clientNames = getClientNameById($clientId, $lawFirmId);
        $company = fetchCompanyName();
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'support@milatucases.com';
            $mail->Password = 'Javeria##2019';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('support@milatucases.com', "Disbursements - $lawFirmName");
            $mail->addAddress($clientEmail);
            $mail->addAttachment($pdfFilename);
            $lawFirmName = $_SESSION['lawFirmName'];
            $mail->isHTML(true);
            $mail->Subject = "Your disbursement from $lawFirmName";
            
            $mail->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { width: 100%; margin: 0 auto; }
                        .footer { margin-top: 20px; font-size: 12px; color: #888; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>Dear, '.$clientNames.'</h2>
                        <p>We hope this message finds you well.</p>
                        <p>Please find attached the disbursement document for the recent works. If you have any questions or need further assistance, feel free to reach out to us.</p>
                        <p>Thank you for your business!</p>
                        <p>Best regards,</p>
                        
                        <div class="footer">
                            '.$company.'
                        </div>
                    </div>
                </body>
                </html>';
            $mail->send();
            unlink($pdfFilename);
            return "The disbursement has been successfully created and sent to $clientEmail.";

        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function generateAndSendDisbursementPDF($emailAddress, $disbursementId, $clientId, $lawFirmId) {
        global $connect;
        
        $stmt = $connect->prepare("SELECT * FROM disbursements WHERE id = ? AND clientId = ? AND lawFirmId = ?");
        $stmt->execute([$disbursementId, $clientId, $lawFirmId]);
        $disbursement = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$disbursement) {
            return "Disbursement not found.";
        }
        
        $stmt = $connect->prepare("SELECT * FROM disbursement_items WHERE disbursement_id = ?");
        $stmt->execute([$disbursementId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $clientInfo = fetchClientInfoByForPdf($clientId);
        
        $pdf = new FPDI();
        $pdf->AddPage();

        // Add the company header
        createCompanyHeader($pdf, $lawFirmId);

        // Client Information
        $pdf->SetXY(10, 65);  // Adjusted Y position
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'Client Information:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, 'Name: ' . $clientInfo['name'], 0, 1);
        $pdf->Cell(0, 6, 'Email: ' . $clientInfo['email'], 0, 1);

        // Disbursement Details
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Disbursement Details', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, 'Date: ' . $disbursement['disbursement_date'], 0, 1);
        $pdf->Cell(0, 6, 'Total: ' . number_format($disbursement['total'], 2), 0, 1);
        
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Description', 1);
        $pdf->Cell(30, 10, 'Quantity', 1);
        $pdf->Cell(40, 10, 'Price', 1);
        $pdf->Cell(40, 10, 'Total', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 12);
        foreach ($items as $item) {
            $pdf->Cell(80, 10, $item['description'], 1);
            $pdf->Cell(30, 10, $item['quantity'], 1);
            $pdf->Cell(40, 10, '' . number_format($item['price'], 2), 1);
            $pdf->Cell(40, 10, '' . number_format($item['total'], 2), 1);
            $pdf->Ln();
        }
        
        $pdfFilename = 'disbursement_' . $disbursementId . '.pdf';
        $pdf->Output($pdfFilename, 'F');
        
        $emailResult = sendEmailWithAttachment($emailAddress, $pdfFilename, $clientId, $lawFirmId);
        
        return $emailResult;
    }

    $result = generateAndSendDisbursementPDF($emailAddress, $disbursementId, $clientId, $lawFirmId);
    echo $result;
}
?>