<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../includes/db.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

function fetchAndDisplayCompanyInfoForPdf() {
    global $connect;
    $sql = "SELECT * FROM `company_info` WHERE lawFirmId = ? ";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$_SESSION['parent_id']]);
    
    $companyInfo = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $address = html_entity_decode($row['address']);
        $formatted_address = str_replace(["\r\n", "\n", "\r"], '<br>', $address);
        $companyInfo[] = [
            'company_name' => $row['company_name'],
            'address' => $formatted_address,
            'postal_code' => $row['postal_code']
        ];
    }
    return $companyInfo;
}

function fetchLawFirmLogoForPfd($lawFirmId) {
    global $connect;
    $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ? ");
    $stmt->execute([$lawFirmId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $output = $row['logo'];
    } else {
        $output = 'uploads/sampleLogo.png';
    }
    return $output;
}

function saveInvoiceMetaData($invoice_id, $lawFirmId, $clientId, $filePath, $fileSize) {
    global $connect;
    try {
        $stmt = $connect->prepare("INSERT INTO lawFirmInvoices (invoice_id, lawFirmId, clientId, pdfFilePath, fileSize) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$invoice_id, $lawFirmId, $clientId, $filePath, $fileSize]);
    } catch (PDOException $e) {
        die('Error saving PDF metadata: ' . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
    $client_tpin = filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
    $clientEmail = filter_input(INPUT_POST, 'clientEmail', FILTER_SANITIZE_SPECIAL_CHARS);
    $invoice_number = filter_input(INPUT_POST, 'invoice_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
    $due_date = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_SPECIAL_CHARS);
    $tax_rate = filter_input(INPUT_POST, 'tax_rate', FILTER_SANITIZE_SPECIAL_CHARS);
    $subtotal = filter_input(INPUT_POST, 'invoiceSubtotal', FILTER_SANITIZE_SPECIAL_CHARS);
    $tax_type = filter_input(INPUT_POST, 'tax_type', FILTER_SANITIZE_SPECIAL_CHARS);
    $tax = filter_input(INPUT_POST, 'invoiceTax', FILTER_SANITIZE_SPECIAL_CHARS);
    $total = filter_input(INPUT_POST, 'invoiceTotal', FILTER_SANITIZE_SPECIAL_CHARS);
    $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
    $terms = filter_input(INPUT_POST, 'terms', FILTER_SANITIZE_SPECIAL_CHARS);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_SPECIAL_CHARS);
    $items = $_POST['items'];

    // Insert into invoices table
    $stmt = $connect->prepare("INSERT INTO invoices (clientId, client_tpin, clientEmail, invoice_number, date, due_date, tax_type, tax_rate, subtotal, tax, total, lawFirmId, terms, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$clientId, $client_tpin, $clientEmail, $invoice_number, $date, $due_date, $tax_type, $tax_rate, $subtotal, $tax, $total, $lawFirmId, $terms, $notes]);
    $invoice_id = $connect->lastInsertId();

    foreach ($items as $item) {
        $description = filter_var($item['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $quantity = filter_var($item['quantity'], FILTER_SANITIZE_SPECIAL_CHARS);
        $price = filter_var($item['price'], FILTER_SANITIZE_SPECIAL_CHARS);
        $item_total = filter_var($item['total'], FILTER_SANITIZE_SPECIAL_CHARS);
        $stmt = $connect->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$invoice_id, $description, $quantity, $price, $item_total]);
    }

    // Fetch logo and company information
    $logo = fetchLawFirmLogoForPfd($lawFirmId);
    $companyInfo = fetchAndDisplayCompanyInfoForPdf();
    $clientName = getClientNameById($clientId, $lawFirmId);

    // Generate PDF using FPDF and FPDI
    $pdf = new Fpdi();
    $pdf->AddPage();

    // Set default font
    $pdf->SetFont('Arial', '', 12);

    $logoPath = '../settings/' . $logo;

    try {
        $pdf->Image($logoPath, 10, 10, 50);
    } catch (Exception $e) {
        error_log("Error loading logo: " . $e->getMessage());
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(10, 10);
        $pdf->Cell(0, 10, 'Company Logo (Error loading)', 0, 1);
    }

    // Add company information
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(10, 35);
    $companyInfoArray = [];
    foreach ($companyInfo as $row) {
        $companyInfoArray[] = $row['company_name'];
        $address = explode('<br>', $row['address']);
        $companyInfoArray = array_merge($companyInfoArray, array_slice($address, 0, 3)); // Take up to 3 lines of address
        $companyInfoArray[] = $row['postal_code'];
    }
    $pdf->MultiCell(90, 5, implode("\n", $companyInfoArray));

    // Add invoice title and number
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetXY(110, 10);
    $pdf->Cell(90, 10, 'INVOICE', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(110, 20);
    $pdf->Cell(90, 10, 'Invoice #: ' . $invoice_number, 0, 1, 'R');

    // Add invoice details
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(110, 35);
    $pdf->Cell(90, 5, 'Date: ' . $date, 0, 1, 'R');
    $pdf->SetX(110);
    $pdf->Cell(90, 5, 'Due Date: ' . $due_date, 0, 1, 'R');

    // Add a line
    $pdf->Line(10, 65, 200, 65);

    // Add client information
    $pdf->SetXY(10, 70);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 7, 'Bill To:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Client: ' . $clientName, 0, 1);
    $pdf->Cell(0, 5, 'Tpin: ' . $client_tpin, 0, 1);
    $pdf->Cell(0, 5, 'Email: ' . $clientEmail, 0, 1);

    // Add items table
    $pdf->SetY(100);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(90, 8, 'Description', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Quantity', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Price', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Total', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    foreach ($items as $item) {
        $pdf->Cell(90, 7, $item['description'], 1);
        $pdf->Cell(25, 7, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(35, 7, '' . number_format($item['price'], 2), 1, 0, 'R');
        $pdf->Cell(40, 7, '' . number_format($item['total'], 2), 1, 1, 'R');
    }

    // Add totals
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(150, 7, 'Subtotal:', 0, 0, 'R');
    $pdf->Cell(40, 7, '' . number_format($subtotal, 2), 1, 1, 'R');
    $pdf->Cell(150, 7, 'Tax (' . $tax_rate . '% ' . $tax_type . '):', 0, 0, 'R');
    $pdf->Cell(40, 7, '' . number_format($tax, 2), 1, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 8, 'Total: ( ZMW )', 0, 0, 'R');
    $pdf->Cell(40, 8, '' . number_format($total, 2), 1, 1, 'R');
    $terms = html_entity_decode($terms);
    $pdf->SetY(-60);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, 'Terms:', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(100, 5, $terms); // specify width for terms

    // Add Notes
    $notes = html_entity_decode($notes);
    $pdf->SetXY(110, $pdf->GetY() - 5); // set x position for notes
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, 'Notes:', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, $notes);

    $pdfFilePath = 'files/invoice_' . $invoice_number . '.pdf';
    $pdf->Output('F', $pdfFilePath);

    $fileSize = filesize($pdfFilePath);
    saveInvoiceMetaData($invoice_id, $lawFirmId, $clientId,  $pdfFilePath, $fileSize);

    // Send email with PDF attachment using PHPMailer
    $sendNames = $_SESSION['names'];
    $position = $_SESSION['userJob'];
    $contact = $_SESSION['phone'] .'<br>'. $_SESSION['email'];
    $companyName = $_SESSION['lawFirmName'];
    if (isset($_POST['email_invoice']) && !empty($clientEmail)) {

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'support@milatucases.com';
            $mail->Password = 'Javeria##2019';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('support@milatucases.com', 'Invoice');
            $mail->addAddress($clientEmail);

            // Attachments
            $mail->addAttachment($pdfFilePath);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "$companyName - Your Invoice # $invoice_number ";
            $mail->Body = '
                <p>Dear ' . htmlspecialchars($clientName) . ',</p>
                <p>I hope this message finds you well.</p>
                <p>Attached to this email, you will find your detailed invoice for the recent services provided by our firm. The invoice includes a comprehensive breakdown of all charges, ensuring transparency and clarity.</p>
                <p>Please review the attached invoice at your earliest convenience. If you have any questions or require further clarification regarding any of the items listed, do not hesitate to reach out to us. We are here to assist you and ensure that all your concerns are addressed promptly.</p>
                <p>Thank you for your continued trust and business. We look forward to your prompt payment.</p>
                <p>Best regards,</p>
                <p>'.$sendNames.'<br>'.$position.'<br>'.$contact.'<br>'.$companyName.'</p>
            ';

            $trackingId = uniqid('invoice_', true);
            // Create a tracking pixel URL
            $trackingPixelUrl = "https://milatucases.com/track_email?id=" . $trackingId;

            // Add the tracking pixel to the email body
            $mail->Body .= '<img src="' . $trackingPixelUrl . '" width="1" height="1" />';

            // Store the tracking information in the database
            $stmt = $connect->prepare("INSERT INTO email_tracking (tracking_id, invoice_id, client_email, clientId, lawFirmId, sent_date) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$trackingId, $invoice_id, $clientEmail, $clientId, $lawFirmId]);
            $mail->send();
            echo 'Invoice created';
            echo "Invoice has been created and to $clientName emailed successfully";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Invoice created successfully.";
    }
}
?>