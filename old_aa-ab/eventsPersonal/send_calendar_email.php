<?php
    include '../../includes/db.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use setasign\Fpdi\Fpdi;

    class PDF extends Fpdi
    {
        protected $companyInfo;
        protected $calendarDates;

        public function setCompanyInfo($companyInfo)
        {
            $this->companyInfo = $companyInfo;
        }

        public function setCalendarDates($start, $end)
        {
            $this->calendarDates = [
                'start' => date("D d M, Y", strtotime($start)),
                'end' => date("D d M, Y", strtotime($end))
            ];
        }

        function Header(){
            $this->SetFillColor(245, 245, 245);
            $this->Rect(0, 0, 210, 40, 'F');

            // Add logo
            $logoPath = '../settings/' . $this->companyInfo['logo'];
            try {
                $this->Image($logoPath, 10, 10, 30);
            } catch (Exception $e) {
                $this->SetFont('Arial', 'B', 12);
                $this->SetXY(10, 10);
                $this->Cell(0, 10, 'Company Logo', 0, 1);
            }

            // Add company info
            $this->SetFont('Arial', 'B', 12);
            $this->SetXY(50, 10);
            $this->Cell(0, 7, $this->companyInfo['company_name'], 0, 1);
            $this->SetFont('Arial', '', 9);
            $this->SetX(50);
            $this->Cell(0, 5, $this->companyInfo['address'], 0, 1);
            $this->SetX(50);
            $this->Cell(0, 5, $this->companyInfo['postal_code'], 0, 1);

            // Add calendar title and date range
            $this->SetFont('Arial', 'B', 16);
            $this->SetXY(110, 10);
            $this->Cell(90, 10, 'Weekly Calendar', 0, 1, 'R');
            $this->SetFont('Arial', '', 10);
            $this->SetXY(110, 20);
            $this->Cell(90, 5, 'From: ' . $this->calendarDates['start'], 0, 1, 'R');
            $this->SetX(110);
            $this->Cell(90, 5, 'To: ' . $this->calendarDates['end'], 0, 1, 'R');

            $this->Ln(20);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $userId = $_POST['userId'];
        $lawFirmId = $_POST['lawFirmId'];

        // Fetch company info and logo
        $companyInfo = fetchCompanyInfo($lawFirmId);
        $companyInfo['logo'] = fetchLawFirmLogo($lawFirmId);

        // Fetch events for the week
        $start_of_week = date("Y-m-d", strtotime('monday this week'));
        $end_of_week = date("Y-m-d", strtotime('sunday next week'));
        
        $query = $connect->prepare("
            SELECT * FROM events_personal 
            WHERE created_by = ? 
              AND start_date BETWEEN ? AND ? 
              AND lawFirmId = ?
        ");
        $query->execute([$userId, $start_of_week, $end_of_week, $lawFirmId]);
        $events = $query->fetchAll();

        // Generate PDF
        $pdf = new PDF();
        $pdf->setCompanyInfo($companyInfo);
        $pdf->setCalendarDates($start_of_week, $end_of_week);
        $pdf->AddPage();

        // Add events table
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(50, 10, 'Title', 1, 0, 'C', true);
        $pdf->Cell(70, 10, 'Description', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Start Date', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'End Date', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 9);

        foreach ($events as $event) {
            $start_date = date('d M, Y', strtotime($event['start_date'])) .' '. date('H:i', strtotime($event['start_time']));
            $end_date = date('d M, Y', strtotime($event['end_date'])) .' '. date('H:i', strtotime($event['end_time']));
            $title = html_entity_decode($event['title']);
            $description = html_entity_decode($event['description']);
            $pdf->Cell(50, 10, $title, 1);
            $pdf->Cell(70, 10, substr($description, 0, 40) . '...', 1);
            $pdf->Cell(35, 10, $start_date, 1);
            $pdf->Cell(35, 10, $end_date, 1);
            $pdf->Ln();
        }

        $pdfFilename = 'weekly_calendar_' . date('Ymd') . '.pdf';
        $pdf->Output($pdfFilename, 'F');

        // Send email with PDF attachment
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
            $mail->setFrom('support@milatucases.com', 'Weekly Calendar');
            $mail->addAddress($email);

            // Attachments
            $mail->addAttachment($pdfFilename);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your Weekly Calendar from " . $companyInfo['company_name'];
            
            // Personalized email body
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
                    <h2>Hello </h2>
                    
                    <p>Please find attached your weekly calendar.</p>
                    <p>Best regards,</p>
                    
                    <div class="footer">
                        ' . $companyInfo['company_name'] . '
                    </div>
                </div>
            </body>
            </html>';

            $mail->send();
            unlink($pdfFilename); // Delete the PDF file after sending
            echo json_encode(['success' => true, 'message' => 'Calendar sent successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

    function fetchCompanyInfo($lawFirmId) {
        global $connect;
        try {
            // Prepare and execute the SQL query to fetch company information
            $sql = "SELECT * FROM company_info WHERE lawFirmId = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$lawFirmId]);
            $companyInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If no company info is found, use default fake company info
            if (!$companyInfo) {
                $companyInfo = [
                    'company_name' => 'Default Company Ltd.',
                    'address' => '1234 Default Address',
                    'postal_code' => '000-000-0000'
                ];
            } else {
                // Decode HTML entities for all fields
                foreach ($companyInfo as $key => $value) {
                    $companyInfo[$key] = html_entity_decode($value);
                }
            }
            
            return $companyInfo;
        } catch (PDOException $e) {
            // Handle database errors
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
?>